<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quote_catalog_price_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quote_catalog_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('supplier')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('markup_percentage', 6, 2)->default(0);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->timestamp('effective_at');
            $table->timestamps();
            $table->index(['quote_catalog_item_id', 'effective_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_catalog_price_histories');
    }
};
