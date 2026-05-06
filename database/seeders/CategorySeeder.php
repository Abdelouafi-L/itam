<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Ordinateurs portables',
                'description' => 'Laptops et notebooks',
            ],
            [
                'name'        => 'Ordinateurs de bureau',
                'description' => 'Desktops et workstations',
            ],
            [
                'name'        => 'Imprimantes',
                'description' => 'Imprimantes et scanners',
            ],
            [
                'name'        => 'Périphériques',
                'description' => 'Souris, claviers, écrans, etc.',
            ],
            [
                'name'        => 'Réseau',
                'description' => 'Switches, routeurs, câbles réseau',
            ],
            [
                'name'        => 'Logiciels',
                'description' => 'Applications et licences logicielles',
            ],
            [
                'name'        => 'Téléphonie',
                'description' => 'Téléphones fixes et mobiles',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}