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
        Schema::table('reels', function (Blueprint $table) {
            // Add video_path for storing the final video URL
            if (!Schema::hasColumn('reels', 'video_path')) {
                $table->string('video_path', 500)->nullable()->after('music');
            }

            // Add prediction_id for tracking Replicate API predictions
            if (!Schema::hasColumn('reels', 'prediction_id')) {
                $table->string('prediction_id', 255)->nullable()->unique()->after('video_path');
            }

            // Update status enum to include 'processing'
            // Drop and recreate is required for PostgreSQL compatibility
            try {
                $table->string('status', 50)->change();  // Convert to string first
            } catch (\Exception $e) {
                // If change fails, the column might already be a string
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reels', function (Blueprint $table) {
            if (Schema::hasColumn('reels', 'video_path')) {
                $table->dropColumn('video_path');
            }
            if (Schema::hasColumn('reels', 'prediction_id')) {
                $table->dropColumn('prediction_id');
            }
        });
    }
};
