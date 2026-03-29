<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // 📞 CONTACT
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            // 📍 ADDRESS
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('GE');

            // 🌍 GEO
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // 🕐 WORK HOURS
            $table->string('open_time')->default('09:00');
            $table->string('close_time')->default('18:00');

            // 🌐 SOCIAL LINKS (SEO-სთვის)
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'email',
                'address',
                'city',
                'country',
                'lat',
                'lng',
                'open_time',
                'close_time',
                'facebook',
                'instagram',
                'linkedin',
            ]);
        });
    }
};
