<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffLog;
use App\Models\TeamLeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StaffChangeController extends Controller
{
    /**
     * Get all staff for selection
     */
    public function getAllStaff(Request $request)
    {
        try {
            $staff = Staff::select('staff_id', 'name', 'role', 'position', 'department', 'group', 'team_leader_id', 'rank', 'warning_letter')
                ->orderBy('name')
                ->get();

            return response()->json($staff);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load staff: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get all team leaders for selection
     */
    public function getTeamLeaders(Request $request)
    {
        try {
            $teamLeaders = TeamLeader::select('staff_id', 'name', 'group', 'department')
                ->orderBy('name')
                ->get();

            return response()->json($teamLeaders);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load team leaders: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Transfer staff to different division (includes changing team leader)
     */
    public function transferDivision(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string|exists:staff,staff_id',
            'new_department' => 'required|string',
            'new_team_leader_id' => 'required|string|exists:staff,staff_id',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            DB::beginTransaction();

            $staff = Staff::where('staff_id', $request->staff_id)->first();

            // Verify new team leader has role='tl'
            $newTL = Staff::where('staff_id', $request->new_team_leader_id)
                ->where('role', 'tl')
                ->first();

            if (!$newTL) {
                return response()->json(['error' => 'Selected team leader is invalid'], 400);
            }

            // Store old values
            $oldValue = [
                'department' => $staff->department,
                'team_leader_id' => $staff->team_leader_id,
            ];

            $newValue = [
                'department' => $request->new_department,
                'team_leader_id' => $request->new_team_leader_id,
            ];

            // Update staff
            $staff->department = $request->new_department;
            $staff->team_leader_id = $request->new_team_leader_id;
            $staff->save();

            // Create log
            StaffLog::create([
                'staff_id' => $request->staff_id,
                'change_type' => 'division_transfer',
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'changed_by' => $request->header('X-Admin-Phone', 'admin'),
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Division transfer completed successfully',
                'staff' => $staff
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transfer failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Promote staff to Team Leader
     */
    public function promoteToTL(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string|exists:staff,staff_id',
            'group' => 'required|string',
            'first_day_tl' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            DB::beginTransaction();

            $staff = Staff::where('staff_id', $request->staff_id)->first();

            // Check if already TL
            if ($staff->role === 'tl') {
                return response()->json(['error' => 'Staff is already a Team Leader'], 400);
            }

            // Store old values
            $oldValue = [
                'role' => $staff->role,
                'position' => $staff->position,
            ];

            $newValue = [
                'role' => 'tl',
                'position' => 'DC TL',
                'group' => $request->group,
                'first_day_tl' => $request->first_day_tl,
            ];

            // Update staff to TL
            $staff->role = 'tl';
            $staff->position = 'DC TL';
            $staff->group = $request->group;
            $staff->first_day_tl = $request->first_day_tl;
            $staff->save();

            // Create log
            StaffLog::create([
                'staff_id' => $request->staff_id,
                'change_type' => 'promotion',
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'changed_by' => $request->header('X-Admin-Phone', 'admin'),
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Promotion to Team Leader completed successfully',
                'staff' => $staff
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Promotion failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Change staff rank
     */
    public function changeRank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string|exists:staff,staff_id',
            'new_rank' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            DB::beginTransaction();

            $staff = Staff::where('staff_id', $request->staff_id)->first();

            // Store old values
            $oldValue = [
                'rank' => $staff->rank,
            ];

            $newValue = [
                'rank' => $request->new_rank,
            ];

            // Update rank
            $staff->rank = $request->new_rank;
            $staff->save();

            // Create log
            StaffLog::create([
                'staff_id' => $request->staff_id,
                'change_type' => 'rank_change',
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'changed_by' => $request->header('X-Admin-Phone', 'admin'),
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Rank change completed successfully',
                'staff' => $staff
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Rank change failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update warning letter
     */
    public function updateWarningLetter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string|exists:staff,staff_id',
            'warning_letter' => 'required|in:1st,2nd,3rd',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        try {
            DB::beginTransaction();

            $staff = Staff::where('staff_id', $request->staff_id)->first();

            // Store old values
            $oldValue = [
                'warning_letter' => $staff->warning_letter,
            ];

            $newValue = [
                'warning_letter' => $request->warning_letter,
            ];

            // Update warning letter
            $staff->warning_letter = $request->warning_letter;
            $staff->save();

            // Create log
            StaffLog::create([
                'staff_id' => $request->staff_id,
                'change_type' => 'warning_letter',
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'changed_by' => $request->header('X-Admin-Phone', 'admin'),
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Warning letter updated successfully',
                'staff' => $staff
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Warning letter update failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get staff change logs
     */
    public function getStaffLogs(Request $request, $staffId = null)
    {
        try {
            $query = StaffLog::with('staff:staff_id,name,role,position')
                ->orderBy('created_at', 'desc');

            if ($staffId) {
                $query->where('staff_id', $staffId);
            }

            $logs = $query->get();

            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load logs: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get a single staff detail with recent logs
     */
    public function getStaffDetail(Request $request, $staffId)
    {
        try {
            $staff = Staff::where('staff_id', $staffId)->first();

            if (!$staff) {
                return response()->json(['error' => 'Staff not found'], 404);
            }

            $recentLogs = StaffLog::where('staff_id', $staffId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'staff' => $staff,
                'recent_logs' => $recentLogs
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load staff detail: ' . $e->getMessage()], 500);
        }
    }
}
