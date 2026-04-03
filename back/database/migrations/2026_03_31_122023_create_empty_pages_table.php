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
        Schema::create('empty_pages', function (Blueprint $table) {
            $table->id();

            $table->json('socials')->nullable(); // 🔥 მთავარი ნაწილი
$table->longText('title')->nullable();
$table->longText('description')->nullable();
$table->longText('coming_soon')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empty_pages');
    }
};
