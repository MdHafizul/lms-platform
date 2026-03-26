<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attempt_question_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_attempt_id');
            $table->unsignedBigInteger('exam_question_id');
            $table->text('question_snapshot');
            $table->integer('seq');
            $table->timestamps();

            $table->foreign('exam_attempt_id')->references('id')->on('exam_attempts')->onDelete('cascade');
            $table->foreign('exam_question_id')->references('id')->on('exam_questions')->onDelete('restrict');
            $table->unique(['exam_attempt_id', 'seq']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_question_snapshots');
    }
};
