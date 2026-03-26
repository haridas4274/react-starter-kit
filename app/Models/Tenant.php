<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

#[Fillable([
    'id',
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
    'data',
])]
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase;
    use HasDomains;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'data' => 'array',
        ];
    }
}
