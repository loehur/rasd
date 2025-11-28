<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
// Intervention Image is optional; we'll fallback to GD if not installed

class AttendanceController extends Controller
{
    /**
     * Display a listing of all attendances with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $reportDay = $request->get('report_day');

        $query = Attendance::with(['staff', 'teamLeader'])
            ->orderBy('created_at', 'desc');

        if (!empty($reportDay)) {
            $query->whereDate('report_day', $reportDay);
        }

        $attendances = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendances->items(),
            'pagination' => [
                'total' => $attendances->total(),
                'per_page' => $attendances->perPage(),
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'from' => $attendances->firstItem(),
                'to' => $attendances->lastItem(),
            ],
        ], 200);
    }

    /**
     * Get staff under this team leader
     */
    public function getStaffByTeamLeader(Request $request)
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

            // Get staff under this team leader using the superior name
            $staff = Staff::where('superior', $teamLeader->name)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $staff,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get staff detail by ID
     */
    public function getStaffDetail($staffId)
    {
        $staff = Staff::where('staff_id', $staffId)->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $staff,
        ], 200);
    }

    /**
     * Store a newly created attendance
     */
    public function store(Request $request)
    {
        // Get token from Authorization header
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Remove 'Bearer ' prefix if present
        $token = str_replace('Bearer ', '', $token);

        // Decode token to get employee_id
        $decoded = base64_decode($token);
        $parts = explode(':', $decoded);
        $teamLeaderId = $parts[0] ?? null;

        if (!$teamLeaderId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'work_status' => 'required|in:WFH,Onsite',
            'staff_id' => 'required|exists:staff,staff_id',
            'name' => 'required|string',
            'position' => 'required|string',
            'superior' => 'required|string',
            'department' => 'required|string',
            'device' => 'required|in:Mobile,PC',
            'hire_date' => 'required|date',
            'rank' => 'required|string',
            'report_day' => 'required|date',
            'ranking_intervals' => 'required|in:Top 5%,5% ~ 25%,25% ~ 50%,50% ~ 70%,70% ~ 90%,Bottom 10%',
            'group' => 'required|string',
            'status_code' => 'required|string',
            // Proof is now optional
            'proof' => 'nullable|file|mimes:jpeg,jpg,png|max:10240',
            'reason_for_resign' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Handle file upload and compression
            $proofPath = null;
            if ($request->hasFile('proof')) {
                $proofPath = $this->compressAndSaveImage($request->file('proof'));
            }

            $attendance = Attendance::create([
                'work_status' => $request->work_status,
                'staff_id' => $request->staff_id,
                'name' => $request->name,
                'position' => $request->position,
                'superior' => $request->superior,
                'department' => $request->department,
                'hire_date' => $request->hire_date,
                'rank' => $request->rank,
                'device' => $request->device,
                'report_day' => $request->report_day,
                'ranking_intervals' => $request->ranking_intervals,
                'group' => $request->group,
                'reason_for_resign' => $request->reason_for_resign,
                'proof' => $proofPath,
                'status_code' => $request->status_code,
                'team_leader_id' => $teamLeaderId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance created successfully',
                'data' => $attendance,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified attendance
     */
    public function show($id)
    {
        $attendance = Attendance::with(['staff', 'teamLeader'])->find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ], 200);
    }

    /**
     * Update the specified attendance
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'work_status' => 'sometimes|required|in:WFH,Onsite',
            'staff_id' => 'sometimes|required|exists:staff,staff_id',
            'name' => 'sometimes|required|string',
            'position' => 'sometimes|required|string',
            'department' => 'sometimes|required|string',
            'device' => 'sometimes|required|in:Mobile,PC',
            'hire_date' => 'sometimes|required|date',
            'report_day' => 'sometimes|required|date',
            'status_code' => 'sometimes|required|string',
            'proof' => 'nullable|image|mimes:jpeg,jpg,png|max:10240',
            'ranking_intervals' => 'nullable|string',
            'reason_for_resign' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Handle file upload and compression if new file is provided
            if ($request->hasFile('proof')) {
                // Delete old file if exists
                if ($attendance->proof) {
                    $oldFullPath = base_path('public') . DIRECTORY_SEPARATOR . ltrim($attendance->proof, DIRECTORY_SEPARATOR);
                    if (file_exists($oldFullPath)) {
                        unlink($oldFullPath);
                    }
                }

                $proofPath = $this->compressAndSaveImage($request->file('proof'));
                $attendance->proof = $proofPath;
            }

            $attendance->update($request->except(['proof']));

            return response()->json([
                'success' => true,
                'message' => 'Attendance updated successfully',
                'data' => $attendance,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attendance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified attendance
     */
    public function destroy(Request $request, $id)
    {
        $role = $request->header('X-Role');
        if (!in_array($role, ['admin', 'super-admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        $attendance = Attendance::find($id);
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance not found',
            ], 404);
        }

        // Delete proof file if exists
        if ($attendance->proof) {
            $oldFullPath = base_path('public') . DIRECTORY_SEPARATOR . ltrim($attendance->proof, DIRECTORY_SEPARATOR);
            if (file_exists($oldFullPath)) {
                @unlink($oldFullPath);
            }
        }

        $attendance->delete();
        $this->logAction($request, 'attendance_delete', ['attendance_id' => $id]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance deleted',
        ]);
    }

    /**
     * Compress and save image to max 200KB
     */
    private function compressAndSaveImage($file)
    {
        // Generate unique filename (always save as JPG for uniform compression)
        $filename = 'proof_' . time() . '_' . uniqid() . '.jpg';
        $path = 'uploads/attendance_proofs/' . $filename;
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
