<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Hardware;
use App\Models\Product;
use App\Models\Software;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of all products.
     */
    public function index(Request $request)
    {
        $query = Product::with([
            'category', 'hardware', 'software', 'stock'
        ]);

        // Search by name, brand or model
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            if ($request->type === 'hardware') {
                $query->whereHas('hardware');
            } elseif ($request->type === 'software') {
                $query->whereHas('software');
            }
        }

        $products   = $query->orderBy('name')->paginate(15)
                            ->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('products.index',
                    compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     * Creates Product + Hardware/Software + Stock in one transaction.
     * Stock starts at 0 — units only enter through validated deliveries.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'brand'        => ['nullable', 'string', 'max:100'],
            'model'        => ['nullable', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'type'         => ['required', 'in:hardware,software'],

            // Hardware specific fields
            'warranty_date'  => ['nullable', 'date'],
            'condition'      => ['nullable', 'string',
                                 'in:Neuf,Bon,Usagé,Endommagé'],
            'purchase_date'  => ['nullable', 'date'],

            // Software specific fields
            'version'        => ['nullable', 'string', 'max:50'],
            'license_type'   => ['nullable', 'string', 'max:100'],
            'platform'       => ['nullable', 'string', 'max:100'],
            'publisher'      => ['nullable', 'string', 'max:100'],
            'release_date'   => ['nullable', 'date'],

            // NOTE: No quantity_total here — stock starts at 0
            // Units only enter through validated Livraisons (RF-35)
        ]);

        DB::transaction(function () use ($validated) {

            // Create the parent Product record
            $product = Product::create([
                'category_id' => $validated['category_id'],
                'name'        => $validated['name'],
                'brand'       => $validated['brand'] ?? null,
                'model'       => $validated['model'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // Create Hardware or Software child record
            if ($validated['type'] === 'hardware') {
                Hardware::create([
                    'product_id'    => $product->id,
                    'warranty_date' => $validated['warranty_date'] ?? null,
                    'condition'     => $validated['condition'] ?? 'Neuf',
                    'purchase_date' => $validated['purchase_date'] ?? null,
                ]);
            } else {
                Software::create([
                    'product_id'   => $product->id,
                    'version'      => $validated['version'] ?? null,
                    'license_type' => $validated['license_type'] ?? null,
                    'platform'     => $validated['platform'] ?? null,
                    'publisher'    => $validated['publisher'] ?? null,
                    'release_date' => $validated['release_date'] ?? null,
                ]);
            }

            // Stock starts at 0 — never populated manually
            // Increases only through Livraison → Réceptionnée (RF-35)
            Stock::create([
                'product_id'         => $product->id,
                'quantity_total'     => 0,
                'quantity_available' => 0,
                'quantity_assigned'  => 0,
            ]);
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit créé avec succès. Créez une livraison pour approvisionner le stock.');
    }

    /**
     * Display the specified product with full details.
     */
    public function show(Product $product)
    {
        $product->load([
            'category',
            'hardware',
            'software',
            'software.license',
            'stock',
        ]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load(['hardware', 'software', 'stock']);
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     * NOTE: Stock is NOT updated here — only through Livraisons.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'name'         => ['required', 'string', 'max:255'],
            'brand'        => ['nullable', 'string', 'max:100'],
            'model'        => ['nullable', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:1000'],

            // Hardware fields
            'warranty_date'  => ['nullable', 'date'],
            'condition'      => ['nullable', 'string',
                                 'in:Neuf,Bon,Usagé,Endommagé'],
            'purchase_date'  => ['nullable', 'date'],

            // Software fields
            'version'        => ['nullable', 'string', 'max:50'],
            'license_type'   => ['nullable', 'string', 'max:100'],
            'platform'       => ['nullable', 'string', 'max:100'],
            'publisher'      => ['nullable', 'string', 'max:100'],
            'release_date'   => ['nullable', 'date'],

            // NOTE: No quantity_total — stock managed by Livraisons only
        ]);

        DB::transaction(function () use ($validated, $product) {

            // Update parent product
            $product->update([
                'category_id' => $validated['category_id'],
                'name'        => $validated['name'],
                'brand'       => $validated['brand'] ?? null,
                'model'       => $validated['model'] ?? null,
                'description' => $validated['description'] ?? null,
            ]);

            // Update Hardware or Software child
            if ($product->hardware) {
                $product->hardware->update([
                    'warranty_date' => $validated['warranty_date'] ?? null,
                    'condition'     => $validated['condition'] ?? 'Neuf',
                    'purchase_date' => $validated['purchase_date'] ?? null,
                ]);
            } elseif ($product->software) {
                $product->software->update([
                    'version'      => $validated['version'] ?? null,
                    'license_type' => $validated['license_type'] ?? null,
                    'platform'     => $validated['platform'] ?? null,
                    'publisher'    => $validated['publisher'] ?? null,
                    'release_date' => $validated['release_date'] ?? null,
                ]);
            }

            // Stock is intentionally NOT updated here
            // All stock changes go through Livraisons (RF-35/RF-36)
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->assignmentDetails()->count() > 0) {
            return redirect()
                ->route('products.index')
                ->with('error',
                    'Impossible de supprimer ce produit — 
                     il est lié à des affectations existantes.'
                );
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
}