<?php

namespace App\Http\Controllers;

use App\Models\TeamLeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class TeamLeaderController extends Controller
{
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
            $csv = Reader::createFromPath($file->getRealPath(), 'r');
            $csv->setHeaderOffset(0);
            
            $records = $csv->getRecords();
            $imported = 0;
            $updated = 0;
            $errors = [];

            foreach ($records as $index => $record) {
                try {
                    // Skip if employee_id is empty
                    if (empty($record['ID TL'])) {
                        continue;
                    }

                    $data = [
                        'employee_id' => trim($record['ID TL']),
                        'name' => trim($record['Name TL']),
                        'work_location' => trim($record['WFH/Oniste']), // Note: CSV has typo "Oniste"
                        'position' => trim($record['Position']),
                        'team' => trim($record['Team']),
                        'team_quantity' => !empty($record['Team
Quantity']) ? (int)$record['Team
Quantity'] : null,
                        'department' => trim($record['Department']),
                        'hire_date' => $this->parseDate($record['Hiredate']),
                        'rank' => trim($record['Rank']),
                        'first_day_tl' => $this->parseDate($record['1st day to be TL']),
                        'warning_letter' => trim($record['Warning letter']),
                        'ojk_case' => !empty($record['OJK case']) ? (int)$record['OJK case'] : 0,
                        'former_tl' => trim($record['Former TL']),
                        'area' => trim($record['Area']),
                    ];

                    // Check if team leader already exists
                    $teamLeader = TeamLeader::where('employee_id', $data['employee_id'])->first();

                    if ($teamLeader) {
                        // Update existing team leader (don't change password)
                        unset($data['password']);
                        $teamLeader->update($data);
                        $updated++;
                    } else {
                        // Create new team leader with default password
                        $data['password'] = TeamLeader::getDefaultPassword();
                        TeamLeader::create($data);
                        $imported++;
                    }

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

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
            'SN',
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

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Add example row
            fputcsv($file, [
                '1',
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
