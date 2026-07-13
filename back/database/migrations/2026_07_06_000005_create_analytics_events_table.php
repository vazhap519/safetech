<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 64)->index();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_slug')->nullable()->index();
            $table->string('page_path', 255)->nullable()->index();
            $table->string('locale', 10)->nullable()->index();
            $table->string('visitor_hash', 64)->index();
            $table->string('ip_hash', 64)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
