<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Abdelouafi',
                'last_name'  => 'Louardi',
                'email'      => 'admin@techcorp.ma',
                'department' => 'Administration',
                'role'       => 'Administrateur',
                'password'   => 'password123',
            ],
            [
                'first_name' => 'Mohamed',
                'last_name'  => 'Alami',
                'email'      => 'tech@techcorp.ma',
                'department' => 'IT',
                'role'       => 'Technicien',
                'password'   => 'password123',
            ],
            [
                'first_name' => 'Fatima',
                'last_name'  => 'Benali',
                'email'      => 'manager@techcorp.ma',
                'department' => 'Finance',
                'role'       => 'Manager',
                'password'   => 'password123',
            ],
            [
                'first_name' => 'Youssef',
                'last_name'  => 'Chakir',
                'email'      => 'employe@techcorp.ma',
                'department' => 'Opérations',
                'role'       => 'Employé',
                'password'   => 'password123',
            ],
        ];

        foreach ($users as $data) {
            $department = Department::where('name', $data['department'])
                                    ->first();
            $role = Role::where('name', $data['role'])->first();

            // Create employee if not exists
            $employee = Employee::firstOrCreate(
                ['email' => $data['email']],
                [
                    'department_id' => $department->id,
                    'first_name'    => $data['first_name'],
                    'last_name'     => $data['last_name'],
                    'is_active'     => true,
                ]
            );

            // Create user account if not exists
            User::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'role_id'  => $role->id,
                    'password' => Hash::make($data['password']),
                    'is_active'=> true,
                ]
            );
        }

        // Create additional employees without user accounts
        $employees = [
            [
                'first_name' => 'Aicha',
                'last_name'  => 'Mansouri',
                'email'      => 'a.mansouri@techcorp.ma',
                'department' => 'RH',
            ],
            [
                'first_name' => 'Karim',
                'last_name'  => 'Idrissi',
                'email'      => 'k.idrissi@techcorp.ma',
                'department' => 'IT',
            ],
            [
                'first_name' => 'Sara',
                'last_name'  => 'Tazi',
                'email'      => 's.tazi@techcorp.ma',
                'department' => 'Finance',
            ],
            [
                'first_name' => 'Omar',
                'last_name'  => 'Benjelloun',
                'email'      => 'o.benjelloun@techcorp.ma',
                'department' => 'Opérations',
            ],
        ];

        foreach ($employees as $data) {
            $department = Department::where('name', $data['department'])
                                    ->first();
            Employee::firstOrCreate(
                ['email' => $data['email']],
                [
                    'department_id' => $department->id,
                    'first_name'    => $data['first_name'],
                    'last_name'     => $data['last_name'],
                    'is_active'     => true,
                ]
            );
        }
    }
}