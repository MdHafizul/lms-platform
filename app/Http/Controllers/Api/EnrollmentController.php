<?php

namespace App\Http\Controllers\Api;

use App\Models\Enrollment;
use App\Models\StudentProgression;
use App\Models\Certificate;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Enrollment::query();

        if ($request->has('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->user()->isStudent()) {
            $query->where('user_id', $request->user()->id);
        }

        $enrollments = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Enrollment::class);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $existing = Enrollment::where('user_id', $validated['user_id'])
            ->where('course_id', $validated['course_id'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'User is already enrolled in this course',
            ], 422);
        }

        $enrollment = Enrollment::create(array_merge($validated, ['status' => 'active']));

        return response()->json([
            'success' => true,
            'message' => 'Enrollment created successfully',
            'data' => $enrollment,
        ], 201);
    }

    public function show(Enrollment $enrollment)
    {
        $this->authorize('view', $enrollment);

        $enrollment->load('user', 'course', 'progression', 'certificate');

        return response()->json([
            'success' => true,
            'data' => $enrollment,
        ], 200);
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $this->authorize('update', $enrollment);

        $validated = $request->validate([
            'status' => 'sometimes|in:active,completed,suspended',
            'current_grade' => 'nullable|numeric|min:0|max:100',
        ]);

        $enrollment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment updated successfully',
            'data' => $enrollment,
        ], 200);
    }

    public function destroy(Request $request, Enrollment $enrollment)
    {
        $this->authorize('delete', $enrollment);

        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment deleted successfully',
        ], 200);
    }

    public function progress(Request $request, Enrollment $enrollment)
    {
        $this->authorize('view', $enrollment);

        $progression = $enrollment->progression;

        return response()->json([
            'success' => true,
            'data' => $progression,
        ], 200);
    }

    public function certificate(Request $request, Enrollment $enrollment)
    {
        $this->authorize('view', $enrollment);

        $certificate = $enrollment->certificate;

        if (!$certificate) {
            return response()->json([
                'success' => false,
                'message' => 'No certificate issued yet',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $certificate,
        ], 200);
    }

    public function completeEnrollment(Request $request, Enrollment $enrollment)
    {
        $this->authorize('update', $enrollment);

        $enrollment->update(['status' => 'completed']);

        // Create certificate
        $certificate = Certificate::create([
            'enrollment_id' => $enrollment->id,
            'issued_at' => now(),
            'valid_until' => now()->addYears(1),
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment completed and certificate issued',
            'data' => [
                'enrollment' => $enrollment,
                'certificate' => $certificate,
            ],
        ], 200);
    }
}
