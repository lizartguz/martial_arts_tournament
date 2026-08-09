<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthApiC extends Controller
{
    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->user)
                ->orWhere('number_phone', $request->user)
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    "response" => "failed",
                    "error" => 'Invalid credentials',
                    "name" => '',
                    "email_number" => $request->user,
                    "type" => 'user',
                ]);
            }

            if ($user->device_identifier === null && $request->filled('identifier')) {
                $user->update(['device_identifier' => $request->identifier]);
            }

            return response()->json([
                "response" => "success",
                "error" => '',
                "name" => $user->name,
                "email_number" => $user->email ?? $user->number_phone,
                "type" => 'user',
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                ["revision controller or passport install" => "si", "error"=> $th->getMessage()],
                500
            );
        }
    }

}
