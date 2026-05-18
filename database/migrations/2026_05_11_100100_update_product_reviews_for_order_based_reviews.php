<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure standalone indexes exist for FK columns before dropping
        // the old composite unique index. MySQL may rely on that unique index
        // for FK enforcement if no separate index exists.
        $this->ensureIndex('product_reviews', 'product_reviews_product_id_index', ['product_id']);
        $this->ensureIndex('product_reviews', 'product_reviews_user_id_index', ['user_id']);

        Schema::table('product_reviews', function (Blueprint $table) {
            if (!Schema::hasColumn('product_reviews', 'order_id')) {
                $table->foreignId('order_id')->nullable()->after('user_id')->constrained('orders')->onDelete('cascade');
            }

            // Replace global one-review-per-product rule with per-order-product rule
            if ($this->indexExists('product_reviews', 'product_reviews_product_id_user_id_unique')) {
                $table->dropUnique('product_reviews_product_id_user_id_unique');
            }

            if (!$this->indexExists('product_reviews', 'product_reviews_order_product_user_unique')) {
                $table->unique(['order_id', 'product_id', 'user_id'], 'product_reviews_order_product_user_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            if ($this->indexExists('product_reviews', 'product_reviews_order_product_user_unique')) {
                $table->dropUnique('product_reviews_order_product_user_unique');
            }

            if (Schema::hasColumn('product_reviews', 'order_id')) {
                $table->dropConstrainedForeignId('order_id');
            }

            if (!$this->indexExists('product_reviews', 'product_reviews_product_id_user_id_unique')) {
                $table->unique(['product_id', 'user_id'], 'product_reviews_product_id_user_id_unique');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?',
            [$table, $indexName]
        );

        return (int) ($result->total ?? 0) > 0;
    }

    private function ensureIndex(string $table, string $indexName, array $columns): void
    {
        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $tableBlueprint) use ($columns, $indexName) {
            $tableBlueprint->index($columns, $indexName);
        });
    }
};
