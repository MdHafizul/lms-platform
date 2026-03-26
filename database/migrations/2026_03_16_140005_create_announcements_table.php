<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('created_by');
            $table->string('title');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('course_id');
            $table->index('published_at');
            $table->index('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
