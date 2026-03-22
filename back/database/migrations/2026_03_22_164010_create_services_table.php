<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('service_section_title');
 $table->string('service_section_description');
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description');

            $table->string('phone')->nullable(); // 🔥 ახალი
            $table->string('button_text')->nullable(); // 🔥 ახალი

            $table->json('features')->nullable(); // 🔥 nullable
            $table->json('faq')->nullable(); // 🔥 nullable

            $table->text('seo_text')->nullable(); // 🔥 უკეთესი ვიდრე json

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};