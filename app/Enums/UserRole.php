<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Accountant = 'accountant';
    case SalesUser = 'sales_user';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Accountant => 'Accountant',
            self::SalesUser => 'Sales User',
            self::Viewer => 'Viewer',
        };
    }

    public function permissions(): array
    {
        return match ($this) {
            self::SuperAdmin => ['*'],
            self::Admin => ['customers.*', 'products.*', 'invoices.*', 'payments.*', 'reports.*', 'users.viewAny', 'users.view', 'users.create', 'users.update', 'settings.view', 'settings.update'],
            self::Accountant => ['customers.viewAny', 'customers.view', 'products.viewAny', 'products.view', 'invoices.viewAny', 'invoices.view', 'invoices.create', 'invoices.update', 'invoices.send', 'invoices.download', 'payments.viewAny', 'payments.view', 'payments.create', 'reports.*', 'settings.view'],
            self::SalesUser => ['customers.viewAny', 'customers.view', 'customers.create', 'customers.update', 'products.viewAny', 'products.view', 'invoices.viewAny', 'invoices.view', 'invoices.create', 'invoices.update', 'invoices.send', 'invoices.download', 'payments.viewAny', 'payments.view', 'reports.view'],
            self::Viewer => ['customers.viewAny', 'customers.view', 'products.viewAny', 'products.view', 'invoices.viewAny', 'invoices.view', 'invoices.download', 'payments.viewAny', 'payments.view', 'reports.view'],
        };
    }

    public function allows(string $ability): bool
    {
        foreach ($this->permissions() as $permission) {
            if ($permission === '*' || $permission === $ability) {
                return true;
            }

            if (str_ends_with($permission, '.*') && str_starts_with($ability, substr($permission, 0, -1))) {
                return true;
            }
        }

        return false;
    }
}
