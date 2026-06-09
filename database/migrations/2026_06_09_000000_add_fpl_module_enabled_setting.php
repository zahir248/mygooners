<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::updateOrCreate(
            ['key' => 'fpl_module_enabled'],
            [
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'maintenance',
                'description' => 'Enable or disable Fantasy Premier League module across the website',
            ]
        );
    }

    public function down(): void
    {
        Setting::where('key', 'fpl_module_enabled')->delete();
    }
};
