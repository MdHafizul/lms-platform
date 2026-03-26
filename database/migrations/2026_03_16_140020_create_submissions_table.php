<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('user_id');
            $table->text('submission_text')->nullable();
            $table->string('submission_file')->nullable();
            $table->string('submission_url')->nullable();
            $table->dateTime('submitted_at');
            $table->enum('status', ['PENDING', 'SUBMITTED', 'GRADED'])->default('PENDING')->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['assignment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
