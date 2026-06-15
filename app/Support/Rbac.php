<?php

namespace App\Support;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Models\User;

class Rbac
{
    /** @return list<string> */
    public static function permissionsForRole(UserRole $role): array
    {
        return config('rbac.roles.'.$role->value, []);
    }

    public static function roleHasPermission(UserRole $role, string $permission): bool
    {
        $permissions = self::permissionsForRole($role);

        return in_array('*', $permissions, true)
            || in_array($permission, $permissions, true);
    }

    public static function userHasPermission(?User $user, string $permission): bool
    {
        if (! $user || ! $user->role instanceof UserRole) {
            return false;
        }

        if ($user->role === UserRole::Admin) {
            return true;
        }

        return self::roleHasPermission($user->role, $permission);
    }

    /** @param  list<string>  $permissions */
    public static function userHasAnyPermission(?User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::userHasPermission($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    public static function permissionFromEnum(Permission $permission): string
    {
        return $permission->value;
    }
}
