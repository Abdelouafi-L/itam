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
            DepartmentSeeder::class, // 1. Departments first
            RolePermissionSeeder::class,  // ← add this before UserSeeder
            UserSeeder::class, // 3. Users + Employees (needs roles + departments)
            CategorySeeder::class, // 4. Categories (for products)
            ProductSeeder::class, // 5. Products + Hardware/Software + Stock (needs categories)
            LicenseSeeder::class, // 6. Licenses (needs software)
            AssignmentSeeder::class, // 7. Assignments (needs employees + products)
            MaintenanceSeeder::class, // 8. Maintenances (needs hardware + users)
        ]);
    }

}