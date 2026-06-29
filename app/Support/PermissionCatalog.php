<?php

namespace App\Support;

class PermissionCatalog
{
    public static function modules(): array
    {
        return [
            'customers' => [
                'label' => 'Customers',
                'description' => 'Customer profiles, contact records, balances, and customer CRUD.',
                'abilities' => self::crudAbilities(),
            ],
            'products' => [
                'label' => 'Products',
                'description' => 'Product catalog, pricing, stock quantities, stock adjustments, and product CRUD.',
                'abilities' => [
                    ...self::crudAbilities(),
                    'adjustStock' => 'Adjust stock',
                ],
            ],
            'invoices' => [
                'label' => 'Invoices',
                'description' => 'Invoice list, invoice creation, edits, cancellation, sending, and PDF download.',
                'abilities' => [
                    'viewAny' => 'List invoices',
                    'view' => 'View invoice',
                    'create' => 'Create invoice',
                    'update' => 'Edit invoice',
                    'delete' => 'Delete draft invoice',
                    'send' => 'Send invoice',
                    'download' => 'Download PDF',
                ],
            ],
            'payments' => [
                'label' => 'Payments',
                'description' => 'Payment listing, payment recording, and payment reversal.',
                'abilities' => [
                    'viewAny' => 'List payments',
                    'view' => 'View payment',
                    'create' => 'Record payment',
                    'delete' => 'Delete payment',
                ],
            ],
            'reports' => [
                'label' => 'Reports',
                'description' => 'Sales, customer, payment, outstanding invoice reports, and CSV exports.',
                'abilities' => [
                    'viewAny' => 'List reports',
                    'view' => 'View reports',
                    'export' => 'Export reports',
                ],
            ],
            'users' => [
                'label' => 'Users',
                'description' => 'User approvals, user profile updates, role assignment, and account activation.',
                'abilities' => [
                    'viewAny' => 'List users',
                    'view' => 'View user',
                    'create' => 'Create user',
                    'update' => 'Update user',
                ],
            ],
            'settings' => [
                'label' => 'Settings',
                'description' => 'Company settings, invoice settings, and system defaults.',
                'abilities' => [
                    'view' => 'View settings',
                    'update' => 'Update settings',
                ],
            ],
        ];
    }

    public static function allPermissions(): array
    {
        return collect(self::modules())
            ->flatMap(fn (array $module, string $key) => array_map(
                fn (string $ability) => "{$key}.{$ability}",
                array_keys($module['abilities'])
            ))
            ->values()
            ->all();
    }

    public static function sanitize(array $permissions): array
    {
        $allowed = self::allPermissions();

        return collect(self::expand($permissions))
            ->filter(fn (mixed $permission) => is_string($permission) && in_array($permission, $allowed, true))
            ->unique()
            ->values()
            ->all();
    }

    public static function expand(array $permissions): array
    {
        $all = self::allPermissions();

        return collect($permissions)
            ->flatMap(function (string $permission) use ($all): array {
                if ($permission === '*') {
                    return $all;
                }

                if (str_ends_with($permission, '.*')) {
                    $prefix = substr($permission, 0, -1);

                    return array_values(array_filter($all, fn (string $allowed) => str_starts_with($allowed, $prefix)));
                }

                return [$permission];
            })
            ->unique()
            ->values()
            ->all();
    }

    private static function crudAbilities(): array
    {
        return [
            'viewAny' => 'List records',
            'view' => 'View record',
            'create' => 'Create record',
            'update' => 'Update record',
            'delete' => 'Delete record',
        ];
    }
}
