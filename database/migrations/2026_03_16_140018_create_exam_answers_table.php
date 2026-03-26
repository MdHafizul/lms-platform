<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_attempt_id');
            $table->unsignedBigInteger('exam_question_id');
            $table->text('answer')->nullable();
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->integer('score')->nullable();
            $table->enum('feedback_status', ['PENDING', 'GRADED', 'REVIEWED'])->default('PENDING')->index();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->foreign('exam_attempt_id')->references('id')->on('exam_attempts')->onDelete('cascade');
            $table->foreign('exam_question_id')->references('id')->on('exam_questions')->onDelete('restrict');
            $table->foreign('selected_option_id')->references('id')->on('question_options')->onDelete('set null');
            $table->unique(['exam_attempt_id', 'exam_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
