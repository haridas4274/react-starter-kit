<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Role as SpatieRole;

#[Fillable(['tenant_id', 'name', 'key', 'guard_name', 'description', 'is_system', 'sort_order', 'status'])]
class Role extends SpatieRole
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
