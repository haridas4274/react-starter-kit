<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! $this->isMySqlFamily()) {
            return;
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tenant_id')) {
            if ($this->hasIndex('users', 'users_tenant_id_email_index')) {
                DB::statement('ALTER TABLE users DROP INDEX users_tenant_id_email_index');
            }
            if ($this->hasIndex('users', 'users_tenant_id_index')) {
                DB::statement('ALTER TABLE users DROP INDEX users_tenant_id_index');
            }

            DB::statement('ALTER TABLE users MODIFY tenant_id VARCHAR(36) NULL');
            DB::statement('ALTER TABLE users ADD INDEX users_tenant_id_index (tenant_id)');
        }

        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'tenant_id')) {
            $this->dropIndexIfExists('roles', 'roles_team_foreign_key_index');
            $this->dropIndexIfExists('roles', 'roles_tenant_id_name_guard_name_unique');
            $this->dropIndexIfExists('roles', 'roles_tenant_id_key_guard_name_unique');
            $this->dropIndexIfExists('roles', 'roles_tenant_id_status_index');

            DB::statement('ALTER TABLE roles MODIFY tenant_id VARCHAR(36) NULL');

            DB::statement('ALTER TABLE roles ADD INDEX roles_team_foreign_key_index (tenant_id)');
            DB::statement('ALTER TABLE roles ADD UNIQUE roles_tenant_id_name_guard_name_unique (tenant_id, name, guard_name)');
            DB::statement('ALTER TABLE roles ADD UNIQUE roles_tenant_id_key_guard_name_unique (tenant_id, `key`, guard_name)');
            DB::statement('ALTER TABLE roles ADD INDEX roles_tenant_id_status_index (tenant_id, status)');
        }

        if (Schema::hasTable('permissions') && Schema::hasColumn('permissions', 'tenant_id')) {
            $this->dropIndexIfExists('permissions', 'permissions_tenant_id_key_guard_name_unique');
            $this->dropIndexIfExists('permissions', 'permissions_tenant_id_module_status_index');

            DB::statement('ALTER TABLE permissions MODIFY tenant_id VARCHAR(36) NULL');

            DB::statement('ALTER TABLE permissions ADD UNIQUE permissions_tenant_id_key_guard_name_unique (tenant_id, `key`, guard_name)');
            DB::statement('ALTER TABLE permissions ADD INDEX permissions_tenant_id_module_status_index (tenant_id, module, status)');
        }

        if (Schema::hasTable('model_has_roles') && Schema::hasColumn('model_has_roles', 'tenant_id')) {
            if ($this->hasPrimary('model_has_roles')) {
                DB::statement('ALTER TABLE model_has_roles DROP PRIMARY KEY');
            }
            $this->dropIndexIfExists('model_has_roles', 'model_has_roles_team_foreign_key_index');

            DB::statement('ALTER TABLE model_has_roles MODIFY tenant_id VARCHAR(36) NOT NULL');
            DB::statement('ALTER TABLE model_has_roles ADD INDEX model_has_roles_team_foreign_key_index (tenant_id)');
            DB::statement('ALTER TABLE model_has_roles ADD PRIMARY KEY (tenant_id, role_id, model_id, model_type)');
        }

        if (Schema::hasTable('model_has_permissions') && Schema::hasColumn('model_has_permissions', 'tenant_id')) {
            if ($this->hasPrimary('model_has_permissions')) {
                DB::statement('ALTER TABLE model_has_permissions DROP PRIMARY KEY');
            }
            $this->dropIndexIfExists('model_has_permissions', 'model_has_permissions_team_foreign_key_index');

            DB::statement('ALTER TABLE model_has_permissions MODIFY tenant_id VARCHAR(36) NOT NULL');
            DB::statement('ALTER TABLE model_has_permissions ADD INDEX model_has_permissions_team_foreign_key_index (tenant_id)');
            DB::statement('ALTER TABLE model_has_permissions ADD PRIMARY KEY (tenant_id, permission_id, model_id, model_type)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! $this->isMySqlFamily()) {
            return;
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tenant_id')) {
            $this->dropIndexIfExists('users', 'users_tenant_id_index');
            $this->dropIndexIfExists('users', 'users_tenant_id_email_index');
            DB::statement('ALTER TABLE users MODIFY tenant_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE users ADD INDEX users_tenant_id_index (tenant_id)');
        }
    }

    private function isMySqlFamily(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function hasIndex(string $table, string $index): bool
    {
        $database = (string) DB::getDatabaseName();

        $result = DB::selectOne(
            'SELECT COUNT(1) AS aggregate FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?',
            [$database, $table, $index],
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function hasPrimary(string $table): bool
    {
        return $this->hasIndex($table, 'PRIMARY');
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! $this->hasIndex($table, $index)) {
            return;
        }

        DB::statement(sprintf('ALTER TABLE %s DROP INDEX %s', $table, $index));
    }
};
