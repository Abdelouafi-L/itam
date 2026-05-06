<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * Define the model's default state.
     * Updated to match our Employee/User split schema.
     */
    public function definition(): array
    {
        // Get or create a default department and role
        $department = Department::first()
                      ?? Department::factory()->create();

        $role = Role::where('name', 'Employé')->first()
                ?? Role::first();

        // Create the Employee record first
        $employee = Employee::create([
            'department_id' => $department->id,
            'first_name'    => fake()->firstName(),
            'last_name'     => fake()->lastName(),
            'email'         => fake()->unique()->safeEmail(),
            'is_active'     => true,
        ]);

        return [
            'employee_id'   => $employee->id,
            'role_id'       => $role->id,
            'password'      => static::$password
                               ??= Hash::make('password'),
            'remember_token'=> Str::random(10),
            'is_active'     => true,
        ];
    }
}