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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('share_title')->nullable(); // 🔥 სათაური
            $table->string('footer_copyright_text')->nullable(); // 🔥 სათაური
            $table->longText('footer_brand_text')->nullable();

            $table->json('share_buttons')->nullable(); // 🔥 buttons

$table->json('footer_brand_soc')->nullable();
$table->json('footer_headers')->nullable();
$table->json('footer_contact_area')->nullable();

$table->string('contact_page_number')->nullable();
$table->string('contact_page_why_title')->nullable();
$table->json('contact_page_why_text')->nullable();
            $table->string('contact_page_hero_title')->nullable();
            $table->string('contact_page_hero_text')->nullable();


            $table->string('contact_page_cta_title')->nullable();
$table->string('contact_page_info_title')->nullable();
            $table->string('contact_page_whatsapp')->nullable();
            $table->string('contact_page_viber')->nullable();
            $table->string('contact_page_email')->nullable();
            $table->string('contact_page_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
