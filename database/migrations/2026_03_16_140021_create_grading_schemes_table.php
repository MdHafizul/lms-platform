<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('grading_schemes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->string('criteria_name');
            $table->integer('max_points');
            $table->text('description')->nullable();
            $table->integer('seq')->default(0);
            $table->timestamps();

            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->index(['assignment_id', 'seq']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_schemes');
    }
};
