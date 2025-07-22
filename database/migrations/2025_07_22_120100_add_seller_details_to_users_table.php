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
        Schema::table('users', function (Blueprint $table) {
            $table->string('business_name')->nullable()->after('is_seller');
            $table->string('business_type')->nullable()->after('business_name');
            $table->string('business_registration')->nullable()->after('business_type');
            $table->string('business_address')->nullable()->after('business_registration');
            $table->string('operating_area')->nullable()->after('business_address');
            $table->string('website')->nullable()->after('operating_area');
            $table->string('id_document')->nullable()->after('website');
            $table->string('selfie_with_id')->nullable()->after('id_document');
            $table->integer('years_experience')->nullable()->after('selfie_with_id');
            $table->text('skills')->nullable()->after('years_experience');
            $table->text('service_areas')->nullable()->after('skills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_type',
                'business_registration',
                'business_address',
                'operating_area',
                'website',
                'id_document',
                'selfie_with_id',
                'years_experience',
                'skills',
                'service_areas',
            ]);
        });
    }
}; 