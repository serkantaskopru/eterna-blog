<?php

namespace App\Http\Controllers\Management;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function view(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Categories', [
            'categories' => $categories,
        ]);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return response()->json($category);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $category = Category::create([
                'name' => $request->input('name'),
            ]);
            return Redirect::back()->with([
                'data' => 'Category created successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error creating category', 'error' => $exception->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => [
                'required',
                'exists:categories,id',
            ]
        ]);
        try {
            $category = Category::find($request->input('category_id'));
            $category->name = $request->input('name');
            $category->save();
            return Redirect::back()->with([
                'data' => 'Category updated successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error creating category', 'error' => $exception->getMessage()], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'category_id' => [
                'required',
                'exists:categories,id',
            ]
        ]);
        try {
            Category::where('id',$request->input('category_id'))->delete();
            return Redirect::back()->with([
                'data' => 'Category updated successfully',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Error creating category', 'error' => $exception->getMessage()], 500);
        }
    }
}
