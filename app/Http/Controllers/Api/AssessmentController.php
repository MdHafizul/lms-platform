<?php

namespace App\Http\Controllers\Api;

use App\Models\Assessment;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Assessment::query();

        if ($request->has('course_id')) {
            $query->whereHas('learningActivity', function ($q) {
                $q->where('course_id', request('course_id'));
            });
        }

        $assessments = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $assessments,
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Assessment::class);

        $validated = $request->validate([
            'learning_activity_id' => 'required|exists:learning_activities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_points' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'show_answers_after_submit' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
        ]);

        $assessment = Assessment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Assessment created successfully',
            'data' => $assessment,
        ], 201);
    }

    public function show(Assessment $assessment)
    {
        $assessment->load('learningActivity', 'questions');

        return response()->json([
            'success' => true,
            'data' => $assessment,
        ], 200);
    }

    public function update(Request $request, Assessment $assessment)
    {
        $this->authorize('update', $assessment);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'passing_score' => 'sometimes|integer|min:0|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'show_answers_after_submit' => 'sometimes|boolean',
            'allow_multiple_attempts' => 'sometimes|boolean',
        ]);

        $assessment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Assessment updated successfully',
            'data' => $assessment,
        ], 200);
    }

    public function destroy(Request $request, Assessment $assessment)
    {
        $this->authorize('delete', $assessment);

        $assessment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assessment deleted successfully',
        ], 200);
    }

    public function startAttempt(Request $request, Assessment $assessment)
    {
        $user = $request->user();

        // Check if user is enrolled
        $enrollment = $user->enrollments()
            ->whereHas('course', function ($q) use ($assessment) {
                $q->whereHas('learningActivities', function ($q2) use ($assessment) {
                    $q2->where('id', $assessment->learning_activity_id);
                });
            })
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'You are not enrolled in this course',
            ], 403);
        }

        // Check if attempt already in progress
        $inProgress = ExamAttempt::where('user_id', $user->id)
            ->where('assessment_id', $assessment->id)
            ->where('status', 'in_progress')
            ->first();

        if ($inProgress) {
            return response()->json([
                'success' => true,
                'message' => 'Attempt already in progress',
                'data' => $inProgress,
            ], 200);
        }

        // Check if multiple attempts allowed
        if (!$assessment->allow_multiple_attempts) {
            $completed = ExamAttempt::where('user_id', $user->id)
                ->where('assessment_id', $assessment->id)
                ->whereIn('status', ['submitted', 'graded'])
                ->first();

            if ($completed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Multiple attempts not allowed for this assessment',
                ], 422);
            }
        }

        $attempt = ExamAttempt::create([
            'user_id' => $user->id,
            'assessment_id' => $assessment->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $attempt->load('assessment.questions.options');

        return response()->json([
            'success' => true,
            'message' => 'Attempt started successfully',
            'data' => $attempt,
        ], 201);
    }

    public function submitAttempt(Request $request, ExamAttempt $attempt)
    {
        $this->authorize('update', $attempt);

        if ($attempt->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Attempt is not in progress',
            ], 422);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:exam_questions,id',
            'answers.*.selected_option_id' => 'nullable|exists:question_options,id',
            'answers.*.text_answer' => 'nullable|string',
        ]);

        $score = 0;
        $totalPoints = 0;

        foreach ($validated['answers'] as $answer) {
            $question = $attempt->assessment->questions()
                ->find($answer['question_id']);

            if (!$question)
                continue;

            $totalPoints += $question->points;

            // Save answer
            $examAnswer = ExamAnswer::create([
                'exam_attempt_id' => $attempt->id,
                'exam_question_id' => $question->id,
                'selected_option_id' => $answer['selected_option_id'] ?? null,
                'text_answer' => $answer['text_answer'] ?? null,
            ]);

            // Check if correct
            if ($answer['selected_option_id']) {
                $option = $question->options()
                    ->find($answer['selected_option_id']);

                if ($option && $option->is_correct) {
                    $score += $question->points;
                }
            }
        }

        $percentage = $totalPoints > 0 ? round(($score / $totalPoints) * 100, 2) : 0;
        $isPassed = $percentage >= $attempt->assessment->passing_score;

        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'score' => $score,
            'percentage_score' => $percentage,
            'is_passed' => $isPassed,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attempt submitted successfully',
            'data' => [
                'attempt' => $attempt,
                'score' => $score,
                'percentage' => $percentage,
                'passed' => $isPassed,
            ],
        ], 200);
    }

    public function myAttempts(Request $request, Assessment $assessment)
    {
        $attempts = ExamAttempt::where('user_id', $request->user()->id)
            ->where('assessment_id', $assessment->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $attempts,
        ], 200);
    }

    public function gradeAnswer(Request $request, ExamAnswer $answer)
    {
        $this->authorize('update', $answer);

        $validated = $request->validate([
            'feedback' => 'required|string',
            'is_correct' => 'boolean',
        ]);

        $answer->update([
            'feedback' => $validated['feedback'],
            'is_feedback_provided' => true,
        ]);

        if (isset($validated['is_correct'])) {
            $answer->update(['is_correct' => $validated['is_correct']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Answer graded successfully',
            'data' => $answer,
        ], 200);
    }
}
