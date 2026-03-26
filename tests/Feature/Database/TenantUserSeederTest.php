<?php

namespace Tests\Feature\Database;

use App\Models\User;
use Database\Seeders\TenantUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TenantUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_tenants_domains_users_and_pivot_memberships(): void
    {
        $this->seed(TenantUserSeeder::class);

        $this->assertDatabaseCount('tenants', 5);
        $this->assertDatabaseCount('domains', 5);
        $this->assertDatabaseCount('tenant_user', 13);

        $shared = User::query()->where('email', 'shared.user@example.com')->firstOrFail();

        $memberships = DB::table('tenant_user')
            ->where('user_id', $shared->id)
            ->count();

        $this->assertSame(3, $memberships);

        $centralAdmin = User::query()->where('email', 'admin@gmail.com')->firstOrFail();
        $this->assertNull($centralAdmin->tenant_id);
        $this->assertTrue(Hash::check('12345678', $centralAdmin->password));
    }
}
