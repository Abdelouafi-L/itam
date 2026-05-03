<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of all categories.
     * Raw PHP equivalent: SELECT * FROM categories
     */
    public function index()
    {
        $categories = Category::withCount('products')
                               ->orderBy('name')
                               ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in storage.
     * Raw PHP equivalent: INSERT INTO categories (name, description) VALUES (?, ?)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100',
                              'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Display the specified category.
     * Raw PHP equivalent: SELECT * FROM categories WHERE id = ?
     */
    public function show(Category $category)
    {
        // Load products belonging to this category
        $category->load('products');

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     * Raw PHP equivalent: UPDATE categories SET name=?, description=? WHERE id=?
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100',
                              'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Remove the specified category from storage.
     * Raw PHP equivalent: DELETE FROM categories WHERE id = ?
     * Protected by restrictOnDelete() — cannot delete if products exist.
     */
    public function destroy(Category $category)
    {
        // Check if category has products before attempting delete
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('categories.index')
                ->with('error',
                    'Impossible de supprimer cette catégorie — 
                     elle contient ' . $category->products()->count() .
                    ' produit(s).'
                );
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}