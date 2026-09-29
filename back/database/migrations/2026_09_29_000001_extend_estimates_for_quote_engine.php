<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table): void {
            $table->foreignId('service_id')->nullable()->after('project_type')->constrained('services')->nullOnDelete();
            $table->string('project_title')->nullable()->after('service_id');
            $table->string('location')->nullable()->after('project_title');
            $table->decimal('discount_percentage', 6, 2)->default(0)->after('markup_rate');
            $table->json('configuration')->nullable()->after('manual_items');
            $table->json('component_overrides')->nullable()->after('configuration');
            $table->boolean('pricing_complete')->default(false)->after('component_overrides');
        });
    }

    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('service_id');
            $table->dropColumn([
                'project_title',
                'location',
                'discount_percentage',
                'configuration',
                'component_overrides',
                'pricing_complete',
            ]);
        });
    }
};
