<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AdminController;

Route::prefix('v1')->group(function () {

    // =====================
    // AUTH ROUTES (PUBLIC)
    // =====================
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // =====================
    // PROTECTED ROUTES
    // =====================
    Route::middleware('auth:sanctum')->group(function () {

        // AUTH ROUTES
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);

        // =====================
        // COURSES
        // =====================
        Route::apiResource('courses', CourseController::class);
        Route::post('/courses/{course}/publish', [CourseController::class, 'publish']);
        Route::post('/courses/{course}/archive', [CourseController::class, 'archive']);
        Route::get('/courses/{course}/students', [CourseController::class, 'students']);
        Route::get('/courses/{course}/learning-activities', [CourseController::class, 'learningActivities']);
        Route::get('/courses/{course}/enrollments', [CourseController::class, 'enrollments']);

        // =====================
        // ENROLLMENTS
        // =====================
        Route::apiResource('enrollments', EnrollmentController::class);
        Route::post('/enrollments/{enrollment}/complete-enrollment', [EnrollmentController::class, 'completeEnrollment']);
        Route::get('/enrollments/{enrollment}/progress', [EnrollmentController::class, 'progress']);
        Route::get('/enrollments/{enrollment}/certificate', [EnrollmentController::class, 'certificate']);

        // =====================
        // ASSESSMENTS & EXAMS
        // =====================
        Route::apiResource('assessments', AssessmentController::class);
        Route::post('/assessments/{assessment}/start-attempt', [AssessmentController::class, 'startAttempt']);
        Route::post('/exam-attempts/{attempt}/submit', [AssessmentController::class, 'submitAttempt']);
        Route::get('/assessments/{assessment}/my-attempts', [AssessmentController::class, 'myAttempts']);
        Route::post('/exam-answers/{answer}/grade', [AssessmentController::class, 'gradeAnswer']);

        // =====================
        // ASSIGNMENTS
        // =====================
        Route::apiResource('assignments', AssignmentController::class);
        Route::post('/assignments/{assignment}/submit', [AssignmentController::class, 'submit']);
        Route::get('/assignments/{assignment}/submissions', [AssignmentController::class, 'submissions']);
        Route::post('/submissions/{submission}/grade', [AssignmentController::class, 'grade']);

        // =====================
        // ADMIN ONLY
        // =====================
        Route::middleware('role:admin')->group(function () {
            Route::get('/admin/audit-logs', [AdminController::class, 'auditLogs']);
            Route::post('/admin/assign-role', [AdminController::class, 'assignRole']);
            Route::post('/admin/override-grade', [AdminController::class, 'overrideGrade']);
        });
    });
});
