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
        Schema::table('articles', function (Blueprint $table) {
            $table->text('twitter_embed')->nullable()->after('youtube_video_id');
            $table->text('facebook_embed')->nullable()->after('twitter_embed');
            $table->text('instagram_embed')->nullable()->after('facebook_embed');
            $table->text('tiktok_embed')->nullable()->after('instagram_embed');
            $table->text('custom_embed')->nullable()->after('tiktok_embed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'twitter_embed',
                'facebook_embed', 
                'instagram_embed',
                'tiktok_embed',
                'custom_embed'
            ]);
        });
    }
};
