<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle admin login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validate input
        $this->validate($request, [
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by phone number
        $user = User::where('phone_number', $request->phone_number)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP atau password salah.',
            ], 401);
        }

        // Generate simple token (format: user_id:timestamp, same as Team Leader)
        $token = base64_encode($user->id . ':' . time());

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'phone_number' => $user->phone_number,
                    'role' => $user->role,
                ],
                'token' => $token,
            ],
        ], 200);
    }
}
