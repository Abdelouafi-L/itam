<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Create all permissions
        $permissions = [
            'assets.view', 'assets.create', 'assets.edit', 'assets.delete',
            'licenses.view', 'licenses.manage',
            'assignments.view', 'assignments.create', 'assignments.return',
            'maintenance.view', 'maintenance.manage',
            'reports.view', 'reports.export',
            'users.view', 'users.manage',
            'fournisseurs.view', 'fournisseurs.manage',
            'livraisons.view', 'livraisons.manage',
            'roles.view', 'roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Create roles and assign permissions
        $admin = Role::firstOrCreate([
            'name' => 'Administrateur', 'guard_name' => 'web'
        ]);
        $admin->syncPermissions(Permission::all());

        $tech = Role::firstOrCreate([
            'name' => 'Technicien', 'guard_name' => 'web'
        ]);
        $tech->syncPermissions([
            'assets.view', 'assets.create', 'assets.edit',
            'licenses.view',
            'assignments.view', 'assignments.create', 'assignments.return',
            'maintenance.view', 'maintenance.manage',
            'fournisseurs.view', 'fournisseurs.manage',
            'livraisons.view', 'livraisons.manage',
            'reports.view',
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'Manager', 'guard_name' => 'web'
        ]);
        $manager->syncPermissions([
            'assets.view',
            'licenses.view',
            'assignments.view',
            'maintenance.view',
            'reports.view', 'reports.export',
            'fournisseurs.view',
            'livraisons.view',
        ]);

        $employe = Role::firstOrCreate([
            'name' => 'Employé', 'guard_name' => 'web'
        ]);
        $employe->syncPermissions([
            'assignments.view',
        ]);
    }
}