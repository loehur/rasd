<?php

namespace App\Http\Controllers;

use App\Models\TeamLeader;
use App\Models\Staff;
use App\Models\StaffLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamLeaderController extends Controller
{
    /**
     * Team Leader login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Employee ID and password are required',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find team leader by staff_id (staff_id is the same as employee_id)
            $teamLeader = TeamLeader::where('staff_id', $request->employee_id)->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid employee ID or password'
                ], 401);
            }

            // Verify password
            if (!password_verify($request->password, $teamLeader->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid employee ID or password'
                ], 401);
            }

            // Check if using default password (tl1230)
            $isDefaultPassword = password_verify('tl1230', $teamLeader->password);

            // Generate token (simple token for now)
            $token = base64_encode($teamLeader->staff_id . ':' . time());

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'is_default_password' => $isDefaultPassword,
                'user' => [
                    'employee_id' => $teamLeader->staff_id,
                    'name' => $teamLeader->name,
                    'position' => $teamLeader->position,
                    'group' => $teamLeader->group,
                    'department' => $teamLeader->department,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get staff under the logged-in team leader's supervision
     */
    public function getStaff(Request $request)
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

            // Find team leader
            $teamLeader = TeamLeader::where('staff_id', $employeeId)->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 404);
            }

            // Get staff under this team leader
            // Assuming staff.team_leader_id references team_leader.staff_id
            $staff = Staff::where('team_leader_id', $teamLeader->staff_id)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all team leaders
     */
    public function index()
    {
        // Only show active team leaders
        $teamLeaders = TeamLeader::where('staff_status', 'active')
                                 ->orderBy('name')
                                 ->get();

        return response()->json([
            'success' => true,
            'data' => $teamLeaders
        ]);
    }

    /**
     * Import team leaders from CSV
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file format. Please upload a CSV file.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $file = $request->file('file');
            $handle = fopen($file->getRealPath(), 'r');
            if ($handle === false) {
                throw new \Exception('Unable to open uploaded file');
            }

            // Read headers
            $headers = fgetcsv($handle);
            if ($headers === false) {
                throw new \Exception('CSV file is empty');
            }

            // Normalize headers: convert to UTF-8 and trim
            $headers = array_map(function ($h) {
                return trim($this->normalizeUtf8((string)$h));
            }, $headers);

            $imported = 0;
            $updated = 0;
            $errors = [];

            $rowIndex = 1; // header row
            while (($values = fgetcsv($handle)) !== false) {
                $rowIndex++;
                try {
                    // Build record associative array safely (handle uneven columns)
                    $record = [];
                    $max = min(count($headers), count($values));
                    for ($i = 0; $i < $max; $i++) {
                        $record[$headers[$i]] = isset($values[$i]) ? trim($this->normalizeUtf8($values[$i])) : '';
                    }

                    // Helper to get value by possible header names
                    $get = function ($names) use ($record) {
                        foreach ((array)$names as $n) {
                            if (isset($record[$n]) && $record[$n] !== '') {
                                return $record[$n];
                            }
                        }
                        return null;
                    };

                    $employeeId = $get(['ID TL', 'ID', 'Employee ID']);
                    if (empty($employeeId)) {
                        // skip rows without employee id
                        continue;
                    }

                    // Fallback phone number when missing: use staff_id (keeps uniqueness, non-null)
                    $phone = $get(['Phone Number', 'Phone']) ?? '';
                    if ($phone === '') {
                        $phone = $employeeId;
                    }

                    $data = [
                        'staff_id' => $employeeId,
                        'name' => $get(['Name TL', 'Name']) ?? '',
                        'phone_number' => $phone,
                        'work_location' => $get(['WFH/Onsite', 'WFH/Oniste', 'Work Location']) ?? '',
                        'position' => $get(['Position']) ?? 'DC TL',
                        'group' => $get(['Group', 'Team']) ?? '',
                        'department' => $get(['Department']) ?? '',
                        'hire_date' => $this->parseDate($get(['Hiredate', 'Hire Date'])) ?? date('Y-m-d'),
                        'rank' => $get(['Rank']) ?? '',
                        'first_day_tl' => $this->parseDate($get(['1st day to be TL', '1st day to be TL'])) ?? date('Y-m-d'),
                        'warning_letter' => $get(['Warning letter']) ?? '',
                        'ojk_case' => ($get(['OJK case']) !== null) ? (int)$get(['OJK case']) : 0,
                        'former_tl' => $get(['Former TL']) ?? '',
                        'area' => $get(['Area']) ?? '',
                        'role' => 'tl',
                    ];

                    // Check if staff already exists (use Staff model to bypass TeamLeader's global scope)
                    $existingStaff = Staff::where('staff_id', $employeeId)->first();

                    if ($existingStaff) {
                        // Update existing staff to TL (or update existing TL)
                        $existingStaff->update($data);
                        $updated++;
                    } else {
                        // Create new team leader with default password
                        $data['password'] = TeamLeader::getDefaultPassword();
                        Staff::create($data);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowIndex}: " . $e->getMessage();
                }
            }

            fclose($handle);

            return response()->json([
                'success' => true,
                'message' => "Import completed: {$imported} new, {$updated} updated",
                'data' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'errors' => $errors
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalize string to valid UTF-8 and sanitize placeholder values
     */
    private function normalizeUtf8($value)
    {
        if ($value === null) {
            return '';
        }
        $str = (string) $value;
        $str = preg_replace('/^\xEF\xBB\xBF/', '', $str);
        $enc = mb_detect_encoding($str, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($enc && $enc !== 'UTF-8') {
            $str = iconv($enc, 'UTF-8//IGNORE', $str);
        } else {
            $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        }
        $str = trim($str);
        if ($str === '-') {
            return '';
        }
        return $str;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString) || $dateString === '-') {
            return null;
        }

        try {
            // Try to parse date (handle MM/DD/YYYY format)
            $date = \DateTime::createFromFormat('n/j/Y', trim($dateString));
            if ($date) {
                return $date->format('Y-m-d');
            }

            // Fallback to strtotime
            $timestamp = strtotime(trim($dateString));
            if ($timestamp) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
            // Return null if parsing fails
        }

        return null;
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $filename = 'team_leader_import_template.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'WFH/Oniste',
            'ID TL',
            'Name TL',
            'Phone Number',
            'Position',
            'Group',
            'Department',
            'Hiredate',
            'Rank',
            '1st day to be TL',
            'Warning letter',
            'OJK case',
            'Former TL',
            'Area'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            // Add example row
            fputcsv($file, [
                'Onsite',
                'IDNA200001',
                'John Doe',
                '628123456789',
                'DC TL',
                'A1',
                'Department Name',
                '1/15/2024',
                'C1',
                '1/20/2024',
                '-',
                '0',
                'Recommended by HR',
                'Jakarta'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Update team leader name
     */
    public function updateName(Request $request)
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

            // Validate request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find and update team leader
            $teamLeader = TeamLeader::where('staff_id', $employeeId)->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 404);
            }

            $teamLeader->name = $request->name;
            $teamLeader->save();

            return response()->json([
                'success' => true,
                'message' => 'Name updated successfully',
                'data' => $teamLeader
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update name: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update team leader password
     */
    public function updatePassword(Request $request)
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

            // Validate request
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Find team leader
            $teamLeader = TeamLeader::where('staff_id', $employeeId)->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 404);
            }

            // Verify current password
            if (!password_verify($request->current_password, $teamLeader->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 401);
            }

            // Update password - pass plain text, let the model mutator hash it
            $teamLeader->password = $request->new_password;
            $teamLeader->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single team leader by employee_id
     */
    public function show($employeeId)
    {
        try {
            $teamLeader = TeamLeader::where('staff_id', $employeeId)->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $teamLeader
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get team leader: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update team leader data
     */
    public function update(Request $request, $employeeId)
    {
        try {
            $teamLeader = TeamLeader::where('staff_id', $employeeId)->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string',
                'position' => 'nullable|string',
                'group' => 'nullable|string',
                'department' => 'nullable|string',
                'area' => 'nullable|string',
                'hire_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $teamLeader->update($request->all());
            $this->logAction($request, 'team_leader_update', [
                'employee_id' => $employeeId,
                'fields' => array_keys($request->all()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Team leader updated successfully',
                'data' => $teamLeader
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team leader: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset team leader password to default
     *
     * @param Request $request
     * @param string $employeeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request, $employeeId)
    {
        try {
            // Find team leader using Staff model to bypass global scope
            $teamLeader = Staff::where('staff_id', $employeeId)
                              ->where('role', 'tl')
                              ->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 404);
            }

            // Reset password to default
            $defaultPassword = TeamLeader::getDefaultPassword();
            $teamLeader->password = password_hash($defaultPassword, PASSWORD_DEFAULT);
            $teamLeader->save();

            $this->logAction($request, 'team_leader_password_reset', [
                'employee_id' => $employeeId,
                'reset_by' => 'admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully to default: ' . $defaultPassword
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process team leader resignation and transfer staff
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resign(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'resigning_tl_id' => 'required|string',
                'replacement_tl_id' => 'required|string|different:resigning_tl_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $resigningTLId = $request->resigning_tl_id;
            $replacementTLId = $request->replacement_tl_id;

            // Verify both team leaders exist
            $resigningTL = TeamLeader::where('staff_id', $resigningTLId)->first();
            $replacementTL = TeamLeader::where('staff_id', $replacementTLId)->first();

            if (!$resigningTL) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resigning team leader not found'
                ], 404);
            }

            if (!$replacementTL) {
                return response()->json([
                    'success' => false,
                    'message' => 'Replacement team leader not found'
                ], 404);
            }

            // Get all active staff under the resigning TL
            $staffMembers = Staff::where('team_leader_id', $resigningTLId)
                                ->where('staff_status', 'active')
                                ->get();

            $transferredCount = 0;

            // Transfer each staff member to the replacement TL
            foreach ($staffMembers as $staff) {
                $oldTL = $staff->team_leader_id;
                $staff->team_leader_id = $replacementTLId;
                $staff->save();

                // Log the transfer using StaffLog model
                try {
                    StaffLog::create([
                        'staff_id' => $staff->staff_id,
                        'change_type' => 'team_leader_transfer',
                        'old_value' => [
                            'team_leader_id' => $oldTL,
                            'team_leader_name' => $resigningTL->name
                        ],
                        'new_value' => [
                            'team_leader_id' => $replacementTLId,
                            'team_leader_name' => $replacementTL->name
                        ],
                        'changed_by' => 'admin',
                        'remarks' => 'Team Leader Resignation - Transfer from ' . $resigningTL->name . ' to ' . $replacementTL->name
                    ]);
                } catch (\Exception $logError) {
                    // Log error but continue processing
                    \Illuminate\Support\Facades\Log::warning('Failed to log staff transfer: ' . $logError->getMessage());
                }

                $transferredCount++;
            }

            // Update the resigning TL status to inactive/resign
            $oldStatus = $resigningTL->staff_status;
            $resigningTL->staff_status = 'resign';
            $resigningTL->save();

            // Log the TL status change using StaffLog model
            try {
                StaffLog::create([
                    'staff_id' => $resigningTL->staff_id,
                    'change_type' => 'status_change',
                    'old_value' => [
                        'staff_status' => $oldStatus,
                        'role' => 'tl'
                    ],
                    'new_value' => [
                        'staff_status' => 'resign',
                        'role' => 'tl'
                    ],
                    'changed_by' => 'admin',
                    'remarks' => 'Team Leader Resignation'
                ]);
            } catch (\Exception $logError) {
                \Illuminate\Support\Facades\Log::warning('Failed to log TL status change: ' . $logError->getMessage());
            }

            $this->logAction($request, 'team_leader_resignation', [
                'resigning_tl_id' => $resigningTLId,
                'resigning_tl_name' => $resigningTL->name,
                'replacement_tl_id' => $replacementTLId,
                'replacement_tl_name' => $replacementTL->name,
                'transferred_staff_count' => $transferredCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully transferred {$transferredCount} staff member(s) from {$resigningTL->name} to {$replacementTL->name}",
                'transferred_count' => $transferredCount
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Team Leader Resignation Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process resignation: ' . $e->getMessage(),
                'error_details' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], 500);
        }
    }
}
