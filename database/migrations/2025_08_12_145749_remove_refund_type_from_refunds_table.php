<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            // First, update existing records to have a default refund_type
            DB::statement("UPDATE refunds SET refund_type = 'return_refund' WHERE refund_type IS NULL OR refund_type NOT IN ('return_refund')");
            
            // Then remove the column
            $table->dropColumn('refund_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->enum('refund_type', ['return_refund'])->default('return_refund')->after('user_id');
        });
    }
};
