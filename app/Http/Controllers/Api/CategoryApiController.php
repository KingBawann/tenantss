<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')
            ->paginate($request->query('per_page', 15));

        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        return new CategoryResource(Category::create($validated));
    }

    public function show(Category $category)
    {
        return new CategoryResource($category->loadCount('products'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $category->update($validated);

        return new CategoryResource($category->fresh()->loadCount('products'));
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Category deleted.'], 200);
    }
}
