<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->integer('total_score')->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->enum('status', ['IN_PROGRESS', 'SUBMITTED', 'GRADED', 'REVIEWED'])->index();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // FK to enrollments added in a separate migration after enrollments table is created
            $table->index(['user_id', 'assessment_id']);
            $table->index(['enrollment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
