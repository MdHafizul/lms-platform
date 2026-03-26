<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('content_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('learning_activity_id')->unique();
            $table->text('content');
            $table->string('content_type')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('learning_activity_id')->references('id')->on('learning_activities')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_activities');
    }
};
