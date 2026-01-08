<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'writer' role
        // Note: MySQL requires raw SQL to modify enum values
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'super_admin', 'writer') DEFAULT 'user'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values (remove 'writer')
        // Note: This will fail if any users have 'writer' role, so handle with care
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'super_admin') DEFAULT 'user'");
    }
};
