<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fresh installs already have the intended nullable/cascade schema.
        // This migration specifically repairs older PostgreSQL production
        // databases whose foreign keys or nullability may predate those rules.
        if (DB::connection()->getDriverName() !== 'pgsql') {
            return;
        }

        foreach ($this->nullOnDeleteConstraints() as [$table, $column, $references]) {
            $this->repairConstraint($table, $column, $references, 'SET NULL', makeNullable: true);
        }

        foreach ($this->cascadeOnDeleteConstraints() as [$table, $column, $references]) {
            $this->repairConstraint($table, $column, $references, 'CASCADE', makeNullable: false);
        }
    }

    public function down(): void
    {
        // Intentionally irreversible. Restoring restrictive production foreign
        // keys would reintroduce the admin delete failures this repair prevents.
    }

    /** @return array<int, array{string, string, string}> */
    private function nullOnDeleteConstraints(): array
    {
        return [
            ['services', 'category_for_service_id', 'category_for_services'],
            ['projects', 'category_id', 'project_categories'],
            ['faqs', 'service_id', 'services'],
            ['analytics_events', 'service_id', 'services'],
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

    private function repairConstraint(
        string $table,
        string $column,
        string $references,
        string $onDelete,
        bool $makeNullable,
    ): void {
        if (! Schema::hasTable($table)
            || ! Schema::hasTable($references)
            || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $quotedTable = $this->quoteIdentifier($table);
        $quotedColumn = $this->quoteIdentifier($column);
        $quotedReferences = $this->quoteIdentifier($references);

        if ($makeNullable) {
            DB::statement("ALTER TABLE {$quotedTable} ALTER COLUMN {$quotedColumn} DROP NOT NULL");
        }

        // Repair inconsistent legacy rows before recreating the FK. SET NULL
        // relationships preserve the child row; CASCADE relationships discard
        // children whose parent no longer exists.
        if ($makeNullable) {
            DB::statement(
                "UPDATE {$quotedTable} AS child SET {$quotedColumn} = NULL "
                ."WHERE child.{$quotedColumn} IS NOT NULL "
                ."AND NOT EXISTS (SELECT 1 FROM {$quotedReferences} AS parent "
                ."WHERE parent.\"id\" = child.{$quotedColumn})",
            );
        } else {
            DB::statement(
                "DELETE FROM {$quotedTable} AS child "
                ."WHERE NOT EXISTS (SELECT 1 FROM {$quotedReferences} AS parent "
                ."WHERE parent.\"id\" = child.{$quotedColumn})",
            );
        }

        // Do not assume Laravel's conventional constraint name. Production has
        // lived through multiple schema revisions, so remove every FK attached
        // to this exact column before installing the canonical rule.
        $foreignKeys = DB::select(
            <<<'SQL'
                SELECT DISTINCT con.conname
                FROM pg_constraint AS con
                JOIN pg_class AS rel ON rel.oid = con.conrelid
                JOIN pg_namespace AS nsp ON nsp.oid = rel.relnamespace
                JOIN pg_attribute AS att
                  ON att.attrelid = rel.oid
                 AND att.attnum = ANY (con.conkey)
                WHERE con.contype = 'f'
                  AND nsp.nspname = current_schema()
                  AND rel.relname = ?
                  AND att.attname = ?
            SQL,
            [$table, $column],
        );

        foreach ($foreignKeys as $foreignKey) {
            $name = (string) ($foreignKey->conname ?? '');

            if ($name === '') {
                continue;
            }

            DB::statement(
                "ALTER TABLE {$quotedTable} DROP CONSTRAINT IF EXISTS ".$this->quoteIdentifier($name),
            );
        }

        $constraint = $this->quoteIdentifier($table.'_'.$column.'_foreign');
        DB::statement(
            "ALTER TABLE {$quotedTable} ADD CONSTRAINT {$constraint} "
            ."FOREIGN KEY ({$quotedColumn}) REFERENCES {$quotedReferences} (\"id\") ON DELETE {$onDelete}",
        );
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
};
