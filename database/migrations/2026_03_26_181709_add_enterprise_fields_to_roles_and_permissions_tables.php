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
        Schema::table('roles', function (Blueprint $table) {
            $table->string('key')->nullable()->after('name');
            $table->string('description')->nullable()->after('guard_name');
            $table->boolean('is_system')->default(false)->after('description');
            $table->unsignedInteger('sort_order')->default(0)->after('is_system');
            $table->string('status')->default('active')->after('sort_order');

            $table->unique(['tenant_id', 'key', 'guard_name']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete()->after('id');
            $table->string('key')->nullable()->after('name');
            $table->string('module')->nullable()->after('key');
            $table->string('description')->nullable()->after('guard_name');
            $table->boolean('is_system')->default(false)->after('description');
            $table->string('status')->default('active')->after('is_system');

            $table->unique(['tenant_id', 'key', 'guard_name']);
            $table->index(['tenant_id', 'module', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropUnique(['tenant_id', 'key', 'guard_name']);
            $table->dropColumn(['key', 'description', 'is_system', 'sort_order', 'status']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'module', 'status']);
            $table->dropUnique(['tenant_id', 'key', 'guard_name']);
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn(['key', 'module', 'description', 'is_system', 'status']);
        });
    }
};
