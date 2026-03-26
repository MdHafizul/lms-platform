<?php

namespace App\Http\Controllers\Api;

use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\LearningActivity;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('lecturer_id')) {
            $query->where('lecturer_id', $request->lecturer_id);
        }

        $courses = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $courses,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Course::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|unique:courses',
            'duration_hours' => 'nullable|integer',
            'credit_hours' => 'nullable|integer',
            'learning_outcomes' => 'nullable|string',
        ]);

        $lecturer = $request->user()->lecturerProfile;

        if (!$lecturer) {
            return response()->json([
                'success' => false,
                'message' => 'Only lecturers can create courses',
            ], 403);
        }

        $course = Course::create(array_merge(
            $validated,
            ['lecturer_id' => $lecturer->id, 'status' => 'draft']
        ));

        return response()->json([
            'success' => true,
            'message' => 'Course created successfully',
            'data' => $course,
        ], 201);
    }

    public function show(Course $course)
    {
        $course->load('lecturer', 'enrollments', 'learningActivities');
        return response()->json([
            'success' => true,
            'data' => $course,
        ], 200);
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'duration_hours' => 'nullable|integer',
            'credit_hours' => 'nullable|integer',
            'learning_outcomes' => 'nullable|string',
        ]);

        $course->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Course updated successfully',
            'data' => $course,
        ], 200);
    }

    public function destroy(Request $request, Course $course)
    {
        $this->authorize('delete', $course);

        $course->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course deleted successfully',
        ], 200);
    }

    public function publish(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        if ($course->status === 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Course is already published',
            ], 422);
        }

        $course->publish();

        return response()->json([
            'success' => true,
            'message' => 'Course published successfully',
            'data' => $course,
        ], 200);
    }

    public function archive(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $course->archive();

        return response()->json([
            'success' => true,
            'message' => 'Course archived successfully',
            'data' => $course,
        ], 200);
    }

    public function students(Course $course)
    {
        $students = $course->students()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $students,
        ], 200);
    }

    public function learningActivities(Course $course)
    {
        $activities = $course->learningActivities()
            ->orderBy('sequence')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $activities,
        ], 200);
    }

    public function enrollments(Course $course)
    {
        $enrollments = $course->enrollments()->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ], 200);
    }
}
