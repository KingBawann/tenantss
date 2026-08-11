<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            Category::create($validated);
            session()->flash('success', 'Category created successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create category: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('categories.index');
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $category->update($validated);
            session()->flash('success', 'Category updated successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update category: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();
            session()->flash('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete category: ' . $e->getMessage());
        }
        return redirect()->route('categories.index');
    }
}
