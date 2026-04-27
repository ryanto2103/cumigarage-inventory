<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('toys')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100|unique:categories,name',
            'color' => 'required|string|max:7',
        ]);
        Category::create($validated);
        return back()->with('success', "Kategori \"{$validated['name']}\" berhasil ditambahkan!");
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:100|unique:categories,name,' . $category->id,
            'color' => 'required|string|max:7',
        ]);
        $category->update($validated);
        return back()->with('success', "Kategori berhasil diperbarui!");
    }

    public function destroy(Category $category)
    {
        if ($category->toys()->count() > 0) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena masih ada mainan di dalamnya.');
        }
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
