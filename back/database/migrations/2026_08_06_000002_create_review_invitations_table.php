<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_invitations', function (Blueprint $table): void {
            $table->id();
            $table->string('token', 96)->unique();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('recipient_name')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->foreignId('testimonial_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_invitations');
    }
};
