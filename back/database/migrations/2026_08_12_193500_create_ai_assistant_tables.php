<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->string('locale', 5)->default('ka');
            $table->string('status', 30)->default('active');
            $table->unsignedTinyInteger('lead_score')->default(0);
            $table->foreignId('contact_lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('privacy_accepted_at')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'last_message_at']);
        });

        Schema::create('ai_messages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('ai_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role', 20);
            $table->text('content');
            $table->string('model')->nullable();
            $table->string('tool_name')->nullable();
            $table->json('tool_payload')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->timestamps();

            $table->index(['ai_conversation_id', 'created_at']);
        });

        Schema::create('ai_feedback', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_message_id')->unique()->constrained()->cascadeOnDelete();
            $table->smallInteger('rating');
            $table->text('comment')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('ai_knowledge_items', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('category', 80)->default('general');
            $table->string('locale', 5)->default('ka');
            $table->string('status', 30)->default('approved');
            $table->string('source_type', 30)->default('manual');
            $table->string('source_reference')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'locale', 'category']);
        });

        Schema::create('ai_knowledge_candidates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->text('question');
            $table->text('suggested_answer');
            $table->string('locale', 5)->default('ka');
            $table->string('status', 30)->default('pending');
            $table->unsignedInteger('occurrences')->default(1);
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_candidates');
        Schema::dropIfExists('ai_knowledge_items');
        Schema::dropIfExists('ai_feedback');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
    }
};
