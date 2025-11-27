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
            // Find team leader by employee_id
            $teamLeader = TeamLeader::where('employee_id', $request->employee_id)->first();

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

            // Generate token (simple token for now)
            $token = base64_encode($teamLeader->employee_id . ':' . time());

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'employee_id' => $teamLeader->employee_id,
                    'name' => $teamLeader->name,
                    'position' => $teamLeader->position,
                    'team' => $teamLeader->team,
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
            $teamLeader = TeamLeader::where('employee_id', $employeeId)->first();

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
                ->get(['staff_id as employee_id', 'name', 'position', 'team_leader_id']);

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

            // Normalize headers (trim)
            $headers = array_map(function ($h) {
                return trim((string)$h);
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
                        $record[$headers[$i]] = isset($values[$i]) ? trim($values[$i]) : '';
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

                    $data = [
                        'employee_id' => $employeeId,
                        'name' => $get(['Name TL', 'Name']) ?? '',
                        'work_location' => $get(['WFH/Onsite', 'WFH/Oniste', 'Work Location']) ?? '',
                        'position' => $get(['Position']) ?? '',
                        'team' => $get(['Team']) ?? '',
                        'team_quantity' => ($get(['Team Quantity', 'Team
Quantity']) !== null) ? (int)$get(['Team Quantity', 'Team
Quantity']) : null,
                        'department' => $get(['Department']) ?? '',
                        'hire_date' => $this->parseDate($get(['Hiredate', 'Hire Date'])),
                        'rank' => $get(['Rank']) ?? '',
                        'first_day_tl' => $this->parseDate($get(['1st day to be TL', '1st day to be TL'])),
                        'warning_letter' => $get(['Warning letter']) ?? '',
                        'ojk_case' => ($get(['OJK case']) !== null) ? (int)$get(['OJK case']) : 0,
                        'former_tl' => $get(['Former TL']) ?? '',
                        'area' => $get(['Area']) ?? '',
                    ];

                    // Check if team leader already exists
                    $teamLeader = TeamLeader::where('employee_id', $data['employee_id'])->first();

                    if ($teamLeader) {
                        // Update existing team leader (don't change password)
                        $teamLeader->update($data);
                        $updated++;
                    } else {
                        // Create new team leader with default password
                        $data['password'] = TeamLeader::getDefaultPassword();
                        TeamLeader::create($data);
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
            'Position',
            'Team',
            'Team Quantity',
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
                'DC TL',
                'A1',
                '10',
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
}
