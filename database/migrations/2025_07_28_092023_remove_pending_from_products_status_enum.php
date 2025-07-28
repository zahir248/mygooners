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
        // First, update any existing pending products to active
        DB::table('products')->where('status', 'pending')->update(['status' => 'active']);
        
        // Then modify the enum to remove 'pending'
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive', 'rejected') NOT NULL DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'pending' back to the enum
        DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive', 'pending', 'rejected') NOT NULL DEFAULT 'active'");
    }
};
