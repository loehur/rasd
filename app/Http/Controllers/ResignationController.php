<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ResignationController extends Controller
{
    /**
     * Get all resignations with pagination and optional filter
     */
    public function index(Request $request)
    {
        try {
            // Allow Admin/Super-Admin to access without Team Leader validation
            $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
            $isAdmin = in_array($role, ['admin', 'super-admin'], true);

            if (!$isAdmin) {
                // Get token from Authorization header (Team Leader)
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

                // Get team leader info from staff table
                $teamLeader = DB::table('staff')
                    ->where('role', 'tl')
                    ->where('staff_id', $employeeId)
                    ->first();

                if (!$teamLeader) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Team leader not found'
                    ], 401);
                }
            }

            // Get query parameters
            $page = (int) $request->query('page', 1);
            $perPage = (int) $request->query('per_page', 1000);
            $reportDay = $request->query('report_day');
            $month = $request->query('month'); // format YYYY-MM

            // Base query: join staff for enrichment
            $query = DB::table('staff_resignation_log')
                ->leftJoin('staff', 'staff_resignation_log.staff_id', '=', 'staff.staff_id')
                ->leftJoin('staff as tl', 'staff.team_leader_id', '=', 'tl.staff_id')
                ->select(
                    'staff_resignation_log.*',
                    'staff.name as staff_name',
                    'staff.position as staff_position',
                    'staff.department',
                    'staff.group',
                    'staff.rank',
                    'staff.hire_date',
                    'staff.area',
                    'staff.work_location',
                    'staff.device',
                    'tl.name as staff_superior'
                );

            // Apply day filter if provided
            if ($reportDay) {
                $query->whereDate('staff_resignation_log.report_day', $reportDay);
            }

            // Apply month filter (default to current month to avoid huge datasets)
            if ($month) {
                [$y, $m] = explode('-', $month);
                $query->whereYear('staff_resignation_log.report_day', (int) $y)
                    ->whereMonth('staff_resignation_log.report_day', (int) $m);
            } else {
                $now = new \DateTime();
                $query->whereYear('staff_resignation_log.report_day', (int) $now->format('Y'))
                    ->whereMonth('staff_resignation_log.report_day', (int) $now->format('n'));
            }

            // Get total count
            $total = $query->count();

            // Get paginated results
            $resignations = $query
                ->orderBy('staff_resignation_log.created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Calculate pagination data
            $lastPage = ceil($total / $perPage);

            return response()->json([
                'success' => true,
                'data' => $resignations,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'from' => (($page - 1) * $perPage) + 1,
                    'to' => min($page * $perPage, $total),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch resignations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new resignation
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

            // Get team leader info from staff table
            $teamLeader = DB::table('staff')
                ->where('role', 'tl')
                ->where('staff_id', $employeeId)
                ->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 401);
            }

            // Validate input
            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|string',
                'last_working_day' => 'required',
                'resignation_type' => 'required|in:voluntary,involuntary',
                'resignation_subtype' => 'required|in:personal_reason,management_reason',
                'report_day' => 'required',
                'reason' => 'required|string|max:1000',
                'proof' => 'nullable|file|mimes:jpeg,jpg,png|max:10240',
                'ranking_intervals' => 'required|in:Top 5%,5% ~ 25%,25% ~ 50%,50% ~ 70%,70% ~ 90%,Bottom 10%'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if staff exists and is active
            $staff = DB::table('staff')
                ->where('staff_id', $request->staff_id)
                ->first();

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found'
                ], 404);
            }

            if ($staff->staff_status === 'inactive') {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff is already inactive'
                ], 400);
            }

            // Verify that this staff belongs to the team leader
            if ($staff->team_leader_id !== $teamLeader->staff_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only process resignations for your own team members'
                ], 403);
            }

            // Start transaction
            DB::beginTransaction();

            try {
                // Handle file upload and compression
                $proofPath = null;
                if ($request->hasFile('proof')) {
                    $proofPath = $this->compressAndSaveImage($request->file('proof'));
                }

                $lastWorking = $this->parseDate($request->last_working_day);
                $reportDay = $this->parseDate($request->report_day);

                if (!$lastWorking || !$reportDay) {
                    throw new \Exception('Invalid date format for last_working_day or report_day');
                }

                DB::table('staff_resignation_log')->insert([
                    'staff_id' => $request->staff_id,
                    'submitted_by' => $teamLeader->staff_id,
                    'resignation_type' => $request->resignation_type,
                    'resignation_subtype' => $request->resignation_subtype,
                    'last_working_day' => $lastWorking,
                    'report_day' => $reportDay,
                    'reason' => $request->reason,
                    'ranking_intervals' => $request->ranking_intervals,
                    'proof' => $proofPath,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // Update staff status to inactive
                DB::table('staff')
                    ->where('staff_id', $request->staff_id)
                    ->update([
                        'staff_status' => 'inactive',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Resignation recorded successfully. Staff status updated to inactive.'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record resignation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific resignation by ID
     */
    public function show(Request $request, $id)
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

            // Get team leader info from staff table
            $teamLeader = DB::table('staff')
                ->where('role', 'tl')
                ->where('staff_id', $employeeId)
                ->first();

            if (!$teamLeader) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team leader not found'
                ], 401);
            }

            // Get resignation
            $resignation = DB::table('staff_resignation_log')
                ->where('id', $id)
                ->where('submitted_by', $teamLeader->staff_id)
                ->first();

            if (!$resignation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resignation not found'
                ], 404);
            }

            // Get staff details
            $staff = DB::table('staff')
                ->where('staff_id', $resignation->staff_id)
                ->first();

            $resignation->staff_name = $staff ? $staff->name : 'Unknown';
            $resignation->staff_position = $staff ? $staff->position : 'Unknown';

            return response()->json([
                'success' => true,
                'data' => $resignation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch resignation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compress and save image to max 200KB
     */
    private function compressAndSaveImage($file)
    {
        // Generate unique filename (always save as JPG for uniform compression)
        $filename = 'proof_' . time() . '_' . uniqid() . '.jpg';
        $path = 'uploads/resignation_proofs/' . $filename;
        $fullPath = base_path('public') . DIRECTORY_SEPARATOR . $path;

        // Ensure directory exists
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Use Intervention if available; else fallback to GD; else move original
        if (class_exists('Intervention\\Image\\ImageManagerStatic')) {
            $image = \Intervention\Image\ImageManagerStatic::make($file);

            $quality = 85;
            if ($image->width() > 1920 || $image->height() > 1920) {
                $image->resize(1920, 1920, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            do {
                $image->save($fullPath, $quality);
                $filesize = @filesize($fullPath) ?: 0;
                if ($filesize > 200000 && $quality > 10) {
                    $quality -= 5;
                } else {
                    break;
                }
            } while ($quality > 10);

            return $path;
        }

        // GD fallback
        if (function_exists('imagecreatefromjpeg') || function_exists('imagecreatefrompng')) {
            $tmpPath = $file->getRealPath();
            $ext = strtolower($file->getClientOriginalExtension());
            $src = null;
            if ($ext === 'jpg' || $ext === 'jpeg') {
                $src = @imagecreatefromjpeg($tmpPath);
            } elseif ($ext === 'png') {
                $src = @imagecreatefrompng($tmpPath);
                if ($src) {
                    // Flatten PNG to white background when converting to JPG
                    $w = imagesx($src);
                    $h = imagesy($src);
                    $flatten = imagecreatetruecolor($w, $h);
                    $white = imagecolorallocate($flatten, 255, 255, 255);
                    imagefilledrectangle($flatten, 0, 0, $w, $h, $white);
                    imagecopy($flatten, $src, 0, 0, 0, 0, $w, $h);
                    imagedestroy($src);
                    $src = $flatten;
                }
            }

            if ($src) {
                // Resize if larger than 1920
                $w = imagesx($src);
                $h = imagesy($src);
                if ($w > 1920 || $h > 1920) {
                    $ratio = min(1920 / $w, 1920 / $h);
                    $newW = (int) floor($w * $ratio);
                    $newH = (int) floor($h * $ratio);
                    $resized = imagecreatetruecolor($newW, $newH);
                    imagecopyresampled($resized, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
                    imagedestroy($src);
                    $src = $resized;
                }

                // Compress to under 200KB where possible
                $quality = 85;
                do {
                    imagejpeg($src, $fullPath, $quality);
                    $filesize = @filesize($fullPath) ?: 0;
                    if ($filesize > 200000 && $quality > 10) {
                        $quality -= 5;
                    } else {
                        break;
                    }
                } while ($quality > 10);

                imagedestroy($src);
                return $path;
            }
        }

        // Last resort: move original file without compression
        $file->move($directory, $filename);
        return $path;
    }

    public function reactivate(Request $request)
    {
        try {
            $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
            $isAdmin = in_array($role, ['admin', 'super-admin'], true);

            if (!$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'staff_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $staffId = $request->input('staff_id');

            DB::beginTransaction();
            try {
                DB::table('staff_resignation_log')
                    ->where('staff_id', $staffId)
                    ->delete();

                DB::table('staff')
                    ->where('staff_id', $staffId)
                    ->update([
                        'staff_status' => 'active',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                DB::commit();
                $this->logAction($request, 'staff_reactivate', ['staff_id' => $staffId]);

                return response()->json([
                    'success' => true,
                    'message' => 'Staff reactivated successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download CSV template for resignation import
     */
    public function downloadTemplate()
    {
        $templatePath = base_path('assets/resignation_import_template.csv');

        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file not found',
            ], 404);
        }

        $content = file_get_contents($templatePath);

        return response($content, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="resignation_import_template.csv"');
    }

    /**
     * Import resignation data from CSV (admin only)
     */
    public function import(Request $request)
    {
        $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
        if (!in_array($role, ['admin', 'super-admin'], true)) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file. Please upload a CSV file (max 4MB).',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $file = $request->file('file');
            $lines = file($file->getRealPath());
            if ($lines === false) {
                throw new \Exception('Unable to read uploaded file');
            }

            $csv = [];
            foreach ($lines as $idx => $line) {
                $normalized = $this->normalizeUtf8($line);
                if ($idx === 0) {
                    $normalized = preg_replace('/^\xEF\xBB\xBF/', '', $normalized);
                }
                $csv[] = str_getcsv($normalized);
            }

            $header = array_shift($csv);
            $header = array_map(function ($h) {
                return trim($this->normalizeUtf8($h));
            }, $header);
            $headerCount = count($header);

            $required = ['staff_id', 'last_working_day', 'resignation_type', 'resignation_subtype', 'report_day', 'reason', 'ranking_intervals'];
            $missing = array_diff($required, $header);
            if (!empty($missing)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required columns: ' . implode(', ', $missing),
                ], 422);
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            DB::beginTransaction();
            try {
                foreach ($csv as $index => $row) {
                    if (empty(array_filter($row))) {
                        continue;
                    }
                    $row = array_map(function ($v) {
                        return $this->normalizeUtf8($v);
                    }, $row);
                    $rowCount = count($row);
                    if ($rowCount < $headerCount) {
                        $row = array_pad($row, $headerCount, '');
                    } elseif ($rowCount > $headerCount) {
                        $row = array_slice($row, 0, $headerCount);
                    }
                    $data = array_combine($header, $row);

                    // Basic validation
                    if (empty($data['staff_id'])) {
                        $skipped++;
                        $errors[] = 'Row ' . ($index + 2) . ': staff_id is required (value: ' . ($data['staff_id'] ?? '') . ')';
                        continue;
                    }

                    $staff = DB::table('staff')->where('staff_id', $data['staff_id'])->first();
                    if (!$staff) {
                        $skipped++;
                        $errors[] = 'Row ' . ($index + 2) . ': staff not found (staff_id: ' . $data['staff_id'] . ')';
                        continue;
                    }

                    $lastWorking = $this->parseDate($data['last_working_day']);
                    $report = $this->parseDate($data['report_day']);
                    if (!$lastWorking) {
                        $skipped++;
                        $errors[] = 'Row ' . ($index + 2) . ': invalid last_working_day (value: ' . ($data['last_working_day'] ?? '') . ')';
                        continue;
                    }
                    if (!$report) {
                        $skipped++;
                        $errors[] = 'Row ' . ($index + 2) . ': invalid report_day (value: ' . ($data['report_day'] ?? '') . ')';
                        continue;
                    }

                    $type = $this->normalizeType($data['resignation_type']);
                    $subtype = $this->normalizeSubtype($data['resignation_subtype']);
                    if (!in_array($type, ['voluntary', 'involuntary'], true)) {
                        $skipped++;
                        $errors[] = 'Row ' . ($index + 2) . ': invalid resignation_type (value: ' . ($data['resignation_type'] ?? '') . ')';
                        continue;
                    }
                    if (!in_array($subtype, ['personal_reason', 'management_reason'], true)) {
                        $skipped++;
                        $errors[] = 'Row ' . ($index + 2) . ': invalid resignation_subtype (value: ' . ($data['resignation_subtype'] ?? '') . ')';
                        continue;
                    }

                    $rankIntervals = trim($this->normalizeInterval($data['ranking_intervals']));
                    // Simplify: accept any normalized range or label; allow empty by storing null
                    if ($rankIntervals === '') {
                        $rankIntervals = null;
                    } else if (preg_match('/^(\d{1,3})%\s*~\s*(\d{1,3})%$/', $rankIntervals, $mm)) {
                        $a = (int) $mm[1];
                        $b = (int) $mm[2];
                        if (!($a >= 0 && $a < $b && $b <= 100)) {
                            $rankIntervals = null; // fallback: store null if range invalid
                        }
                    }

                    $proofPath = null;
                    if (!empty($data['proof'])) {
                        // Accept URL or relative path; store as given
                        $proofPath = $data['proof'];
                    }

                    DB::table('staff_resignation_log')->updateOrInsert(
                        ['staff_id' => $data['staff_id']],
                        [
                            'submitted_by' => $staff->team_leader_id ?: 'admin',
                            'resignation_type' => $type,
                            'resignation_subtype' => $subtype,
                            'last_working_day' => $lastWorking,
                            'report_day' => $report,
                            'reason' => $data['reason'],
                            'ranking_intervals' => $rankIntervals,
                            'proof' => $proofPath,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    );

                    DB::table('staff')
                        ->where('staff_id', $data['staff_id'])
                        ->update([
                            'staff_status' => 'inactive',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    $imported++;
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

            return response()->json([
                'success' => true,
                'message' => "Import completed: {$imported} imported, {$skipped} skipped",
                'data' => [
                    'imported' => $imported,
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

    public function months(Request $request)
    {
        try {
            $role = $request->header('X-Role') ?? ($request->input('role') ?? null);
            $isAdmin = in_array($role, ['admin', 'super-admin'], true);

            if (!$isAdmin) {
                $token = $request->header('Authorization');
                if (!$token) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
                }
                $token = str_replace('Bearer ', '', $token);
                $decoded = base64_decode($token);
                $parts = explode(':', $decoded);
                $employeeId = $parts[0] ?? null;
                if (!$employeeId) {
                    return response()->json(['success' => false, 'message' => 'Invalid token'], 401);
                }
                $teamLeader = DB::table('staff')
                    ->where('role', 'tl')
                    ->where('staff_id', $employeeId)
                    ->first();
                if (!$teamLeader) {
                    return response()->json(['success' => false, 'message' => 'Team leader not found'], 401);
                }
            }

            $months = DB::table('staff_resignation_log')
                ->select(DB::raw("DATE_FORMAT(report_day, '%Y-%m') as month"), DB::raw('COUNT(*) as count'))
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $months,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch months: ' . $e->getMessage()
            ], 500);
        }
    }

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

    private function parseDate($dateString)
    {
        if (empty($dateString) || $dateString === '-') {
            return null;
        }
        try {
            $dateString = trim((string) $dateString);

            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $m)) {
                $a = (int) $m[1];
                $b = (int) $m[2];
                $y = (int) $m[3];
                $dateString = sprintf('%02d/%02d/%04d', $a, $b, $y);
            } elseif (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $dateString, $m)) {
                $a = (int) $m[1];
                $b = (int) $m[2];
                $y = (int) $m[3];
                $dateString = sprintf('%02d-%02d-%04d', $a, $b, $y);
            }

            $formats = ['n/j/Y', 'j/n/Y', 'm/d/Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'];
            foreach ($formats as $fmt) {
                $date = \DateTime::createFromFormat($fmt, $dateString);
                if ($date) {
                    return $date->format('Y-m-d');
                }
            }
            $timestamp = strtotime($dateString);
            if ($timestamp) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Exception $e) {
        }
        return null;
    }

    private function normalizeType($value)
    {
        $v = strtolower($this->normalizeUtf8($value));
        if (strpos($v, 'volun') !== false) {
            return 'voluntary';
        }
        if (strpos($v, 'invol') !== false || strpos($v, 'terminat') !== false || strpos($v, 'departure') !== false) {
            return 'involuntary';
        }
        return $v;
    }

    private function normalizeSubtype($value)
    {
        $v = strtolower($this->normalizeUtf8($value));
        if (strpos($v, 'personal') !== false) {
            return 'personal_reason';
        }
        if (strpos($v, 'manage') !== false || strpos($v, 'company') !== false) {
            return 'management_reason';
        }
        return $v;
    }

    private function normalizeInterval($value)
    {
        $raw = $this->normalizeUtf8($value);
        $v = strtolower($raw);
        // Normalize various separators to hyphen
        $v = str_replace(['–', '—'], '-', $v);
        $v = preg_replace('/\s*(s\/d|sd|to)\s*/', '-', $v);
        $v = preg_replace('/\s+/', ' ', $v);
        if (strpos($v, 'top') !== false) {
            return 'Top 5%';
        }
        if (strpos($v, 'bottom') !== false) {
            return 'Bottom 10%';
        }
        // Match ranges like 70-90, 70%-90%, 70 % - 90 % , 70%~90%
        if (preg_match('/(\d{1,3})\s*%?\s*[-~]\s*(\d{1,3})\s*%?/', $v, $m)) {
            $a = (int) $m[1];
            $b = (int) $m[2];
            return $a . '% ~ ' . $b . '%';
        }
        return $raw;
    }
}
