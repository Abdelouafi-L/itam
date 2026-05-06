<?php

namespace Database\Seeders;

use App\Models\Hardware;
use App\Models\Maintenance;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    public function run(): void
    {
        $tech = User::whereHas('employee', fn($q) =>
            $q->where('email', 'tech@techcorp.ma')
        )->first();

        if (!$tech) return;

        $maintenances = [
            [
                'product'     => 'HP EliteBook 840 G10',
                'type'        => 'Corrective',
                'description' => 'Remplacement de la batterie défectueuse',
                'date'        => now()->subDays(45)->format('Y-m-d'),
                'cost'        => 850.00,
                'status'      => 'Terminée',
                'condition'   => 'Bon',
            ],
            [
                'product'     => 'Dell Latitude 5540',
                'type'        => 'Préventive',
                'description' => 'Nettoyage et mise à jour système',
                'date'        => now()->subDays(20)->format('Y-m-d'),
                'cost'        => 200.00,
                'status'      => 'Terminée',
                'condition'   => 'Neuf',
            ],
            [
                'product'     => 'Switch Cisco 24 ports',
                'type'        => 'Corrective',
                'description' => 'Remplacement port défectueux',
                'date'        => now()->subDays(5)->format('Y-m-d'),
                'cost'        => 1200.00,
                'status'      => 'En cours',
                'condition'   => 'Usagé',
            ],
            [
                'product'     => 'HP LaserJet Pro M404n',
                'type'        => 'Préventive',
                'description' => 'Maintenance périodique annuelle',
                'date'        => now()->addDays(10)->format('Y-m-d'),
                'cost'        => 350.00,
                'status'      => 'Planifiée',
                'condition'   => null,
            ],
        ];

        foreach ($maintenances as $data) {
            $product = Product::where('name', $data['product'])->first();
            if (!$product || !$product->hardware) continue;

            Maintenance::create([
                'hardware_id'   => $product->hardware->id,
                'technician_id' => $tech->id,
                'type'          => $data['type'],
                'description'   => $data['description'],
                'date'          => $data['date'],
                'cost'          => $data['cost'],
                'status'        => $data['status'],
            ]);

            if ($data['condition']) {
                $product->hardware->update([
                    'condition' => $data['condition'],
                ]);
            }
        }
    }
}