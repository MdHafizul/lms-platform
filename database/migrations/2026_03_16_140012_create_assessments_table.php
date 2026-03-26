<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learning_activity_id')->unique();
            $table->enum('assessment_type', ['EXAM', 'QUIZ', 'ASSIGNMENT'])->index();
            $table->enum('grading_type', ['AUTO', 'MANUAL', 'HYBRID'])->default('AUTO');
            $table->integer('total_questions')->default(0);
            $table->integer('pass_score')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->foreign('learning_activity_id')->references('id')->on('learning_activities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessments');
    }
};
