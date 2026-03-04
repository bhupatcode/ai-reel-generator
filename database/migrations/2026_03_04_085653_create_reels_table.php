<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reels', function (Blueprint $table) {
            $table->id();

            // User inputs
            $table->string('topic', 200);
            $table->string('mood', 50);
            $table->unsignedSmallInteger('duration'); // in seconds

            // AI-generated outputs (stored as JSON)
            $table->json('script')->nullable();
            $table->json('scenes')->nullable();
            $table->json('captions')->nullable();
            $table->string('music', 500)->nullable();

            // Full raw AI response for debugging
            $table->longText('raw_response')->nullable();

            // Status tracking
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
