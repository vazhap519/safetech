<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->nullOnDeleteConstraints() as [$table, $column, $references]) {
            $this->replaceForeignConstraint($table, $column, $references, cascade: false);
        }

        foreach ($this->cascadeOnDeleteConstraints() as [$table, $column, $references]) {
            $this->replaceForeignConstraint($table, $column, $references, cascade: true);
        }
    }

    public function down(): void
    {
        // These constraints are deliberately kept safe on rollback. Reverting
        // them to restrictive foreign keys would reintroduce admin delete
        // failures on production databases that originated from older schema
        // revisions.
    }

    /** @return array<int, array{string, string, string}> */
    private function nullOnDeleteConstraints(): array
    {
        return [
            ['services', 'category_for_service_id', 'category_for_services'],
            ['projects', 'category_id', 'project_categories'],
            ['faqs', 'service_id', 'services'],
            ['review_invitations', 'project_id', 'projects'],
            ['review_invitations', 'testimonial_id', 'testimonials'],
            ['ai_conversations', 'contact_lead_id', 'contact_leads'],
            ['ai_knowledge_candidates', 'ai_conversation_id', 'ai_conversations'],
            ['ai_knowledge_candidates', 'reviewed_by', 'users'],
            ['estimates', 'created_by', 'users'],
            ['admin_audits', 'user_id', 'users'],
        ];
    }

    /** @return array<int, array{string, string, string}> */
    private function cascadeOnDeleteConstraints(): array
    {
        return [
            ['ai_messages', 'ai_conversation_id', 'ai_conversations'],
            ['ai_feedback', 'ai_message_id', 'ai_messages'],
            ['local_service_landings', 'service_id', 'services'],
            ['local_service_landing_project', 'landing_id', 'local_service_landings'],
            ['local_service_landing_project', 'project_id', 'projects'],
        ];
    }

    private function replaceForeignConstraint(
        string $table,
        string $column,
        string $references,
        bool $cascade,
    ): void {
        if (! Schema::hasTable($table)
            || ! Schema::hasTable($references)
            || ! Schema::hasColumn($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column): void {
            $blueprint->dropForeign([$column]);
        });

        Schema::table($table, function (Blueprint $blueprint) use (
            $column,
            $references,
            $cascade,
        ): void {
            $foreign = $blueprint->foreign($column)
                ->references('id')
                ->on($references);

            if ($cascade) {
                $foreign->cascadeOnDelete();

                return;
            }

            $foreign->nullOnDelete();
        });
    }
};
