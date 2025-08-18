<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing 'proved' values to 'approved'
        DB::table('service_reviews')
            ->where('status', 'proved')
            ->update(['status' => 'approved']);

        // Then modify the enum column to include 'approved' instead of 'proved'
        DB::statement("ALTER TABLE `service_reviews` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to the original enum values
        DB::statement("ALTER TABLE `service_reviews` MODIFY COLUMN `status` ENUM('pending', 'proved', 'rejected') DEFAULT 'pending'");
        
        // Update any 'approved' values back to 'proved'
        DB::table('service_reviews')
            ->where('status', 'approved')
            ->update(['status' => 'proved']);
    }
};
