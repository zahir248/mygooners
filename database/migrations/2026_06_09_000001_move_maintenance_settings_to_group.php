<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Setting::whereIn('key', ['maintenance_mode', 'fpl_module_enabled'])
            ->update(['group' => 'maintenance']);
    }

    public function down(): void
    {
        Setting::whereIn('key', ['maintenance_mode', 'fpl_module_enabled'])
            ->update(['group' => 'general']);
    }
};
