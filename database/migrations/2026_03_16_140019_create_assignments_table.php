<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learning_activity_id')->unique();
            $table->dateTime('due_date')->nullable();
            $table->integer('max_submissions')->nullable();
            $table->text('instructions')->nullable();
            $table->enum('submission_type', ['FILE', 'TEXT', 'URL'])->default('FILE');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->foreign('learning_activity_id')->references('id')->on('learning_activities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
