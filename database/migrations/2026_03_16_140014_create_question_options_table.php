<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exam_question_id');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('seq')->default(0);
            $table->timestamps();

            $table->foreign('exam_question_id')->references('id')->on('exam_questions')->onDelete('cascade');
            $table->index(['exam_question_id', 'is_correct']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
