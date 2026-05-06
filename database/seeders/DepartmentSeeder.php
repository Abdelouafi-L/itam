<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name'        => 'IT',
                'description' => 'Département informatique et systèmes',
            ],
            [
                'name'        => 'Finance',
                'description' => 'Département financier et comptabilité',
            ],
            [
                'name'        => 'RH',
                'description' => 'Ressources humaines',
            ],
            [
                'name'        => 'Opérations',
                'description' => 'Département des opérations',
            ],
            [
                'name'        => 'Administration',
                'description' => 'Département administratif système',
            ],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name']],
                ['description' => $dept['description']]
            );
        }
    }
}