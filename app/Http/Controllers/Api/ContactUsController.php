<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'message' => 'required|string|max:255|min:5',
        ]);
        $user = $request->user;
        // $validatedData['user_id'] = $user->id;
        $validatedData['name'] = $user->name;
        $validatedData['email'] = $user->email;
        $validatedData['phone'] = $user->phone;
        $validatedData['subject'] = "Contact Us";

        ContactUs::create($validatedData);

        return response()->json([
            'status_code' => 200,
            'message' => 'Success',
        ], 200);
    }
}
