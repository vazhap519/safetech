<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_catalog_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('component_key');
            $table->string('name');
            $table->string('category', 64)->default('other');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('markup_percentage', 6, 2)->default(60);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->unsignedSmallInteger('warranty_months')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['service_id', 'component_key']);
            $table->index(['service_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_catalog_items');
    }
};
