<?php

namespace App\Http\Controllers\Api;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\GradingScheme;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assignment::query();

        if ($request->has('course_id')) {
            $query->whereHas('learningActivity', function ($q) {
                $q->where('course_id', request('course_id'));
            });
        }

        $assignments = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Assignment::class);

        $validated = $request->validate([
            'learning_activity_id' => 'required|exists:learning_activities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'total_points' => 'required|integer|min:1',
            'due_date' => 'required|date_format:Y-m-d H:i:s',
            'allow_late_submission' => 'boolean',
            'late_penalty_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $assignment = Assignment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully',
            'data' => $assignment,
        ], 201);
    }

    public function show(Assignment $assignment)
    {
        $assignment->load('learningActivity', 'submissions', 'gradingScheme');

        return response()->json([
            'success' => true,
            'data' => $assignment,
        ], 200);
    }

    public function update(Request $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'sometimes|date_format:Y-m-d H:i:s',
            'allow_late_submission' => 'sometimes|boolean',
            'late_penalty_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $assignment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Assignment updated successfully',
            'data' => $assignment,
        ], 200);
    }

    public function destroy(Request $request, Assignment $assignment)
    {
        $this->authorize('delete', $assignment);

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully',
        ], 200);
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = $request->user();

        // Check enrollment
        $enrollment = $user->enrollments()
            ->whereHas('course', function ($q) use ($assignment) {
                $q->whereHas('learningActivities', function ($q2) use ($assignment) {
                    $q2->where('id', $assignment->learning_activity_id);
                });
            })
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course',
            ], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'file_path' => 'nullable|string',
        ]);

        $isLate = now() > $assignment->due_date;

        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $user->id,
            'content' => $validated['content'],
            'file_path' => $validated['file_path'] ?? null,
            'submitted_at' => now(),
            'is_late' => $isLate,
            'status' => 'submitted',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Assignment submitted successfully',
            'data' => [
                'submission' => $submission,
                'is_late' => $isLate,
            ],
        ], 201);
    }

    public function submissions(Request $request, Assignment $assignment)
    {
        $submissions = $assignment->submissions()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $submissions,
        ], 200);
    }

    public function grade(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $validated = $request->validate([
            'score' => 'required|integer|min:0',
            'feedback' => 'nullable|string',
            'points_deducted' => 'nullable|integer|min:0',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'points_deducted' => $validated['points_deducted'] ?? 0,
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Submission graded successfully',
            'data' => $submission,
        ], 200);
    }
}
