<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'COMPLETED', 'DROPPED'])->default('ACTIVE')->index();
            $table->dateTime('enrolled_at');
            $table->dateTime('completed_at')->nullable();
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->unique(['user_id', 'course_id']);
            $table->index(['user_id', 'status']);
            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
