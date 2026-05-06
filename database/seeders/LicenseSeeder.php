<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Product;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder
{
    public function run(): void
    {
        $licenses = [
            [
                'product'       => 'Microsoft Office 365',
                'seats_total'   => 50,
                'seats_used'    => 32,
                'purchase_date' => '2024-01-01',
                'expiry_date'   => now()->addDays(20)->format('Y-m-d'),
                'cost'          => 25000.00,
                'status'        => 'Active',
            ],
            [
                'product'       => 'Adobe Acrobat Pro',
                'seats_total'   => 10,
                'seats_used'    => 7,
                'purchase_date' => '2024-03-01',
                'expiry_date'   => now()->addDays(90)->format('Y-m-d'),
                'cost'          => 8500.00,
                'status'        => 'Active',
            ],
            [
                'product'       => 'Windows 11 Pro',
                'seats_total'   => 45,
                'seats_used'    => 40,
                'purchase_date' => '2022-01-01',
                'expiry_date'   => null,
                'cost'          => 45000.00,
                'status'        => 'Active',
            ],
        ];

        foreach ($licenses as $data) {
            $product = Product::where('name', $data['product'])->first();
            if (!$product || !$product->software) continue;

            License::firstOrCreate(
                ['software_id' => $product->software->id],
                [
                    'seats_total'   => $data['seats_total'],
                    'seats_used'    => $data['seats_used'],
                    'purchase_date' => $data['purchase_date'],
                    'expiry_date'   => $data['expiry_date'],
                    'cost'          => $data['cost'],
                    'status'        => $data['status'],
                ]
            );
        }
    }
}