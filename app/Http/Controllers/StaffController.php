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
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            // Get header row and clean it
            $header = array_shift($csvData);
            $header = array_map('trim', $header);
            $headerCount = count($header);

            // Validate CSV structure
            $requiredColumns = ['staff_id', 'name', 'phone_number', 'position', 'department', 'hire_date'];
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
                $row = array_map('trim', $row);

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

                // Validate required fields
                if (empty($data['staff_id']) || empty($data['name']) || empty($data['phone_number'])) {
                    $skipped++;
                    $errors[] = "Row " . ($index + 2) . ": Missing required data";
                    continue;
                }

                // Prepare staff data
                $staffData = [
                    'staff_id' => $data['staff_id'],
                    'name' => $data['name'],
                    'phone_number' => $data['phone_number'],
                    'email' => !empty($data['email']) ? $data['email'] : null,
                    'position' => $data['position'] ?? '',
                    'department' => $data['department'] ?? '',
                    'superior' => !empty($data['superior']) ? $data['superior'] : null,
                    'group' => !empty($data['group']) ? $data['group'] : null,
                    'area' => !empty($data['area']) ? $data['area'] : null,
                    'work_location' => !empty($data['work_location']) ? $data['work_location'] : null,
                    'hire_date' => !empty($data['hire_date']) ? $data['hire_date'] : date('Y-m-d'),
                    'rank' => !empty($data['rank']) ? $data['rank'] : null,
                    'device' => !empty($data['device']) ? $data['device'] : null,
                    'team_leader_id' => !empty($data['team_leader_id']) ? $data['team_leader_id'] : null,
                    'warning_letter' => !empty($data['warning_letter']) ? $data['warning_letter'] : null,
                    'ojk_case' => !empty($data['ojk_case']) && is_numeric($data['ojk_case']) ? (int)$data['ojk_case'] : 0,
                    'notes' => !empty($data['notes']) ? $data['notes'] : null,
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
}
