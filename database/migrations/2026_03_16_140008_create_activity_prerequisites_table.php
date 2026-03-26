<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('prerequisite_activity_id');
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('learning_activities')->onDelete('cascade');
            $table->foreign('prerequisite_activity_id')->references('id')->on('learning_activities')->onDelete('cascade');
            $table->unique(['activity_id', 'prerequisite_activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_prerequisites');
    }
};
