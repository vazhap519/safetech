<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_leads', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->string('first_name', 60)->nullable();
            $table->string('last_name', 60)->nullable();
            $table->string('company')->nullable();
            $table->string('phone', 24)->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('service')->nullable();
            $table->string('project_size', 80)->nullable();
            $table->string('property_type', 100)->nullable();
            $table->text('message')->nullable();
            $table->string('source', 80)->index();
            $table->string('status', 30)->default('new')->index();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('privacy_accepted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_leads');
    }
};
