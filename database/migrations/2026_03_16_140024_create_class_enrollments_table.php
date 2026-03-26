<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->dateTime('joined_at');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'DROPPED'])->default('ACTIVE')->index();
            $table->timestamps();

            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->unique(['class_id', 'enrollment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
