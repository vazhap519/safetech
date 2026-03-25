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
        Schema::create('post_sections', function (Blueprint $table) {
            $table->id();
            // 🔥 RELATION
            $table->foreignId('post_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            // 📄 CONTENT
            $table->string('title')->nullable();
            $table->longText('content')->nullable();

            // ⚠️ order reserved keyword → better rename
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_sections');
    }
};
