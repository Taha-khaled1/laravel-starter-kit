<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\AttendanceRecord;
use App\Models\Country;
use App\Traits\ImageProcessing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    use ImageProcessing;
    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user;

        // Validate the request data
        $validated = $request->validate([
            'name' => 'nullable|string',
            'phone' => 'nullable|string',
            'type' => 'in:admin,user,supervisor,driver',
            'city_id' => 'nullable|exists:cities,id',
            'fcm' => 'nullable|string',
            'device_id' => 'nullable|string',
            'current_team_id' => 'nullable|exists:teams,id',
        ]);
        //'image'|'certificate_path|'academic_qualification_path' |'cv_path'
        if ($request->hasFile('image')) {
            $imageName = $this->saveImage(
                $request->file("image"),
                "users/images"
            );
            $validated['image'] = $imageName;
        }

      

        if ($request->has('country_name_ar') && $request->has('country_name_en') && $request->has('country_code')) {
            $existsCountry = Country::where('code', $request->country_code)->first();
            if (!$existsCountry) {
                $country = Country::create([
                    'name_ar' => $request->country_name_ar,
                    'name_en' => $request->country_name_en,
                    'code' => $request->country_code
                ]);
                $validated['country_id'] = $country->id;
            } else {
                $validated['country_id'] = $existsCountry->id;
            }
        }
        
        $user->update($validated);
        // Return response
        return response()->json([
            'message' => 'User updated successfully',
            'user' => new UserResource($user),
        ]);
    }   // Settings with Cache

    /**
     * Change user password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = $request->user;

            // Check if current password is correct
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect',
                    'status_code' => 422
                ], 422);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'user' => new UserResource($user),
                'message' => 'Password changed successfully',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error changing password: ' . $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    public function getUserInfo(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $accessToken = PersonalAccessToken::findToken($token);
            if (!$accessToken) {
                return response()->json(['message' => __('custom.unauthorized'), 401], 401);
            }
            $user = $accessToken->tokenable;



            return successResponse(
                [
                    'user' => new UserResource($user),
                ],
                200,
                __('custom.user_info'),

            );
        } catch (\Throwable $th) {
            return response()->json(['message' => __('custom.server_issue') . $th, 'status_code' => 404,], 404);
        }
    }

    public function getOtpForUser(Request $request)
    {
        echo $request->email . "+";
        $email = $request->email;
        $otp = DB::table('otps')->where('identifier', "+" . $email)->orderBy('id', 'desc')->get();
        return response()->json(['otp' => $otp], 200);
    }
}
