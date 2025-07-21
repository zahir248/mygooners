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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->decimal('price', 10, 2)->nullable(); // Override product price
            $table->decimal('sale_price', 10, 2)->nullable(); // Override product sale price
            $table->integer('stock_quantity')->default(0);
            $table->json('attribute_values'); // Store selected attribute values
            $table->json('images')->nullable(); // Variation-specific images
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
