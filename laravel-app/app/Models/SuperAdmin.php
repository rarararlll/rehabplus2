<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SuperAdmin extends Authenticatable
{
    protected $table = 'super_admins';

    protected $hidden = [
        'password',
    ];

    public function getAuthIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getNameAttribute(): string
    {
        return 'Super Admin';
    }

    public function getRoleAttribute(): string
    {
        return 'superadmin';
    }
}