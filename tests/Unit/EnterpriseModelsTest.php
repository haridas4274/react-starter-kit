<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Tests\TestCase;

class EnterpriseModelsTest extends TestCase
{
    public function test_permission_config_uses_custom_models_and_tenant_teams(): void
    {
        $this->assertSame(Role::class, config('permission.models.role'));
        $this->assertSame(Permission::class, config('permission.models.permission'));
        $this->assertTrue(config('permission.teams'));
        $this->assertSame('tenant_id', config('permission.column_names.team_foreign_key'));
    }

    public function test_user_role_and_permission_models_have_tenant_relationships(): void
    {
        $this->assertSame('tenant', (new User)->tenant()->getRelationName());
        $this->assertSame(Tenant::class, (new User)->tenant()->getRelated()::class);

        $this->assertSame('tenant', (new Role)->tenant()->getRelationName());
        $this->assertSame(Tenant::class, (new Role)->tenant()->getRelated()::class);

        $this->assertSame('tenant', (new Permission)->tenant()->getRelationName());
        $this->assertSame(Tenant::class, (new Permission)->tenant()->getRelated()::class);
    }
}
