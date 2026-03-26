<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_progression', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->integer('current_activity_index')->default(0);
            $table->enum('progression_status', ['NOT_STARTED', 'IN_PROGRESS', 'COMPLETED', 'BLOCKED'])->default('NOT_STARTED')->index();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('tracking_data')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->unique('enrollment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_progression');
    }
};
