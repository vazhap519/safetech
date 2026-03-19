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
        Schema::create('heroes', function (Blueprint $table) {
    $table->id();

    $table->string('badge')->nullable();
    $table->string('title');
    $table->text('subtitle')->nullable();

    // buttons
    $table->string('primary_text')->nullable();
    $table->string('primary_link')->nullable();

    $table->string('secondary_text')->nullable();
    $table->string('secondary_link')->nullable();

    $table->timestamps();
});
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
