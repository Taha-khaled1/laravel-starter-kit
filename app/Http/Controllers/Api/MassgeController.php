<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Pusher\Pusher;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;
use App\Models\Participant;
use App\Models\Recipient;
use App\Traits\ImageProcessing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MassgeController extends Controller
{
    use ImageProcessing;


    public function index(Request $request)
    {
        $user =  $request->user;
        $chats = $user->chat()
            ->with(['participants', 'lastMessageeWithUser.user'])
            ->get(); // Fetch all chats without pagination chat_id



        $chats->transform(function ($chat) use ($user) {
            $unreadCount = Recipient::where('chat_id', $chat->id)->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();
            // where('chat_id', $chat->id)
            // ->
            $peerUserdata = Participant::where('chat_id', $chat->id)->where('user_id', '!=', $user->id)
                ->with('user')
                ->first();
            return [
                'id' => $chat->id,
                'unread_count' => $unreadCount,
                'status' => $chat->status,
                // 'user' => $chat->user, // user who owns the chat
                'peer_user' =>  [
                    'id' => $peerUserdata->user->id,
                    'name' => $peerUserdata->user->name,
                    'type' => $peerUserdata->user->type,
                    "phone" => $peerUserdata->user->phone,
                    "image" => $peerUserdata->user->image ? asset($peerUserdata->user->image) : null,
                ], // the peer user
                'name' => $chat->name,
                'type' => $chat->type,
                'created_at' => $chat->created_at,
                'updated_at' => $chat->updated_at,
                'last_message' => $chat->lastMessageeWithUser ? [
                    'id' => $chat->lastMessageeWithUser->id,
                    'user' => [
                        'id' => $chat->lastMessageeWithUser->user->id,
                        'name' => $chat->lastMessageeWithUser->user->name,
                        'type' => $chat->lastMessageeWithUser->user->type,
                        "image" =>  $chat->lastMessageeWithUser->user->image ? asset($chat->lastMessageeWithUser->user->image) : null,
                        // 'showroom_status' => $chat->lastMessageeWithUser->user->showroom_status,
                    ],
                    'message' => in_array($chat->lastMessageeWithUser->type, ["image", "file", "voice"]) ? asset($chat->lastMessageeWithUser->message) : $chat->lastMessageeWithUser->message,
                    'type' => $chat->lastMessageeWithUser->type,
                    'duration' => $chat->lastMessageeWithUser->duration,
                    'created_at' => $chat->lastMessageeWithUser->created_at,
                ] : null,
            ];
        });



        return  response()->json(['messages' => "success", 'chats' => $chats, 'status_code' => 200], 200);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|exists:chats,id',
        ]);

        $userId = $request->user->id;
        $chatId = $request->chat_id;
        Recipient::where('chat_id', $chatId)
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return successResponse();
    }

    public function unreadChatsCount(Request $request)
    {
        $userId = $request->user->id;

        $unreadChatsCount = Chat::whereHas('messages', function ($query) use ($userId) {
            $query->whereHas('recipients', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereNull('read_at');
            });
        })->count();

        return successResponse(['unread_chats_count' => $unreadChatsCount]);
    }

    public function show(Request $request)
    {
        try {
            $request->validate([
                'chat_id' => [
                    Rule::requiredIf(function () use ($request) {
                        return !$request->input('peer_id');
                    }),
                    'int',
                    'exists:chats,id',
                ],
                'peer_id' => [
                    Rule::requiredIf(function () use ($request) {
                        return !$request->input('chat_id');
                    }),
                    'int',
                    'exists:users,id',
                ],
            ]);

            $myuser = $request->user;

            if ($request->filled('chat_id')) {
                $chat = $myuser->chat()->findOrFail($request->chat_id);
            } else {
                $peerUser = User::findOrFail($request->peer_id);
                $chat = Chat::where('type', 'peer')
                    ->whereIn('id', function ($query) use ($myuser, $peerUser) {
                        $query->select('chat_id')
                            ->from('participants')
                            ->whereIn('user_id', [$myuser->id, $peerUser->id])
                            ->groupBy('chat_id')
                            ->havingRaw('COUNT(DISTINCT user_id) = 2');
                    })
                    ->first();
                // $chat = Chat::where('type', 'peer')
                //     ->whereIn('id', function ($query) use ($myuser, $peerUser) {
                //         $query->select('chat_id')
                //             ->from('participants')
                //             ->groupBy('chat_id')
                //             ->havingRaw(
                //                 'COUNT(DISTINCT user_id) = 2 AND GROUP_CONCAT(DISTINCT user_id ORDER BY user_id) = ?',
                //                 [implode(',', [$myuser->id, $peerUser->id])]
                //             );
                //     })
                //     ->first();
                if (!$chat) {
                    return response()->json([
                        'messages' => [
                            "message" => "chat not found",
                            "data" => [],
                        ],
                        "job" =>  null
                    ], 200);
                }
            }

            $messages = $chat->messages()->with('user:id,name,type,email,phone,image')->paginate(30);

            // Transform messages to include proper asset URLs for file-based messages
            $messages->getCollection()->transform(function ($message) {
                if (in_array($message->type, ['image', 'file', 'voice'])) {
                    $message->message = asset($message->message);
                }
                return $message;
            });

            return response()->json([
                'messages' => $messages,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'messages' => [
                    "message" => $th->getMessage(),
                    "data" => [],
                ],
                "job" =>  null
            ], 200);
        }
    }

    public function sendMessage(Request $request)
    {
        DB::beginTransaction();
        try {

            $request->validate([
                'message' => [
                    Rule::requiredIf(function () use ($request) {
                        return in_array($request->type, ['text', 'location']);
                    }),
                    'nullable'
                ],
                'chat_id' => [
                    Rule::requiredIf(function () use ($request) {
                        return !$request->input('user_id');
                    }),
                    'int',
                    'exists:chats,id',
                ],
                'user_id' => [
                    Rule::requiredIf(function () use ($request) {
                        return !$request->input('chat_id');
                    }),
                    'int',
                    'exists:users,id',
                ],
                'type' => ['required', 'in:text,image,file,location,voice'],
                'duration' => 'nullable|integer|min:1|max:300' // Duration in seconds, max 5 minutes
            ]);

            $userId = $request->user_id; // peer id
            $chatId = $request->chat_id;
            $user =  $request->user;    // mt id
            if ($chatId) {
                $conversation = $user->chat()->findOrFail($chatId);
            } else {
                $conversation = Chat::where('type', '=', 'peer')
                    ->whereHas('participants', function ($builder) use ($userId, $user) {
                        $builder->join('participants as participants2', 'participants2.chat_id', '=', 'participants.chat_id')
                            ->where('participants.user_id', '=', $userId)->where('participants2.user_id', '=', $user->id);
                    })->first();

                if (!$conversation) {
                    $conversation = Chat::create([
                        'user_id' => $user->id,
                        'type' => 'peer',

                    ]);

                    $conversation->participants()->attach([
                        $user->id,
                        $userId,
                        // $user->id => ["joined_at" => now()],
                        // $userId => ["joined_at" => now()],
                    ]);
                }
            }

            if ($request->type == "location" || $request->type == "text") {
                $message = $conversation->messages()->create([
                    'user_id' => $user->id,
                    'message' => $request->message,
                    'type' => $request->type,
                    // 'latitude' => $request->latitude,
                    // 'longitude' => $request->longitude,
                ]);
            } elseif ($request->type == "image") {
                $mainImagePath =  $this->saveImage($request->file('message'), 'product');
                $message = $conversation->messages()->create([
                    'user_id' => $user->id,
                    'message' => $mainImagePath,
                    'type' => $request->type,
                ]);
            } elseif ($request->type == "file") {
                $mainImagePath =  $this->saveMedia($request->file('message'), 'product');
                $message = $conversation->messages()->create([
                    'user_id' => $user->id,
                    'message' => $mainImagePath,
                    'type' => $request->type,
                ]);
            } elseif ($request->type == "voice") {
                $voicePath = $this->saveVoiceMessage($request->file('message'), 'voices');
                $message = $conversation->messages()->create([
                    'user_id' => $user->id,
                    'message' => $voicePath,
                    'type' => $request->type,
                    'duration' => $request->duration,
                ]);
            } else {
                $message = $conversation->messages()->create([
                    'user_id' => $user->id,
                    'message' => $request->message,
                ]);
            }


            DB::statement('
            INSERT INTO recipients (user_id, message_id, chat_id, read_at)
            SELECT user_id, ?, ?, CASE WHEN user_id = ? THEN ? ELSE NULL END
            FROM participants
            WHERE chat_id = ?
        ', [$message->id, $conversation->id, $user->id, Carbon::now(), $conversation->id]);
            $conversation->update([
                "last_message_id" => $message->id,
            ]);



            $finalMessage = $message->with('user:id,name,type,email,phone,image')->latest()->first();
            $pusher = new Pusher(
                '404bce2023768d543d44',
                'e82489cc05de9123c229',
                1878473,
                [
                    'cluster' => 'mt1',
                    'useTLS' => true
                ],
            );
            // broadcast(new ChatMessageEvent($finalMessage))->toOthers();
            // event(new ChatMessageEvent($finalMessage));

            $pusher->trigger('chat.' . $finalMessage->chat_id, 'ChatMessageEvent', $finalMessage);
            DB::commit();
            return successResponse($finalMessage, 200, "Message sent successfully");
        } catch (\Throwable $th) {
            DB::rollback();
            return errorResponse("Message not sent" . $th->getMessage(), 500);
        }
    }

    public function deleteChat(Request $request)
    {
        try {
            $request->validate([
                'chat_id' => 'required|exists:chats,id',
            ]);
            $userId = $request->user->id;
            $chatId = $request->chat_id;
            Participant::where('chat_id', $chatId)->where('user_id', $userId)->delete();
            Recipient::where('chat_id', $chatId)->where('user_id', $userId)->delete();
            // 
            Message::where('chat_id', $chatId)->delete();
            Chat::where('id', $chatId)->delete();

            return successResponse();
        } catch (\Throwable $th) {
            return successResponse();
        }
    }
}
