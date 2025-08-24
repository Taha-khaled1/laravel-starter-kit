<?php

namespace App\Traits\Api;

use App\Jobs\SendVerificationEmailJob;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Notifications\EmailverfyNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

trait AuthTrait
{
    // Validate email address before using it
    public function validateEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid email address',
            ]);
        }
        return $email;
    }
    private function createUser(array $data)
    {
        return User::create([
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'type' => $data['type'] ?? 'user', // Default to 'user'
            'password' => Hash::make($data['password']),
            'device_id' => $data['device_id'] ?? null,
            'fcm' => $data['fcm'] ?? null,
        ]);
    }
    // A separate method to get the user
    public function getUserByemail($email)
    {
        return User::where('email', $email)->first();
    }

    public function blockUser($user)
    {
        if ($user->status == '0') {
            return response()->json(['message' => __('custom.user_blocked'), 'user' => $user], 500);
        }
    }

    public function createTokenForUser($user)
    {
        return $user->createToken('authToken')->plainTextToken;
    }

    public function updateFcm($user, $fcm)
    {
        if ($fcm) {
            $user->fcm = $fcm;
            $user->save();
        }
    }
    public function getUserBytoken($token)
    {
        $accessToken = PersonalAccessToken::findToken($token);
        if (!$accessToken) {
            return response()->json(['message' => __('custom.login_first'), 401], 401);
        }
        return $accessToken->tokenable;
    }
    public function getOtpForUser(Request $request)
    {
        $email = $request->email;
        $otp = DB::table('otps')->where('identifier', $email)->orderBy('id', 'desc')->get();
        return response()->json(['otp' => $otp], 200);
    }
}
