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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['user', 'admin', 'super_admin'])->default('user')->after('password');
            $table->decimal('trust_score', 3, 1)->default(0.0)->after('role');
            $table->boolean('is_verified')->default(false)->after('trust_score');
            $table->text('bio')->nullable()->after('is_verified');
            $table->string('location')->nullable()->after('bio');
            $table->string('phone')->nullable()->after('location');
            $table->string('profile_image')->nullable()->after('phone');
            $table->json('admin_request_data')->nullable()->after('profile_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 
                'trust_score', 
                'is_verified', 
                'bio', 
                'location', 
                'phone', 
                'profile_image', 
                'admin_request_data'
            ]);
        });
    }
};
