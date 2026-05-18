<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'is_received')) {
                $table->boolean('is_received')->default(false)->after('delivered_at');
            }

            if (!Schema::hasColumn('orders', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('is_received');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('orders', 'received_at')) {
                $columnsToDrop[] = 'received_at';
            }

            if (Schema::hasColumn('orders', 'is_received')) {
                $columnsToDrop[] = 'is_received';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
