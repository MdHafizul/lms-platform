<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('learning_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['CONTENT', 'ACTIVITY', 'ASSESSMENT'])->index();
            $table->integer('sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('duration_minutes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->index(['course_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_activities');
    }
};
