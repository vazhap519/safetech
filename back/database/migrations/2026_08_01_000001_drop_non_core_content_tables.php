<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('post_sections');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');

        Schema::dropIfExists('products');
        Schema::dropIfExists('product_filters');
        Schema::dropIfExists('product_categories');

        Schema::dropIfExists('privacy_policies');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Removed modules are intentionally not recreated.
    }
};
