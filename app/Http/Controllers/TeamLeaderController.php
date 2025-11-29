<?php

namespace App\Http\Controllers;

use App\Models\TeamLeader;
use App\Models\Staff;
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
                    'employee_id' => $teamLeader->employee_id,
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
            // Assuming staff.team_leader_id references team_leader.employee_id
            $staff = Staff::where('team_leader_id', $teamLeader->employee_id)
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
        $teamLeaders = TeamLeader::orderBy('name')->get();

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
     * Reset team leader password to default (Admin only)
     */
    public function resetPassword(Request $request, $employeeId)
    {
        try {
            // Find team leader by employee_id
            $teamLeader = TeamLeader::where('staff_id', $employeeId)->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 404);
            }

            // Reset password to "tl1230" - pass plain text, let the model mutator hash it
            $teamLeader->password = 'tl1230';
            $teamLeader->save();

            $this->logAction($request, 'team_leader_reset_password', ['employee_id' => $employeeId]);
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. New password: tl1230'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
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
}
