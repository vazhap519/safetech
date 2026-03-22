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
        Schema::create('home_hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('home_hero_title');
$table->text('home_hero_description');
$table->json('home_hero_list');
 $table->string('home_hero_call_button_text');
 $table->string('home_hero_call_button_number');
 $table->string('home_hero_service_button_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_hero_sections');
    }
};
