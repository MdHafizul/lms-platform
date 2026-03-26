<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->string('question_text');
            $table->enum('question_type', ['MULTIPLE_CHOICE', 'SHORT_ANSWER', 'ESSAY', 'TRUE_FALSE'])->index();
            $table->integer('points')->default(1);
            $table->integer('seq')->default(0);
            $table->text('explanation')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('assessment_id')->references('id')->on('assessments')->onDelete('cascade');
            $table->index(['assessment_id', 'seq']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
