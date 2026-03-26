<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_activity_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('learning_activity_id');
            $table->dateTime('viewed_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('time_spent_minutes')->nullable();
            $table->enum('status', ['NOT_STARTED', 'IN_PROGRESS', 'COMPLETED'])->default('NOT_STARTED')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->foreign('learning_activity_id')->references('id')->on('learning_activities')->onDelete('cascade');
            $table->unique(['enrollment_id', 'learning_activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_activity_tracking');
    }
};
