<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_slt_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id')->unique();
            $table->integer('total_activities')->default(0);
            $table->integer('completed_activities')->default(0);
            $table->integer('total_time_spent_minutes')->default(0);
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->json('summary')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_slt_summary');
    }
};
