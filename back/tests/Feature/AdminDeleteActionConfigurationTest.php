<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminDeleteActionConfigurationTest extends TestCase
{
    #[DataProvider('deletableTableProvider')]
    public function test_deletable_admin_tables_expose_direct_and_bulk_delete_actions(string $relativePath): void
    {
        $source = file_get_contents(base_path($relativePath));

        $this->assertIsString($source);
        $this->assertStringContainsString('DeleteAction::make(', $source);
        $this->assertStringContainsString('DeleteBulkAction::make(', $source);
    }

    /** @return array<string, array{string}> */
    public static function deletableTableProvider(): array
    {
        return [
            'shared categories' => ['app/Filament/Support/CategoryTable.php'],
            'projects' => ['app/Filament/Resources/ProjectResource.php'],
            'testimonials' => ['app/Filament/Resources/TestimonialResource.php'],
            'contact leads' => ['app/Filament/Resources/ContactLeadResource.php'],
            'review invitations' => ['app/Filament/Resources/ReviewInvitationResource.php'],
            'FAQs' => ['app/Filament/Resources/FaqResource.php'],
            'partners' => ['app/Filament/Resources/PartnerResource.php'],
            'pages' => ['app/Filament/Resources/PageResource.php'],
            'team members' => ['app/Filament/Resources/TeamMemberResource.php'],
            'AI conversations' => ['app/Filament/Resources/AiConversationResource.php'],
            'AI knowledge candidates' => ['app/Filament/Resources/AiKnowledgeCandidateResource.php'],
            'AI knowledge items' => ['app/Filament/Resources/AiKnowledgeItemResource.php'],
        ];
    }
}
