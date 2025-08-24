<?php

namespace App\Http\Requests\Auth;

use App\Rules\ValidPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => 'required|string|min:8',
            'phone' => ['required', 'string', 'max:30', 'unique:users', new ValidPhoneNumber],
            'type' => ['nullable', Rule::in(['admin', 'user', 'supervisor', 'driver'])],
            'fcm' => ['nullable', 'string'],
            'device_id' => ['nullable', 'string'],
        ];
    }
}
