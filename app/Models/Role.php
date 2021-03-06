<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, RolePermission::class);
    }

    /**
     * Users who have been assigned this role
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, UserRole::class);
    }
}
