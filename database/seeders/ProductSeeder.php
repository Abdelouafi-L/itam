<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Hardware;
use App\Models\Product;
use App\Models\Software;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Ordinateurs portables
            [
                'category'     => 'Ordinateurs portables',
                'name'         => 'Dell Latitude 5540',
                'brand'        => 'Dell',
                'model'        => 'Latitude 5540',
                'description'  => 'Ordinateur portable professionnel',
                'type'         => 'hardware',
                'condition'    => 'Neuf',
                'purchase_date'=> '2024-01-15',
                'warranty_date'=> '2027-01-15',
                'quantity'     => 15,
            ],
            [
                'category'     => 'Ordinateurs portables',
                'name'         => 'HP EliteBook 840 G10',
                'brand'        => 'HP',
                'model'        => 'EliteBook 840 G10',
                'description'  => 'Laptop haute performance',
                'type'         => 'hardware',
                'condition'    => 'Bon',
                'purchase_date'=> '2023-06-01',
                'warranty_date'=> '2026-06-01',
                'quantity'     => 10,
            ],
            // Ordinateurs de bureau
            [
                'category'     => 'Ordinateurs de bureau',
                'name'         => 'Dell OptiPlex 7010',
                'brand'        => 'Dell',
                'model'        => 'OptiPlex 7010',
                'description'  => 'Ordinateur de bureau compact',
                'type'         => 'hardware',
                'condition'    => 'Neuf',
                'purchase_date'=> '2024-03-01',
                'warranty_date'=> '2027-03-01',
                'quantity'     => 20,
            ],
            // Imprimantes
            [
                'category'     => 'Imprimantes',
                'name'         => 'HP LaserJet Pro M404n',
                'brand'        => 'HP',
                'model'        => 'LaserJet Pro M404n',
                'description'  => 'Imprimante laser monochrome',
                'type'         => 'hardware',
                'condition'    => 'Bon',
                'purchase_date'=> '2023-09-15',
                'warranty_date'=> '2025-09-15',
                'quantity'     => 5,
            ],
            // Périphériques
            [
                'category'     => 'Périphériques',
                'name'         => 'Écran Dell 24"',
                'brand'        => 'Dell',
                'model'        => 'P2422H',
                'description'  => 'Écran professionnel Full HD',
                'type'         => 'hardware',
                'condition'    => 'Neuf',
                'purchase_date'=> '2024-01-15',
                'warranty_date'=> '2027-01-15',
                'quantity'     => 25,
            ],
            [
                'category'     => 'Périphériques',
                'name'         => 'Clavier + Souris Logitech',
                'brand'        => 'Logitech',
                'model'        => 'MK270',
                'description'  => 'Pack clavier souris sans fil',
                'type'         => 'hardware',
                'condition'    => 'Neuf',
                'purchase_date'=> '2024-02-01',
                'warranty_date'=> '2026-02-01',
                'quantity'     => 30,
            ],
            // Réseau
            [
                'category'     => 'Réseau',
                'name'         => 'Switch Cisco 24 ports',
                'brand'        => 'Cisco',
                'model'        => 'SG350-28',
                'description'  => 'Switch manageable 24 ports',
                'type'         => 'hardware',
                'condition'    => 'Bon',
                'purchase_date'=> '2022-11-01',
                'warranty_date'=> '2025-11-01',
                'quantity'     => 3,
            ],
            // Logiciels
            [
                'category'    => 'Logiciels',
                'name'        => 'Microsoft Office 365',
                'brand'       => 'Microsoft',
                'model'       => null,
                'description' => 'Suite bureautique Microsoft',
                'type'        => 'software',
                'version'     => '2024',
                'license_type'=> 'Abonnement',
                'platform'    => 'Windows',
                'publisher'   => 'Microsoft',
                'quantity'    => 50,
            ],
            [
                'category'    => 'Logiciels',
                'name'        => 'Adobe Acrobat Pro',
                'brand'       => 'Adobe',
                'model'       => null,
                'description' => 'Éditeur PDF professionnel',
                'type'        => 'software',
                'version'     => '2024',
                'license_type'=> 'Abonnement',
                'platform'    => 'Windows',
                'publisher'   => 'Adobe',
                'quantity'    => 10,
            ],
            [
                'category'    => 'Logiciels',
                'name'        => 'Windows 11 Pro',
                'brand'       => 'Microsoft',
                'model'       => null,
                'description' => 'Système d\'exploitation',
                'type'        => 'software',
                'version'     => '23H2',
                'license_type'=> 'Perpétuelle',
                'platform'    => 'Windows',
                'publisher'   => 'Microsoft',
                'quantity'    => 45,
            ],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])
                                 ->first();

            // Skip if product already exists
            if (Product::where('name', $data['name'])->exists()) {
                continue;
            }

            $product = Product::create([
                'category_id' => $category->id,
                'name'        => $data['name'],
                'brand'       => $data['brand'] ?? null,
                'model'       => $data['model'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            if ($data['type'] === 'hardware') {
                Hardware::create([
                    'product_id'    => $product->id,
                    'condition'     => $data['condition'] ?? 'Neuf',
                    'purchase_date' => $data['purchase_date'] ?? null,
                    'warranty_date' => $data['warranty_date'] ?? null,
                ]);
            } else {
                Software::create([
                    'product_id'   => $product->id,
                    'version'      => $data['version'] ?? null,
                    'license_type' => $data['license_type'] ?? null,
                    'platform'     => $data['platform'] ?? null,
                    'publisher'    => $data['publisher'] ?? null,
                ]);
            }

            Stock::create([
                'product_id'         => $product->id,
                'quantity_total'     => $data['quantity'],
                'quantity_available' => $data['quantity'],
                'quantity_assigned'  => 0,
            ]);
        }
    }
}