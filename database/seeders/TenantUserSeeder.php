<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class TenantUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseDomain = $this->baseDomainFromAppUrl();
        $password = '12345678';

        $tenantSeeds = [
            ['id' => (string) Str::uuid(), 'name' => 'Acme Inc', 'slug' => 'acme', 'plan' => 'enterprise'],
            ['id' => (string) Str::uuid(), 'name' => 'Globex Corp', 'slug' => 'globex', 'plan' => 'pro'],
            ['id' => (string) Str::uuid(), 'name' => 'Initech', 'slug' => 'initech', 'plan' => 'pro'],
            ['id' => (string) Str::uuid(), 'name' => 'Umbrella Group', 'slug' => 'umbrella', 'plan' => 'starter'],
            ['id' => (string) Str::uuid(), 'name' => 'Wayne Enterprises', 'slug' => 'wayne', 'plan' => 'enterprise'],
        ];

        $tenants = [];

        Tenant::withoutEvents(function () use (&$tenants, $tenantSeeds): void {
            foreach ($tenantSeeds as $seed) {
                DB::table('tenants')->updateOrInsert(
                    ['slug' => $seed['slug']],
                    [
                        'id' => $seed['id'],
                        'name' => $seed['name'],
                        'status' => 'active',
                        'billing_email' => 'billing@'.$seed['slug'].'.example.com',
                        'timezone' => 'UTC',
                        'locale' => 'en',
                        'plan' => $seed['plan'],
                        'data' => json_encode([
                            'onboarding_completed' => true,
                            'industry' => 'software',
                        ], JSON_THROW_ON_ERROR),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );

                $tenants[] = Tenant::query()->where('slug', $seed['slug'])->firstOrFail();
            }
        });

        foreach ($tenants as $tenant) {
            Domain::query()->updateOrCreate(
                ['domain' => $tenant->slug.'.'.$baseDomain],
                ['tenant_id' => $tenant->id],
            );

            $owner = User::query()->updateOrCreate(
                ['email' => 'owner+'.$tenant->slug.'@example.com'],
                [
                    'name' => $tenant->name.' Owner',
                    'tenant_id' => $tenant->id,
                    'password' => $password,
                    'email_verified_at' => now(),
                ],
            );

            $manager = User::query()->updateOrCreate(
                ['email' => 'manager+'.$tenant->slug.'@example.com'],
                [
                    'name' => $tenant->name.' Manager',
                    'tenant_id' => $tenant->id,
                    'password' => $password,
                    'email_verified_at' => now(),
                ],
            );

            $tenant->members()->syncWithoutDetaching([
                $owner->id => [
                    'role_in_tenant' => 'owner',
                    'is_owner' => true,
                    'status' => 'active',
                    'last_accessed_at' => now(),
                ],
                $manager->id => [
                    'role_in_tenant' => 'manager',
                    'is_owner' => false,
                    'status' => 'active',
                    'last_accessed_at' => now(),
                ],
            ]);
        }

        $sharedUser = User::query()->updateOrCreate(
            ['email' => 'shared.user@example.com'],
            [
                'name' => 'Shared Multi Tenant User',
                'tenant_id' => Arr::first($tenants)?->id,
                'password' => $password,
                'email_verified_at' => now(),
            ],
        );

        foreach (array_slice($tenants, 0, 3) as $tenant) {
            $tenant->members()->syncWithoutDetaching([
                $sharedUser->id => [
                    'role_in_tenant' => 'auditor',
                    'is_owner' => false,
                    'status' => 'active',
                    'last_accessed_at' => now(),
                ],
            ]);
        }

        // Central marketplace account (not scoped to a tenant).
        User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Central Marketplace Admin',
                'tenant_id' => null,
                'password' => $password,
                'email_verified_at' => now(),
            ],
        );
    }

    private function baseDomainFromAppUrl(): string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return 'localhost';
        }

        return preg_replace('/^www\./', '', $host) ?: 'localhost';
    }
}
