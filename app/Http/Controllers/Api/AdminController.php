<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function auditLogs(Request $request)
    {
        $this->authorize('viewAuditLogs');

        $query = AuditLog::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ], 200);
    }

    public function assignRole(Request $request)
    {
        $this->authorize('assignRole');

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,lecturer,student',
        ]);

        $user = User::findOrFail($validated['user_id']);

        // Remove all roles first
        $user->roles()->sync([]);

        // Assign new role
        $user->assignRole($validated['role']);

        // Record action
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'role_assigned',
            'model' => 'User',
            'model_id' => $user->id,
            'old_values' => json_encode($user->getRoleNames()),
            'new_values' => json_encode([$validated['role']]),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully',
            'data' => $user->load('roles'),
        ], 200);
    }

    public function overrideGrade(Request $request)
    {
        $this->authorize('overrideGrade');

        $validated = $request->validate([
            'submission_id' => 'required|exists:submissions,id',
            'score' => 'required|integer|min:0',
            'reason' => 'required|string',
        ]);

        $submission = \App\Models\Submission::findOrFail($validated['submission_id']);

        $oldScore = $submission->score;

        $submission->update(['score' => $validated['score']]);

        // Record override
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'grade_override',
            'model' => 'Submission',
            'model_id' => $submission->id,
            'old_values' => json_encode(['score' => $oldScore]),
            'new_values' => json_encode(['score' => $validated['score']]),
            'description' => 'Reason: ' . $validated['reason'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Grade overridden successfully',
            'data' => $submission,
        ], 200);
    }
}
