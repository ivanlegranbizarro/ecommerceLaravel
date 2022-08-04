<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        /*
         * Get all categories
         */
        return response([
            'categories' => new CategoryResource(Category::all())
        ]);
    }

    public function show(Category $category)
    {
        /*
         * Return a single category
         */
        return response([
            'category' => new CategoryResource($category)
        ]);
    }

    public function store(Request $request)
    {
        /*
         * Create a new category
         */

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255'
        ]);

        if ($category = Category::create($data)) {
            return response([
                'category' => new CategoryResource($category)
            ], 201);
        }

        return response([
            'message' => 'Something went wrong'
        ], 500);
    }

    public function update(Request $request, Category $category)
    {
        /*
         * Update a category
         */
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255'
            ]);

            $category->update($data);

            return response([
                'category' => new CategoryResource($category)
            ], 200);
        }
        catch (\Exception $e) {
            return response([
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        /*
         * Delete a category
         */
        try {
            $category->delete();
            return response([
                'message' => 'Category deleted'
            ], 200);
        }
        catch (\Throwable $e) {
            return response([
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
