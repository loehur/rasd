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

                // Get team leader info
                $teamLeader = DB::table('team_leaders')
                    ->where('employee_id', $employeeId)
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
            $perPage = (int) $request->query('per_page', 15);
            $reportDay = $request->query('report_day');

            // Base query for resignations (include all, not limited to current team leader)
            $query = DB::table('staff_resignation_log');

            // Apply date filter if provided
            if ($reportDay) {
                $query->whereDate('report_day', $reportDay);
            }

            // Get total count
            $total = $query->count();

            // Get paginated results
            $resignations = $query
                ->orderBy('created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Enrich with staff details
            foreach ($resignations as $resignation) {
                $staff = DB::table('staff')
                    ->where('staff_id', $resignation->staff_id)
                    ->first();

                $resignation->staff_name = $staff ? $staff->name : 'Unknown';
                $resignation->staff_position = $staff ? $staff->position : null;
                $resignation->staff_superior = $staff ? $staff->superior : null;
            }

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

            // Get team leader info
            $teamLeader = DB::table('team_leaders')
                ->where('employee_id', $employeeId)
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
                'last_working_day' => 'required|date',
                'resignation_type' => 'required|in:voluntary,involuntary',
                'resignation_subtype' => 'required|in:personal_reason,management_reason',
                'report_day' => 'required|date',
                'reason' => 'required|string|max:1000',
                'proof' => 'nullable|file|mimes:jpeg,jpg,png|max:10240',
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
            if ($staff->superior !== $teamLeader->name) {
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

                // Insert resignation log
                DB::table('staff_resignation_log')->insert([
                    'staff_id' => $request->staff_id,
                    'submitted_by' => $teamLeader->employee_id,
                    'resignation_type' => $request->resignation_type,
                    'resignation_subtype' => $request->resignation_subtype,
                    'last_working_day' => $request->last_working_day,
                    'report_day' => $request->report_day,
                    'reason' => $request->reason,
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

            // Get team leader info
            $teamLeader = DB::table('team_leaders')
                ->where('employee_id', $employeeId)
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
                ->where('submitted_by', $teamLeader->employee_id)
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
}
