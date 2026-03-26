<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('content_activity_id');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('content_activity_id')->references('id')->on('content_activities')->onDelete('cascade');
            $table->index('content_activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_files');
    }
};
