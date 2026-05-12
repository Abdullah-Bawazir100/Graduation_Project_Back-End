<?php

namespace App\Application\User\Constants;

use App\Domain\User\Enums\UserRole;

class UsersRolesAndAllowedRoutes
{
    public static array $allowedRoutes = [
        // Admin Permissions
        UserRole::Admin->value => [

            // Users
            'app_users.index',
            'app_users.store',
            'app_users.show',
            'app_users.update',
            'app_users.destroy',

            // Departments
            'departments.index',
            'departments.store',
            'departments.show',
            'departments.update',
            'departments.destroy',
            'departments.move-users',

            // Activity Types
            'activity_types.index',
            'activity_types.store',
            'activity_types.show',
            'activity_types.update',
            'activity_types.destroy',

            // Payment Types
            'payment_types.index',
            'payment_types.store',
            'payment_types.show',
            'payment_types.update',
            'payment_types.destroy',

            // Regions
            'regions.index',
            'regions.store',
            'regions.show',
            'regions.update',
            'regions.destroy',

            // Districts
            'districts.index',
            'districts.store',
            'districts.show',
            'districts.update',
            'districts.destroy',
            'districts.getByRegion',

            // Addresses
            'addresses.index',
            'addresses.store',
            'addresses.show',
            'addresses.update',
            'addresses.destroy',

            // Job Types
            'job-types.index',
            'job-types.store',
            'job-types.show',
            'job-types.update',
            'job-types.destroy',
            'job-types.move-TaxCollectors',

            // Statistics
            'statistics.getStatistics',

            // Tax Collectors
            'tax-collectors.index',
            'tax-collectors.store',
            'tax-collectors.show',
            'tax-collectors.update',
            'tax-collectors.destroy',

            // Tax Payers
            'tax-payers.index',
            'tax-payers.store',
            'tax-payers.show',
            'tax-payers.update',
            'tax-payers.destroy',
            'tax-payer-by-userId',
            'get-tax-payers-with-special-info',
            'tax-payers.create-file-to-existing',

            // Companies
            'companies.index',
            'companies.store',
            'companies.show',
            'companies.update',
            'companies.destroy',
            'companies.create-file-to-existing',


            // Charitable Companies
            'charitable-companies.index',
            'charitable-companies.store',
            'charitable-companies.show',
            'charitable-companies.update',
            'charitable-companies.destroy',
            'charitable-companies.create-file-to-existing',

            // Tax Types
            'tax-types.index',
            'tax-types.store',
            'tax-types.show',
            'tax-types.update',
            'tax-types.destroy',

            // Tax Informations
            'tax-informations.index',
            'tax-informations.store',
            'tax-informations.show',
            'tax-informations.update',
            'tax-informations.destroy',

            // File Status
            'file-status.index',
            'file-status.store',
            'file-status.show',
            'file-status.update',
            'file-status.destroy',

            // Authentication routes
            'auth.create-user',
            'auth.reset-password',
            'auth.complete-profile',
            'auth.logout',

            // Other routes
            'users.show',
            'activity-log.index',
        ],

        // Manager Permissions
        UserRole::Manager->value => [
            // Users
            'app_users.index',
            'app_users.store',
            'app_users.show',
            'app_users.update',
            // Note: No destroy permission for managers

            // Departments
            'departments.index',
            'departments.store',
            'departments.show',
            'departments.update',
            'departments.move-users',
            // Note: No destroy permission for managers

            // Activity Types
            'activity_types.index',
            'activity_types.store',
            'activity_types.show',
            'activity_types.update',
            // Note: No destroy permission for managers

            // Payment Types
            'payment_types.index',
            'payment_types.store',
            'payment_types.show',
            'payment_types.update',
            // Note: No destroy permission for managers

            // Regions
            'regions.index',
            'regions.store',
            'regions.show',
            'regions.update',
            // Note: No destroy permission for managers

            // Districts
            'districts.index',
            'districts.store',
            'districts.show',
            'districts.update',
            'districts.getByRegion',
            // Note: No destroy permission for managers

            // Addresses
            'addresses.index',
            'addresses.store',
            'addresses.show',
            'addresses.update',
            // Note: No destroy permission for managers

            // Job Types
            'job-types.index',
            'job-types.store',
            'job-types.show',
            'job-types.update',
            'job-types.move-TaxCollectors',
            // Note: No destroy permission for managers

            // Statistics
            'statistics.getStatistics',

            // Tax Collectors
            'tax-collectors.index',
            'tax-collectors.store',
            'tax-collectors.show',
            'tax-collectors.update',
            // Note: No destroy permission for managers

            // Tax Payers
            'tax-payers.index',
            'tax-payers.store',
            'tax-payers.show',
            'tax-payers.update',
            'tax-payer-by-userId',
            'get-tax-payers-with-special-info',
            'tax-payers.create-file-to-existing',
            // Note: No destroy permission for managers

            // Companies
            'companies.index',
            'companies.store',
            'companies.show',
            'companies.update',
            'companies.create-file-to-existing',
            // Note: No destroy permission for managers

            // Charitable Companies
            'charitable-companies.index',
            'charitable-companies.store',
            'charitable-companies.show',
            'charitable-companies.update',
            'charitable-companies.create-file-to-existing',
            // Note: No destroy permission for managers

            // Tax Types
            'tax-types.index',
            'tax-types.store',
            'tax-types.show',
            'tax-types.update',
            // Note: No destroy permission for managers

            // Tax Informations
            'tax-informations.index',
            'tax-informations.store',
            'tax-informations.show',
            'tax-informations.update',

            // File Status
            'file-status.index',
            'file-status.store',
            'file-status.show',
            'file-status.update',
            // Note: No destroy permission for managers

            // Authentication routes
            'auth.create-user',
            'auth.reset-password',
            'auth.logout',

            // Other routes
            'users.show',
            'activity-log.index',
        ],

        // Employee Permissions
        UserRole::Employee->value => [
            // Employees have limited access - only read operations
            // Just Permissions in Files Section
        ],

        // Tax Payer Permissions
        UserRole::Tax_Payer->value => [
            // He will use the mobile app
            'update-tax-payer-mobile',
            'get-tax-payer-mobile-profile',
            'tax-payer-mobile-logout'
        ],

        // Collectors Manager Permissions
        UserRole::Collectors_Manager->value => [

            'departments.index',

            // Job Types - Can view and update only
            'job-types.index',
            'job-types.store',
            'job-types.show',
            'job-types.update',
            'job-types.move-TaxCollectors',
            // Note: No store/destroy for collectors manager

            // Tax Collectors - Full access except destroy
            'tax-collectors.index',
            'tax-collectors.store',
            'tax-collectors.show',
            'tax-collectors.update',
            // Note: No destroy permission for collectors manager
        ],
    ];
}
