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
        Schema::create('video_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $defaults = [
            'Match Highlights',
            'Player Interviews',
            'Manager Press Conferences',
            'Behind the Scenes',
            'Training Sessions',
            'Fan Content',
            'Analysis',
            'News',
        ];

        $now = now();
        foreach ($defaults as $index => $name) {
            DB::table('video_categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'sort_order' => $index,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('video_categories');
    }
};
