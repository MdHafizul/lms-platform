<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id')->unique();
            $table->string('certificate_code')->unique();
            $table->string('issued_by')->nullable();
            $table->dateTime('issued_at');
            $table->dateTime('expires_at')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['ISSUED', 'REVOKED', 'EXPIRED'])->default('ISSUED')->index();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
            $table->index('certificate_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
