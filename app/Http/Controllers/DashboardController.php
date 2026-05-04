<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\License;
use App\Models\Maintenance;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the real dashboard with live statistics.
     * RF-24: Global dashboard.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------
        | Stock Statistics
        |--------------------------------------------------------------
        */

        // Total products in the system
        $totalProducts = Product::count();

        // Total items across all stock
        $totalItems = Stock::sum('quantity_total');

        // Available items
        $totalAvailable = Stock::sum('quantity_available');

        // Assigned items
        $totalAssigned = Stock::sum('quantity_assigned');

        /*
        |--------------------------------------------------------------
        | Stock by Category — for chart
        |--------------------------------------------------------------
        */
        $stockByCategory = Category::withCount('products')
            ->having('products_count', '>', 0)
            ->get()
            ->map(fn($c) => [
                'name'  => $c->name,
                'count' => $c->products_count,
            ]);

        /*
        |--------------------------------------------------------------
        | License Alerts — RF-20
        |--------------------------------------------------------------
        */

        // Licenses expiring within 30 days
        $expiringLicenses = License::with(['software.product'])
            ->where('status', 'Active')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now()->startOfDay())
            ->whereDate('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->get();

        // Licenses out of seats
        $outOfSeatsLicenses = License::with(['software.product'])
            ->where('status', 'Active')
            ->whereColumn('seats_used', '>=', 'seats_total')
            ->get();

        /*
        |--------------------------------------------------------------
        | Recent Assignments
        |--------------------------------------------------------------
        */
        $recentAssignments = Assignment::with([
            'employee',
            'details.product',
        ])
        ->orderByDesc('assigned_at')
        ->limit(5)
        ->get();

        /*
        |--------------------------------------------------------------
        | Maintenance Statistics — RF-22
        |--------------------------------------------------------------
        */

        // Total maintenance cost
        $totalMaintenanceCost = Maintenance::sum('cost');

        // Maintenances in progress
        $maintenancesInProgress = Maintenance::where('status', 'En cours')
                                             ->count();

        // Recent maintenances
        $recentMaintenances = Maintenance::with(['hardware.product'])
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------
        | Assignment Statistics — for chart
        |--------------------------------------------------------------
        */
        $assignmentStats = [
            'active'   => Assignment::where('status', 'Active')->count(),
            'closed'   => Assignment::where('status', 'Clôturée')->count(),
        ];

        return view('dashboard', compact(
            'totalProducts',
            'totalItems',
            'totalAvailable',
            'totalAssigned',
            'stockByCategory',
            'expiringLicenses',
            'outOfSeatsLicenses',
            'recentAssignments',
            'totalMaintenanceCost',
            'maintenancesInProgress',
            'recentMaintenances',
            'assignmentStats',
        ));
    }
}