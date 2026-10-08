<?php

namespace App\Auth;

use App\Models\SuperAdmin;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class RehabPlusUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        if (is_string($identifier) && str_starts_with($identifier, 'super_admins:')) {
            return SuperAdmin::query()->find(substr($identifier, strlen('super_admins:')));
        }

        return parent::retrieveById($identifier);
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (isset($credentials['email'])) {
            $superAdmin = SuperAdmin::query()
                ->where('email', $credentials['email'])
                ->first();

            if ($superAdmin) {
                return $superAdmin;
            }
        }

        return parent::retrieveByCredentials($credentials);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        if ($user instanceof SuperAdmin) {
            return;
        }

        parent::updateRememberToken($user, $token);
    }
}