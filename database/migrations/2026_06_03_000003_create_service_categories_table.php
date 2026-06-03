<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $defaults = [
            ['name' => 'Coaching', 'label' => 'Coaching'],
            ['name' => 'Transport', 'label' => 'Pengangkutan'],
            ['name' => 'Authentication', 'label' => 'Pengesahan'],
            ['name' => 'Photography', 'label' => 'Rafi'],
            ['name' => 'Entertainment', 'label' => 'Hiburan'],
            ['name' => 'Catering', 'label' => 'Katering'],
            ['name' => 'Security', 'label' => 'Keselamatan'],
            ['name' => 'Other', 'label' => 'Lain-lain'],
        ];

        $now = now();
        foreach ($defaults as $index => $item) {
            DB::table('service_categories')->insert([
                'name' => $item['name'],
                'label' => $item['label'],
                'slug' => Str::slug($item['name']),
                'sort_order' => $index,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
