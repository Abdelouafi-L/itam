<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name'        => 'Administrateur',
                'description' => 'Accès complet au système',
            ],
            [
                'name'        => 'Technicien',
                'description' => 'Gestion des équipements et opérations',
            ],
            [
                'name'        => 'Manager',
                'description' => 'Lecture seule et rapports',
            ],
            [
                'name'        => 'Employé',
                'description' => 'Consultation de ses propres affectations',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}