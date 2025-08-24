<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Notifications\EmailverfyNotification;
use App\Traits\Api\AuthTrait as ApiAuthTrait;
use Ichtrojan\Otp\Otp;
use App\Traits\WhatsAppTrait;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SocialRegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// __('custom.')
class AuthController extends Controller
{
    use Notifiable, WhatsAppTrait, ApiAuthTrait;
    private $auth;
    public $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            throw new \Illuminate\Auth\AuthenticationException(__('custom.authentication_failed'));
        }

        $user = auth()->user();

        if ($user->status == '0') {
            throw new \Illuminate\Auth\AuthenticationException(__('custom.user_blocked'));
        }

        $token = $this->createTokenForUser($user);
        $this->updateFcm($user, $request->fcm);

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user),
            'message' => 'Success',
            'status_code' => 200
        ], 200);
    }



    public function logout(Request $request)
    {
        $request->user->tokens()->delete();
        return response()->json(['message' => 'Success', 'status_code' => 200,], 200);
    }


    public function register(RegisterRequest $request)
    {
        try {
            // Create the user
            $user = $this->createUser($request->validated());

            // Fetch the newly created user
            $newUser = User::where('email', $user->email)->first();

            // // Create a token for the user
            // $token = $this->createTokenForUser($newUser);

            // Format the phone number
            $newUser['phone'] = phoneNumberFormat($user->phone);
            $newUser->notify(new EmailverfyNotification());
            // Return the response
            return response()->json([
                // 'token' => $token,
                'user' => new UserResource($newUser),
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            // Handle errors
            return errorResponse($th->getMessage());
        }
    }
    public function socialRegister(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:100'],
                'login_type' => ['required', Rule::in(['google', 'apple', 'normal'])],
                'email' => ['required', 'string', 'email', 'max:255', 'email:rfc', 'indisposable'],
            ]);

            if ($validator->fails()) {
                // يحتوي المصفوفة $errors على قائمة الأخطاء المكتشفة
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $userData = $request->all();
            $request['password'] = 'social-register';
            // Check if the user already exists in your system
            $user = User::where('email', $userData['email'])->first();
            $action = '';
            if (!$user) {
                // User doesn't exist, create a new user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'login_type' => $userData['login_type'],
                    'fcm' => $userData['fcm'] ?? "test",
                    'email_verified_at' => now(),  // Set the email_verified_at field to current timestamp
                    'password' => Hash::make($request['password']),
                ]);
                $user->email_verified_at = now();
                $user->save();
                $action = 'registered';
            } else {

                $credentials = $request->only(['email', 'password']);

                if (!Auth::attempt($credentials)) {
                    return response()->json(
                        [
                            'message' => __('custom.auth_arror'),
                            'status_code' => 409
                        ],
                        409
                    );
                }

                $user = Auth::user();
                $action = 'logged_in';
            }
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'action' => $action, 'user' => $user, 'message' => 'Success', 'status_code' => 200], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed', 'status_code' => 500], 500);
        }
    }
}
