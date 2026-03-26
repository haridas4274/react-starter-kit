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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->string('slug', 191)->unique()->after('name');
            $table->string('status', 32)->default('active')->after('slug');
            $table->string('billing_email')->nullable()->after('status');
            $table->string('phone')->nullable()->after('billing_email');
            $table->string('country_code', 2)->nullable()->after('phone');
            $table->string('timezone')->default('UTC')->after('country_code');
            $table->string('locale', 10)->default('en')->after('timezone');
            $table->string('plan', 32)->default('starter')->after('locale');
            $table->timestamp('trial_ends_at')->nullable()->after('plan');
            $table->index(['status', 'plan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['status', 'plan']);
            $table->dropUnique(['slug']);
            $table->dropColumn([
                'name',
                'slug',
                'status',
                'billing_email',
                'phone',
                'country_code',
                'timezone',
                'locale',
                'plan',
                'trial_ends_at',
            ]);
        });
    }
};
