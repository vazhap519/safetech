<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camera_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title', 140);
            $table->string('contact_name', 100);
            $table->string('contact_phone', 24);
            $table->string('contact_email', 160)->nullable();
            $table->string('status', 32)->default('new')->index();
            $table->json('layout');
            $table->string('background_path')->nullable();
            $table->timestamp('privacy_accepted_at');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camera_plans');
    }
};
