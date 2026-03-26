<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecturer_id');
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->string('code')->unique();
            $table->integer('duration_hours')->default(0);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->index();
            $table->integer('credit_hours')->nullable();
            $table->text('learning_outcomes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('lecturer_id')->references('id')->on('lecturer_profiles')->onDelete('restrict');
            $table->index(['lecturer_id', 'status']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
