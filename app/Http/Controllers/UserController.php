<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * List users (super-admin only)
     */
    public function index(Request $request)
    {
        // Basic role check via localStorage-based token flow
        $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
        if ($role !== 'super-admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $users = User::select('id', 'name', 'phone_number', 'role')->orderBy('id', 'asc')->get();
        return response()->json(['success' => true, 'data' => $users]);
    }

    /**
     * Create new admin user (super-admin only)
     */
    public function store(Request $request)
    {
        $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
        if ($role !== 'super-admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|unique:users,phone_number',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = new User();
            $user->name = $request->name;
            $user->phone_number = $request->phone_number;
            $user->role = 'admin';
            $user->password = 'Admin123!';
            $user->save();

            return response()->json(['success' => true, 'message' => 'Admin created', 'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone_number' => $user->phone_number,
                'role' => $user->role,
            ]], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create admin'], 500);
        }
    }

    /**
     * Reset password to default (super-admin only)
     */
    public function resetPassword($id, Request $request)
    {
        $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
        if ($role !== 'super-admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $user->password = 'Admin123!';
        $user->save();
        $this->logAction($request, 'admin_user_reset_password', ['user_id' => $id]);

        return response()->json(['success' => true, 'message' => 'Password reset to default']);
    }

    /**
     * Delete user (super-admin only)
     */
    public function destroy($id, Request $request)
    {
        $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
        if ($role !== 'super-admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        if ($user->role === 'super-admin') {
            return response()->json(['success' => false, 'message' => 'Cannot delete super-admin'], 403);
        }

        $user->delete();
        $this->logAction($request, 'admin_user_delete', ['user_id' => $id]);
        return response()->json(['success' => true, 'message' => 'User deleted']);
    }
}
