<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[Fillable(['tenant_id', 'name', 'key', 'guard_name', 'description', 'module', 'is_system', 'status'])]
class Permission extends SpatiePermission
{
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
