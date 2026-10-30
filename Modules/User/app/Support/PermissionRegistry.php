<?php

namespace Modules\User\Support;

class PermissionRegistry
{
    /**
     * Map of function names to friendly display labels.
     * Raw permission identifiers must never be shown as the label in UI.
     */
    protected static array $actionLabels = [
        'view' => 'View',
        'create' => 'Create',
        'edit' => 'Edit',
        'update' => 'Update',
        'delete' => 'Delete',
        'export' => 'Export',
        'print' => 'Print',
        'approve' => 'Approve',
        'refund' => 'Refund',
        'cancel' => 'Cancel',
        'sync' => 'Sync',
    ];

    /**
     * Map of module keys to human-readable section labels.
     */
    protected static array $moduleLabels = [
        'users' => 'User Management',
        'register' => 'Registration & Visits',
        'patient' => 'Patient Management',
        'invoice' => 'Invoice & Billing',
        'payment' => 'Payment Management',
        'hospital' => 'Hospital Management',
        'odontogram' => 'Dental Chart / Odontogram',
        'conditions' => 'Dental Conditions',
        'activity_log' => 'Activity & Audit Log',
        'settings' => 'System Settings',
    ];

    /**
     * Map of controller keys to human-readable resource labels.
     */
    protected static array $controllerLabels = [
        'users' => 'Users',
        'roles' => 'Roles',
        'permissions' => 'Permissions',
        'register' => 'Registration',
        'patients' => 'Patients',
        'invoices' => 'Invoices',
        'payments' => 'Payments',
        'service-categories' => 'Service Categories',
        'product-categories' => 'Product Categories',
        'services' => 'Services',
        'products' => 'Products',
        'doctors' => 'Doctors',
        'dental_chart' => 'Dental Charts',
        'dental_condition' => 'Dental Conditions',
        'general' => 'General Settings',
    ];

    /**
     * Parse permission identifier into [module, controller, function].
     *
     * @return array{module: string, controller: string, function: string}
     */
    public static function parse(string $permissionName): array
    {
        $parts = explode(':', trim($permissionName));

        return [
            'module' => $parts[0] ?? 'general',
            'controller' => $parts[1] ?? 'general',
            'function' => $parts[2] ?? 'view',
        ];
    }

    /**
     * Format a permission name from parts.
     */
    public static function format(string $module, string $controller, string $function): string
    {
        return strtolower(trim($module)).':'.strtolower(trim($controller)).':'.strtolower(trim($function));
    }

    /**
     * Get the human-readable display label for an action/function.
     */
    public static function actionLabel(string $action): string
    {
        $key = strtolower(trim($action));

        return static::$actionLabels[$key] ?? ucwords(str_replace(['_', '-'], ' ', $key));
    }

    /**
     * Get the human-readable display label for a module.
     */
    public static function moduleLabel(string $module): string
    {
        $key = strtolower(trim($module));

        return static::$moduleLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Get the human-readable display label for a controller/resource.
     */
    public static function controllerLabel(string $controller): string
    {
        $key = strtolower(trim($controller));

        return static::$controllerLabels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Default list of application permissions for seeding.
     * Format: module:controller:function
     */
    public static function defaultPermissions(): array
    {
        return [
            // User Module - Users
            'users:users:view',
            'users:users:create',
            'users:users:edit',
            'users:users:update',
            'users:users:delete',
            'users:users:export',

            // User Module - Roles
            'users:roles:view',
            'users:roles:create',
            'users:roles:edit',
            'users:roles:update',
            'users:roles:delete',

            // User Module - Permissions
            'users:permissions:view',
            'users:permissions:create',
            'users:permissions:edit',
            'users:permissions:update',
            'users:permissions:delete',

            // Patient Module - Patients
            'patient:patients:view',
            'patient:patients:create',
            'patient:patients:edit',
            'patient:patients:update',
            'patient:patients:delete',
            'patient:patients:export',

            // Register Module - Visits & Registration
            'register:register:view',
            'register:register:create',
            'register:register:update',

            // Invoice Module - Invoices
            'invoice:invoices:view',
            'invoice:invoices:create',
            'invoice:invoices:edit',
            'invoice:invoices:update',
            'invoice:invoices:delete',

            // Payment Module - Payments
            'payment:payments:view',
            'payment:payments:create',
            'payment:payments:edit',
            'payment:payments:update',
            'payment:payments:delete',

            // Hospital Module - Service Categories
            'hospital:service-categories:view',
            'hospital:service-categories:create',
            'hospital:service-categories:edit',
            'hospital:service-categories:update',
            'hospital:service-categories:delete',

            // Hospital Module - Product Categories
            'hospital:product-categories:view',
            'hospital:product-categories:create',
            'hospital:product-categories:edit',
            'hospital:product-categories:update',
            'hospital:product-categories:delete',

            // Hospital Module - Services
            'hospital:services:view',
            'hospital:services:create',
            'hospital:services:edit',
            'hospital:services:update',
            'hospital:services:delete',

            // Hospital Module - Products
            'hospital:products:view',
            'hospital:products:create',
            'hospital:products:edit',
            'hospital:products:update',
            'hospital:products:delete',

            // Hospital Module - Doctors
            'hospital:doctors:view',
            'hospital:doctors:create',
            'hospital:doctors:edit',
            'hospital:doctors:update',
            'hospital:doctors:delete',

            // DentalChart Module - Dental Charts
            'odontogram:dental_chart:view',
            'odontogram:dental_chart:create',
            'odontogram:dental_chart:edit',
            'odontogram:dental_chart:update',
            'odontogram:dental_chart:delete',
            'odontogram:dental_chart:export',
            'odontogram:dental_chart:print',

            // DentalCondition Module - Dental Conditions
            'conditions:dental_condition:view',
            'conditions:dental_condition:create',
            'conditions:dental_condition:edit',
            'conditions:dental_condition:update',
            'conditions:dental_condition:delete',

            // Settings Module - General Settings
            'settings:general:view',
            'settings:general:edit',
            'settings:general:update',
        ];
    }
}
