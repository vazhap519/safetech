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
            $table->json('share_buttons')->nullable(); // 🔥 buttons


            $table->text('footer_description')->nullable();
            $table->string('footer_copyright')->nullable();
            $table->json('footer_contact_info')->nullable();
$table->json('footer_socials')->nullable();
$table->json('footer_headers')->nullable();
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
