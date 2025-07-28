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
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['original_product_id']);
            // Then drop the columns
            $table->dropColumn(['is_update_request', 'original_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_update_request')->default(false);
            $table->unsignedBigInteger('original_product_id')->nullable();
            $table->foreign('original_product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
