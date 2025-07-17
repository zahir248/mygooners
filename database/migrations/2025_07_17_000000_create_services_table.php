<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('location');
            $table->string('pricing');
            $table->string('contact_info');
            $table->string('category');
            $table->json('tags')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->decimal('trust_score', 3, 2)->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->string('status')->default('pending');
            $table->json('images')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}; 