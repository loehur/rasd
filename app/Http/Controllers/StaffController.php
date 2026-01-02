<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Download CSV template for staff import
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        $templatePath = base_path('assets/staff_import_template.csv');

        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file not found',
            ], 404);
        }

        $content = file_get_contents($templatePath);

        return response($content, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="staff_import_template.csv"');
    }

    /**
     * Import staff data from CSV
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        // Validate file upload
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file. Please upload a CSV file (max 2MB).',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            $lines = file($file->getRealPath());
            if ($lines === false) {
                throw new \Exception('Unable to read uploaded file');
            }

            // Convert each line to UTF-8 and parse CSV
            $csvData = [];
            foreach ($lines as $idx => $line) {
                $normalizedLine = $this->normalizeUtf8($line);
                // Remove BOM on very first line if present
                if ($idx === 0) {
                    $normalizedLine = preg_replace('/^\xEF\xBB\xBF/', '', $normalizedLine);
                }
                $csvData[] = str_getcsv($normalizedLine);
            }

            // Get header row and clean it
            $header = array_shift($csvData);
            $header = array_map(function ($h) {
                return trim($this->normalizeUtf8($h));
            }, $header);
            $headerCount = count($header);

            // Validate CSV structure
            $requiredColumns = ['staff_id', 'name', 'position', 'department', 'hire_date'];
            $missingColumns = array_diff($requiredColumns, $header);

            if (!empty($missingColumns)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missingColumns),
                ], 422);
            }

            $imported = 0;
            $updated = 0;
            $skipped = 0;
            $errors = [];

            foreach ($csvData as $index => $row) {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Trim all values
                $row = array_map(function ($v) {
                    return $this->normalizeUtf8($v);
                }, $row);

                // Ensure row has same number of elements as header
                $rowCount = count($row);
                if ($rowCount < $headerCount) {
                    // Pad with empty strings if row has fewer columns
                    $row = array_pad($row, $headerCount, '');
                } elseif ($rowCount > $headerCount) {
                    // Truncate if row has more columns
                    $row = array_slice($row, 0, $headerCount);
                }

                // Map CSV columns to array
                $data = array_combine($header, $row);

                // Validate required fields (phone number optional; will fallback to staff_id)
                if (empty($data['staff_id']) || empty($data['name'])) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 2) . ": Missing required data";
                    continue;
                }

                // Prepare staff data
                // Fallback phone number when missing: use staff_id (keeps uniqueness, non-null)
                $phone = $this->normalizeUtf8($data['phone_number'] ?? '');
                if ($phone === '') {
                    $phone = $data['staff_id'];
                }

                $staffData = [
                    'staff_id' => $data['staff_id'],
                    'name' => $this->normalizeUtf8($data['name']),
                    'phone_number' => $phone,
                    'email' => !empty($data['email']) ? $this->normalizeUtf8($data['email']) : null,
                    'position' => $this->normalizeUtf8($data['position'] ?? ''),
                    'department' => $this->normalizeUtf8($data['department'] ?? ''),
                    'group' => !empty($data['group']) ? $this->normalizeUtf8($data['group']) : null,
                    'area' => !empty($data['area']) ? $this->normalizeUtf8($data['area']) : null,
                    'work_location' => !empty($data['work_location']) ? $this->normalizeUtf8($data['work_location']) : null,
                    'hire_date' => !empty($data['hire_date']) ? ($this->parseDate($data['hire_date']) ?? date('Y-m-d')) : date('Y-m-d'),
                    'rank' => !empty($data['rank']) ? $this->normalizeUtf8($data['rank']) : null,
                    'device' => !empty($data['device']) ? $this->normalizeUtf8($data['device']) : null,
                    'team_leader_id' => !empty($data['team_leader_id']) ? $this->normalizeUtf8($data['team_leader_id']) : null,
                    'warning_letter' => !empty($data['warning_letter']) ? $this->normalizeUtf8($data['warning_letter']) : null,
                    'ojk_case' => !empty($data['ojk_case']) && is_numeric($data['ojk_case']) ? (int)$data['ojk_case'] : 0,
                    'notes' => !empty($data['notes']) ? $this->normalizeUtf8($data['notes']) : null,
                ];

                try {
                    // Check if staff already exists
                    $existingStaff = Staff::where('staff_id', $data['staff_id'])->first();

                    if ($existingStaff) {
                        // Update existing staff
                        $existingStaff->update($staffData);
                        $updated++;
                    } else {
                        // Create new staff
                        Staff::create($staffData);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Import completed: {$imported} new staff, {$updated} updated, {$skipped} skipped",
                'data' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => $errors,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
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
        // Strip UTF-8 BOM if present
        $str = preg_replace('/^\xEF\xBB\xBF/', '', $str);
        // Detect and convert encoding to UTF-8
        $enc = mb_detect_encoding($str, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($enc && $enc !== 'UTF-8') {
            $str = iconv($enc, 'UTF-8//IGNORE', $str);
        } else {
            // Ensure valid UTF-8
            $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        }
        $str = trim($str);
        // Treat dash placeholders as empty
        if ($str === '-') {
            return '';
        }
        return $str;
    }

    /**
     * Parse date from various formats to Y-m-d
     */
    private function parseDate($dateString)
    {
        if (empty($dateString) || $dateString === '-') {
            return null;
        }
        try {
            $dateString = trim((string) $dateString);
            // Try MM/DD/YYYY
            $date = \DateTime::createFromFormat('n/j/Y', $dateString);
            if ($date) {
                return $date->format('Y-m-d');
            }
            // Fallback
            $timestamp = strtotime($dateString);
            if ($timestamp) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
        }
        return null;
    }

    /**
     * Get all staff
     */
    public function index()
    {
        $staff = Staff::orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $staff,
        ], 200);
    }

    /**
     * Delete a staff by staff_id (admin only)
     */
    public function destroy($staffId, Request $request)
    {
        $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
        if (!in_array($role, ['admin', 'super-admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $staff = Staff::where('staff_id', $staffId)->first();
        if (!$staff) {
            return response()->json(['success' => false, 'message' => 'Staff not found'], 404);
        }

        try {
            $staff->delete();
            $this->logAction($request, 'admin_staff_delete', ['staff_id' => $staffId]);
            return response()->json(['success' => true, 'message' => 'Staff deleted']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete staff'], 500);
        }
    }

    /**
     * Reset staff password to default (staff1230) - Admin only
     */
    public function resetPassword($staffId)
    {
        try {
            $staff = Staff::where('staff_id', $staffId)
                ->where('role', 'staff')
                ->first();

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found'
                ], 404);
            }

            // Reset password to default
            $defaultPassword = password_hash('staff1230', PASSWORD_BCRYPT);
            $staff->update(['password' => $defaultPassword]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully to staff1230'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset all staff data (Super Admin only)
     * WARNING: This will permanently delete ALL staff records
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetAll(Request $request)
    {
        // Verify super-admin role
        $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
        if ($role !== 'super-admin') {
            // Log unauthorized access attempt
            \Illuminate\Support\Facades\Log::warning('[SECURITY] Unauthorized Reset Data Attempt', [
                'role' => $role,
                'ip' => $request->ip(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Access denied. Super-admin only.'
            ], 403);
        }

        // Validate password
        $validator = Validator::make($request->all(), [
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Password is required',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get current user from token
            $token = $request->header('Authorization');
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Extract user ID from token (assuming base64 encoded "user_id:timestamp")
            $token = str_replace('Bearer ', '', $token);
            $decoded = base64_decode($token);
            $parts = explode(':', $decoded);
            $userId = $parts[0] ?? null;

            // Log token debugging info
            \Illuminate\Support\Facades\Log::debug('Reset Data Token Debug', [
                'decoded' => $decoded,
                'user_id' => $userId,
                'parts_count' => count($parts)
            ]);

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid session token. Please logout and login again.'
                ], 401);
            }

            // Find user
            $user = \App\Models\User::where('id', $userId)->first();
            if (!$user) {
                // Log the failed user lookup
                \Illuminate\Support\Facades\Log::warning('Reset Data - User Not Found', [
                    'user_id_from_token' => $userId,
                    'decoded_token' => $decoded
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Your session is invalid or expired. Please logout and login again to continue.'
                ], 401);
            }

            // Verify password
            if (!password_verify($request->password, $user->password)) {
                // Log failed attempt
                \Illuminate\Support\Facades\Log::warning('[SECURITY] Failed Reset Data Attempt - Invalid Password', [
                    'admin_id' => $userId,
                    'admin_name' => $user->name,
                    'admin_email' => $user->email,
                    'ip' => $request->ip(),
                    'timestamp' => date('Y-m-d H:i:s')
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 401);
            }

            // Count records before deletion
            $count = Staff::count();

            // Delete all staff records
            Staff::query()->delete();

            // Prepare log details
            $logDetails = [
                'deleted_count' => $count,
                'user_id' => $userId,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_phone' => $user->phone_number ?? 'N/A',
                'user_role' => $user->role,
                'timestamp' => date('Y-m-d H:i:s'),
                'action' => 'DELETE ALL STAFF DATA',
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ];

            // Log the action to database
            $this->logAction($request, 'system_reset_all_staff', $logDetails);

            // Also log to Laravel log file for system audit
            \Illuminate\Support\Facades\Log::warning('[CRITICAL] System Reset All Staff Data', [
                'admin_id' => $userId,
                'admin_name' => $user->name,
                'admin_email' => $user->email,
                'deleted_records' => $count,
                'ip' => $request->ip(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted all staff data",
                'deleted' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset staff data: ' . $e->getMessage()
            ], 500);
        }
    }
}
