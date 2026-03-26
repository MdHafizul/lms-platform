<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attempt_option_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_question_snapshot_id');
            $table->unsignedBigInteger('question_option_id');
            $table->text('option_snapshot');
            $table->integer('seq');
            $table->timestamps();

            $table->foreign('attempt_question_snapshot_id')->references('id')->on('attempt_question_snapshots')->onDelete('cascade');
            $table->foreign('question_option_id')->references('id')->on('question_options')->onDelete('restrict');
            $table->index(['attempt_question_snapshot_id', 'seq']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempt_option_snapshots');
    }
};
