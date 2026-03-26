<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role_in_tenant', 'is_owner', 'status', 'last_accessed_at'])
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'data' => 'array',
        ];
    }
}
