<?php

namespace App\Auth;

class PermissionMap
{
    /**
     * Map of roles to their corresponding permissions.
     *
     * @var array<string, array<int, string>>
     */
    private const PERMISSIONS = [
        'cashier' => ['pos.sell', 'sales.view-own', 'products.view', 'customers.view'],
        'manager' => ['pos.sell', 'sales.*', 'products.*', 'customers.*', 'suppliers.*', 'purchases.view', 'reports.view', 'inventory.view'],
        'admin'   => ['*'],
        'owner'   => ['*'],
    ];

    /**
     * Get the permissions for a specific role.
     *
     * @param string $role
     * @return array<int, string>
     */
    public static function forRole(string $role): array
    {
        return self::PERMISSIONS[$role] ?? [];
    }

    /**
     * Get Sanctum-compatible abilities for a specific role.
     * Replaces dots with colons in permissions.
     *
     * @param string $role
     * @return array<int, string>
     */
    public static function tokenAbilities(string $role): array
    {
        $permissions = self::forRole($role);

        return array_map(function (string $permission) {
            return str_replace('.', ':', $permission);
        }, $permissions);
    }
}
