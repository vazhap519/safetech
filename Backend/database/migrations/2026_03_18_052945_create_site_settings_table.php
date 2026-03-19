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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
                        $table->string('site_name');

            //HomePage Headers
            $table->string('our_services');
            $table->string('recent_projets');
            $table->string('client_reviews');
            $table->string('latest_articles');
            $table->string('get_consultation');
            //TopBar
              $table->string('top_bar_consultation_text');
                $table->string('top_bar_number');
                //Navigation 
                $table->string('navigation_services');
                 $table->string('navigation_projects');
                  $table->string('navigation_about');
                   $table->string('navigation_blog');
                    $table->string('navigation_contact');
            //Footer Headers
            $table->string('footer_services');
              $table->string('footer_contact');
                $table->string('footer_company');
                $table->longtext('footer_description');
                $table->json('styles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
