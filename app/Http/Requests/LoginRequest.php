<?php

namespace App\Http\Requests;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{

    public function rules()
    {
        return [
            'email' => 'required|string|exists:users,email',
            'password' => 'required|string|min:8',
            'fcm' => 'nullable|string',
        ];
    }
}
