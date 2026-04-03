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

            $table->foreignId('category_for_service_id')
                ->nullable()
                ->constrained('category_for_services')
                ->nullOnDelete();

            $table->string('slug')->unique();
            $table->string('title');

            $table->text('short_description'); // 🔥 მთავარი ტექსტი
            $table->longText('long_description')->nullable(); // optional

            $table->string('phone')->nullable();
            $table->string('button_text')->default('დაგვიკავშირდი');

            // ✅ მინიმალური dynamic content
            $table->json('features')->nullable(); // max 3-5
            $table->json('faq')->nullable();

            // ✅ SEO untouched
            $table->json('seo')->nullable();

            // 🔥 CTA (ძალიან მნიშვნელოვანი)
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
