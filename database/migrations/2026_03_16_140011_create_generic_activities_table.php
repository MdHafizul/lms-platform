<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('generic_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learning_activity_id')->unique();
            $table->text('instructions')->nullable();
            $table->json('configuration')->nullable();
            $table->timestamps();

            $table->foreign('learning_activity_id')->references('id')->on('learning_activities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generic_activities');
    }
};
