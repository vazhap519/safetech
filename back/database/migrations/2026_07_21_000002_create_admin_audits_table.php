<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_audits', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 32);
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('label')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_audits');
    }
};
