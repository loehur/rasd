<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhoneNumberController extends Controller
{
    /**
     * Get all phone numbers for a specific staff or team leader
     * Staff: only their own data
     * Team Leader: all data from their team
     */
    public function index(Request $request)
    {
        try {
            // Get token from Authorization header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Remove 'Bearer ' prefix if present
            $token = str_replace('Bearer ', '', $token);

            // Decode token to get employee_id
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);
            $employeeId = $parts[0] ?? null;
            $roleIndicator = $parts[1] ?? null; // 'staff' or timestamp

            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Find user in staff table first (for TL and Staff)
            $user = Staff::where('staff_id', $employeeId)->first();
            
            // If not found in staff table, check users table (for Admin)
            if (!$user) {
                $user = \App\Models\User::find($employeeId);
                if ($user) {
                    // Set a flag to identify this is from users table
                    $user->role = $user->role ?? 'admin';
                }
            }

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            $query = PhoneNumber::query();

            // If user is staff, only show their own data
            if ($user->role === 'staff') {
                $query->where('staff_id', $employeeId);
            }
            // If user is team leader, show all data from their team
            elseif ($user->role === 'tl') {
                // Get all staff under this team leader
                $teamStaffIds = Staff::where('team_leader_id', $user->staff_id)
                    ->pluck('staff_id')
                    ->toArray();
                
                $query->whereIn('staff_id', $teamStaffIds);
            }
            // Admin can see all data - no filter needed

            // Search by phone number (minimum 8 digits)
            $search = $request->input('search');
            if ($search && strlen($search) >= 8) {
                $query->where('phone_number', 'LIKE', '%' . $search . '%');
            }

            // Pagination parameters
            $page = (int) $request->input('page', 1);
            $perPage = (int) $request->input('per_page', 20);
            
            // Get total count before pagination
            $total = $query->count();
            
            // Calculate offset and get paginated data
            $offset = ($page - 1) * $perPage;
            $phoneNumbers = $query->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($perPage)
                ->get();
            
            // Calculate pagination info
            $totalPages = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'data' => $phoneNumbers,
                'user_role' => $user->role ?? 'admin',
                'search' => $search,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'total_pages' => $totalPages,
                    'has_prev' => $page > 1,
                    'has_next' => $page < $totalPages
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch phone numbers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new phone number (Staff only)
     */
    public function store(Request $request)
    {
        try {
            // Get token from Authorization header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Remove 'Bearer ' prefix if present
            $token = str_replace('Bearer ', '', $token);

            // Decode token to get employee_id
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);
            $employeeId = $parts[0] ?? null;

            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Find user in staff table
            $user = Staff::where('staff_id', $employeeId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Only staff can add phone numbers
            if ($user->role !== 'staff') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only staff members can add phone numbers'
                ], 403);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|max:20',
                'remarks' => 'required|string|max:500', // Changed to required
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get team leader info
            $teamLeaderName = null;
            $teamLeaderId = null;
            if ($user->team_leader_id) {
                $teamLeader = Staff::where('staff_id', $user->team_leader_id)->first();
                if ($teamLeader) {
                    $teamLeaderName = $teamLeader->name;
                    $teamLeaderId = $teamLeader->staff_id;
                }
            }

            // Sanitize phone number - only keep digits
            $cleanPhoneNumber = preg_replace('/[^0-9]/', '', $request->phone_number);

            // Create phone number entry
            $phoneNumber = PhoneNumber::create([
                'staff_id' => $user->staff_id,
                'employee_name' => $user->name,
                'team_leader_name' => $teamLeaderName,
                'team_leader_id' => $teamLeaderId,
                'phone_number' => $cleanPhoneNumber,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone number added successfully',
                'data' => $phoneNumber
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add phone number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a phone number (Staff only - their own data)
     */
    public function update(Request $request, $id)
    {
        try {
            // Get token from Authorization header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Remove 'Bearer ' prefix if present
            $token = str_replace('Bearer ', '', $token);

            // Decode token to get employee_id
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);
            $employeeId = $parts[0] ?? null;

            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            // Find phone number
            $phoneNumber = PhoneNumber::find($id);

            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found'
                ], 404);
            }

            // Get user info to check role
            $user = Staff::where('staff_id', $employeeId)->first();
            if (!$user) {
                $user = \App\Models\User::find($employeeId);
                if ($user) {
                    $user->role = $user->role ?? 'admin';
                }
            }

            // Admin can edit any, Staff can only edit their own
            if ($user && !in_array($user->role, ['admin', 'super-admin']) && $phoneNumber->staff_id !== $employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only edit your own phone numbers'
                ], 403);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|max:20',
                'remarks' => 'required|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Sanitize phone number - only keep digits
            $cleanPhoneNumber = preg_replace('/[^0-9]/', '', $request->phone_number);

            // Update phone number
            $phoneNumber->update([
                'phone_number' => $cleanPhoneNumber,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Phone number updated successfully',
                'data' => $phoneNumber
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update phone number: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a phone number (Staff only - their own data)
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Get token from Authorization header
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Remove 'Bearer ' prefix if present
            $token = str_replace('Bearer ', '', $token);

            // Decode token to get employee_id
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);
            $employeeId = $parts[0] ?? null;

            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token'
                ], 401);
            }

            $phoneNumber = PhoneNumber::find($id);

            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phone number not found'
                ], 404);
            }

            // Get user info to check role
            $user = Staff::where('staff_id', $employeeId)->first();
            if (!$user) {
                $user = \App\Models\User::find($employeeId);
                if ($user) {
                    $user->role = $user->role ?? 'admin';
                }
            }

            // Admin can delete any, Staff can only delete their own
            if ($user && !in_array($user->role, ['admin', 'super-admin']) && $phoneNumber->staff_id !== $employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own phone numbers'
                ], 403);
            }

            $phoneNumber->delete();

            return response()->json([
                'success' => true,
                'message' => 'Phone number deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete phone number: ' . $e->getMessage()
            ], 500);
        }
    }
}
