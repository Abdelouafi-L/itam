<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Order matters — dependencies must be seeded first.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,   // 1. Departments first
            RoleSeeder::class,         // 2. Roles
            UserSeeder::class,         // 3. Users + Employees
            CategorySeeder::class,     // 4. Categories
            ProductSeeder::class,      // 5. Products + Hardware/Software + Stock
            LicenseSeeder::class,      // 6. Licenses (needs software)
            AssignmentSeeder::class,   // 7. Assignments (needs employees + products)
            MaintenanceSeeder::class,  // 8. Maintenances (needs hardware + users)
        ]);
    }
}