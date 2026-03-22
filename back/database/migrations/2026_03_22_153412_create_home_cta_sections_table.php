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
        Schema::create('home_cta_sections', function (Blueprint $table) {
            $table->id();
            $table->string('cta_title');
$table->string('cta_title_hilight');
$table->string('cta_description');
$table->string('cta_phone_btn_number');
$table->string('cta_phone_btn_text');
$table->string('cta_message_button_text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_cta_sections');
    }
};
