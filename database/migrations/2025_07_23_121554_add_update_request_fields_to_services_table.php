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
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_update_request')->default(false)->after('status');
            $table->unsignedBigInteger('original_service_id')->nullable()->after('is_update_request');
            $table->foreign('original_service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['original_service_id']);
            $table->dropColumn(['is_update_request', 'original_service_id']);
        });
    }
};
