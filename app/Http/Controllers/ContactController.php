<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\WebsiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function info()
    {
        $settings = WebsiteSetting::first();

        return response()->json([
            'success' => true,
            'contact' => [
                'phone' => $settings?->phone ?? '',
                'email' => $settings?->email ?? '',
                'address' => $settings?->address ?? '',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'nullable|email|max:150',
            'phone'      => 'nullable|string|max:50',
            'message'    => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$request->filled('email') && !$request->filled('phone')) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide email or phone number.',
            ], 422);
        }

        $contactMessage = ContactMessage::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'message'    => $request->message,
            'is_read'    => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully.',
            'data' => $contactMessage,
        ], 201);
    }
}
