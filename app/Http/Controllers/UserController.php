<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display all users.
     */
    public function index()
    {
        $users = User::with(['employee.department'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Show form to create a new user.
     */
    public function create()
    {
        $roles       = Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.users-create',
                    compact('roles', 'departments'));
    }

    /**
     * Store a new user.
     * Creates Employee + User in one transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:50'],
            'last_name'     => ['required', 'string', 'max:50'],
            'email'         => ['required', 'email',
                                'unique:employees,email'],
            'department_id' => ['required', 'exists:departments,id'],
            'role_id'       => ['required', 'exists:roles,id'],
            'password'      => ['required', 'min:8', 'confirmed'],
            'phone'         => ['nullable', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($validated) {

            $employee = Employee::create([
                'department_id' => $validated['department_id'],
                'first_name'    => $validated['first_name'],
                'last_name'     => $validated['last_name'],
                'email'         => $validated['email'],
                'phone'         => $validated['phone'] ?? null,
                'is_active'     => true,
            ]);

            $user = User::create([
                'employee_id' => $employee->id,
                'password'    => Hash::make($validated['password']),
                'is_active'   => true,
            ]);

            // RF-44: Find role by ID and assign via Spatie
            $role = \Spatie\Permission\Models\Role::findById(
                $validated['role_id']
            );
            $user->assignRole($role);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'Compte utilisateur créé avec succès.');
    }

    /**
     * Show form to edit a user.
     */
    public function edit(User $user)
    {
        $user->load(['employee.department']);
        $roles       = \Spatie\Permission\Models\Role::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();

        return view('admin.users-edit',
                    compact('user', 'roles', 'departments'));
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name'    => ['required', 'string', 'max:50'],
            'last_name'     => ['required', 'string', 'max:50'],
            'email'         => ['required', 'email',
                                'unique:employees,email,'
                                . $user->employee->id],
            'department_id' => ['required', 'exists:departments,id'],
            'role_id'       => ['required', 'exists:roles,id'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'is_active'     => ['boolean'],
            'password'      => ['nullable', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($validated, $user) {

            // Update employee record
            $user->employee->update([
                'first_name'    => $validated['first_name'],
                'last_name'     => $validated['last_name'],
                'email'         => $validated['email'],
                'department_id' => $validated['department_id'],
                'phone'         => $validated['phone'] ?? null,
                'is_active'     => $validated['is_active'] ?? true,
            ]);

            // Update user record
            $updateData = [
                'is_active' => $validated['is_active'] ?? true,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // RF-44: Update role via Spatie
            $role = \Spatie\Permission\Models\Role::findById(
                $validated['role_id']
            );
            $user->syncRoles([$role]);
        });

        return redirect()
            ->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Toggle user active status.
     * No hard delete — audit trail must be preserved.
     */
    public function destroy(User $user)
    {
        /** @var \App\Models\User $authUser */
        $authUser = \Illuminate\Support\Facades\Auth::user();

        // Prevent deactivating yourself
        if ($user->id === $authUser->id) {
            return redirect()
                ->route('users.index')
                ->with('error',
                    'Vous ne pouvez pas désactiver votre propre compte.'
                );
        }

        $user->update(['is_active' => !$user->is_active]);
        $user->employee->update(['is_active' => !$user->employee->is_active]);

        $status = $user->is_active ? 'activé' : 'désactivé';

        return redirect()
            ->route('users.index')
            ->with('success', "Compte {$status} avec succès.");
    }

    /**
     * Show and edit methods not needed separately.
     */
    public function show(User $user)
    {
        return redirect()->route('users.edit', $user);
    }
}