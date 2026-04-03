<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
Schema::create('project_categories', function (Blueprint $table) {
$table->id();

/* =========================
BASIC
========================= */
$table->string('name');
$table->string('slug')->unique();

/* =========================
OPTIONAL
========================= */
$table->string('icon')->nullable(); // future UI
$table->integer('sort_order')->default(0);

/* =========================
SEO (optional)
========================= */
$table->json('seo')->nullable();
$table->timestamps();

/* =========================
INDEXES
========================= */
$table->index('slug');
});
}

public function down(): void
{
Schema::dropIfExists('project_categories');
}
};
