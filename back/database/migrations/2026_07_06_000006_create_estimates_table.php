<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_number')->unique();
            $table->string('client_name')->nullable();
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('project_type', 32)->default('cctv');
            $table->string('camera_type', 32)->default('ip');
            $table->unsignedInteger('camera_count')->default(0);
            $table->decimal('camera_megapixels', 6, 2)->default(4);
            $table->unsignedTinyInteger('frames_per_second')->default(15);
            $table->unsignedTinyInteger('recording_hours_per_day')->default(24);
            $table->unsignedSmallInteger('recording_days')->default(30);
            $table->decimal('camera_unit_cost', 12, 2)->default(0);
            $table->decimal('recorder_unit_cost', 12, 2)->default(0);
            $table->decimal('cable_meters', 12, 2)->default(0);
            $table->decimal('cable_unit_cost', 12, 2)->default(0);
            $table->decimal('trunking_meters', 12, 2)->default(0);
            $table->decimal('trunking_unit_cost', 12, 2)->default(0);
            $table->decimal('installation_cost', 12, 2)->default(0);
            $table->decimal('hdd_cost_per_tb', 12, 2)->default(0);
            $table->decimal('poe_switch_unit_cost', 12, 2)->default(0);
            $table->decimal('connector_unit_cost', 12, 2)->default(0);
            $table->decimal('markup_rate', 8, 4)->default(0.6);
            $table->decimal('required_storage_tb', 12, 2)->default(0);
            $table->decimal('cost_total', 12, 2)->default(0);
            $table->decimal('markup_total', 12, 2)->default(0);
            $table->decimal('final_total', 12, 2)->default(0);
            $table->decimal('profit_total', 12, 2)->default(0);
            $table->json('manual_items')->nullable();
            $table->json('calculation')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
