<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table): void {
            $table->boolean('vat_included')->default(false)->after('discount_percentage');
            $table->decimal('vat_rate', 5, 2)->default(18)->after('vat_included');
            $table->decimal('vat_amount', 12, 2)->default(0)->after('vat_rate');
            $table->date('due_date')->nullable()->after('vat_amount');
            $table->string('payment_status', 24)->default('unpaid')->after('due_date')->index();
            $table->string('bank_name')->nullable()->after('payment_status');
            $table->string('bank_account')->nullable()->after('bank_name');
            $table->string('payment_reference')->nullable()->after('bank_account');
        });
    }

    public function down(): void
    {
        Schema::table('estimates', fn (Blueprint $table) => $table->dropColumn([
            'vat_included', 'vat_rate', 'vat_amount', 'due_date', 'payment_status',
            'bank_name', 'bank_account', 'payment_reference',
        ]));
    }
};
