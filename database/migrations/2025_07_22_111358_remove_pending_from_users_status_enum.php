<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Added this import for DB facade

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any users with 'pending' status to 'active'
        DB::table('users')->where('status', 'pending')->update(['status' => 'active']);
        
        // Then modify the enum to remove 'pending'
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'suspended') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'pending' back to the enum
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'pending', 'suspended') DEFAULT 'active'");
    }
};
