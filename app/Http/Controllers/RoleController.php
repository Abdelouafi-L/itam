<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * RF-43: View role/permission matrix.
     */
    public function index()
    {
        $roles       = Role::with('permissions')->get();
        $permissions = Permission::orderBy('name')->get();

        // Group permissions by module
        $permissionsByModule = $permissions->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('admin.roles.index',
                    compact('roles', 'permissions',
                            'permissionsByModule'));
    }

    /**
     * RF-40: Show create role form.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        $permissionsByModule = $permissions->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('admin.roles.create',
                    compact('permissions', 'permissionsByModule'));
    }

    /**
     * RF-40: Store new role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:50',
                              'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $permissionObjects = \Spatie\Permission\Models\Permission::whereIn(
                'id', $validated['permissions']
            )->get();
            $role->syncPermissions($permissionObjects);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()
            ->route('roles.index')
            ->with('success',
                'Rôle "' . $role->name . '" créé avec succès.');
    }

    /**
     * RF-41 + RF-42: Show edit form with permissions.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();

        $permissionsByModule = $permissions->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit',
                    compact('role', 'permissions',
                            'permissionsByModule', 'rolePermissions'));
    }

    /**
     * RF-41 + RF-42: Update role and its permissions.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:50',
                                'unique:roles,name,' . $role->id],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update(['name' => $validated['name']]);

        // RF-42: Sync permissions — removes old, adds new
        if (!empty($validated['permissions'])) {
            $permissionObjects = \Spatie\Permission\Models\Permission::whereIn(
                'id', $validated['permissions']
            )->get();
            $role->syncPermissions($permissionObjects);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()
            ->route('roles.index')
            ->with('success',
                'Rôle "' . $role->name . '" mis à jour avec succès.');
    }

    /**
     * Delete role — only if no users have it.
     */
    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()
                ->route('roles.index')
                ->with('error',
                    'Impossible de supprimer ce rôle — ' .
                    $role->users()->count() .
                    ' utilisateur(s) l\'ont assigné.'
                );
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès.');
    }

    /**
     * RF-44: Assign role to user — handled in UserController.
     */
    public function show(Role $role)
    {
        return redirect()->route('roles.edit', $role);
    }
}