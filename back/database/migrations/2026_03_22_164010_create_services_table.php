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

            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description');
            $table->text('short_description');
$table->longText('long_description');
            $table->string('phone')->nullable(); // 🔥 ახალი
            $table->string('button_text')->nullable(); // 🔥 ახალი

            $table->json('features')->nullable(); // 🔥 nullable
            $table->json('faq')->nullable(); // 🔥 nullable
            $table->json('seo')->nullable();

            $table->json('problems')->nullable();
            $table->json('results')->nullable();
            $table->json('testimonials')->nullable();
            $table->json('case_study')->nullable();
            $table->string('cta_title')->nullable();
            $table->string('cta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
