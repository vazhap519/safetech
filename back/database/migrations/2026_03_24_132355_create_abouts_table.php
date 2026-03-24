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
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            //Hero Section
            $table->string('hero_title');
        $table->string('hero_description');
        $table->json('hero_trust_list');
        $table->string('hero_badge');

        //Story
        $table->string('story_title');
        $table->string('story_title_description');
        $table->string('story_content');
        $table->json('story_stats');
        //Why Us
        $table->string('why_us_title');
        $table->string('why_us_title_description');
        $table->json('why_us_content');
        //Cta
        $table->string('cta_title');
        $table->string('cta_title_description');
        $table->json('cta_trust');
        $table->string('cta_phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
