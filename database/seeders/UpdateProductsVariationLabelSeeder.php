<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class UpdateProductsVariationLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all existing products to have a default variation label
        Product::whereNull('variation_label')->update([
            'variation_label' => 'Pilihan Varian'
        ]);

        $this->command->info('Updated existing products with default variation label.');
    }
}
