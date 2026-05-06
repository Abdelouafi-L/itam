<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\AssignmentDetail;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('employee', fn($q) =>
            $q->where('email', 'admin@techcorp.ma')
        )->first();

        $tech = User::whereHas('employee', fn($q) =>
            $q->where('email', 'tech@techcorp.ma')
        )->first();

        if (!$admin || !$tech) return;

        $assignments = [
            [
                'employee_email' => 'employe@techcorp.ma',
                'created_by'     => $admin->id,
                'assigned_at'    => now()->subDays(30),
                'status'         => 'Active',
                'notes'          => 'Équipement poste de travail',
                'products'       => [
                    ['name' => 'Dell Latitude 5540',    'qty' => 1],
                    ['name' => 'Écran Dell 24"',         'qty' => 1],
                    ['name' => 'Clavier + Souris Logitech', 'qty' => 1],
                ],
            ],
            [
                'employee_email' => 'k.idrissi@techcorp.ma',
                'created_by'     => $tech->id,
                'assigned_at'    => now()->subDays(15),
                'status'         => 'Active',
                'notes'          => 'Nouveau poste IT',
                'products'       => [
                    ['name' => 'HP EliteBook 840 G10',  'qty' => 1],
                    ['name' => 'Écran Dell 24"',         'qty' => 2],
                ],
            ],
            [
                'employee_email' => 'a.mansouri@techcorp.ma',
                'created_by'     => $admin->id,
                'assigned_at'    => now()->subDays(60),
                'status'         => 'Clôturée',
                'returned_at'    => now()->subDays(10),
                'notes'          => 'Affectation temporaire',
                'products'       => [
                    ['name' => 'Dell Latitude 5540', 'qty' => 1],
                ],
            ],
        ];

        foreach ($assignments as $data) {
            $employee = Employee::where('email', $data['employee_email'])
                                ->first();
            if (!$employee) continue;

            $assignment = Assignment::create([
                'employee_id' => $employee->id,
                'created_by'  => $data['created_by'],
                'assigned_at' => $data['assigned_at'],
                'returned_at' => $data['returned_at'] ?? null,
                'status'      => $data['status'],
                'notes'       => $data['notes'],
            ]);

            foreach ($data['products'] as $line) {
                $product = Product::where('name', $line['name'])->first();
                if (!$product || !$product->stock) continue;

                $qty = $line['qty'];

                // Only update stock for active assignments
                if ($data['status'] === 'Active') {
                    $stock = $product->stock;
                    if ($stock->quantity_available >= $qty) {
                        $stock->update([
                            'quantity_available' => $stock->quantity_available - $qty,
                            'quantity_assigned'  => $stock->quantity_assigned + $qty,
                        ]);
                    }
                }

                AssignmentDetail::create([
                    'assignment_id' => $assignment->id,
                    'product_id'    => $product->id,
                    'quantity'      => $qty,
                    'returned_qty'  => $data['status'] === 'Clôturée'
                                       ? $qty : 0,
                ]);
            }
        }
    }
}