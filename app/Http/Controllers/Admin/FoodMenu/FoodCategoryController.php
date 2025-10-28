<?php

namespace App\Http\Controllers\Admin\FoodMenu;

use App\Http\Controllers\Controller;
use App\Models\FoodCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FoodCategoryController extends Controller
{
    /*
    *  Function to store data category name into food category table
    */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'new_category' => 'required',
            'category_image' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $imagePath = null;

        if ($request->hasFile('category_image')) {
            $file = $request->file('category_image');
            $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path('images/food-category');

            // ✅ Create folder if it doesn't exist
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true); // 0755 permissions, true = recursive
            }

            $file->move($destinationPath, $fileName);
            $imagePath = 'images/food-category/' . $fileName; // Save relative path
        }


        $validated = $validator->safe()->only('new_category');

        $exists = FoodCategory::where('name', $validated['new_category'])->exists();

        if ($exists) {
            return back()->withErrors([
                'error-message' => 'Category already exists.'
            ]);
        } else {
            $category = FoodCategory::create([
                'name' => $validated['new_category'],
                'image' => $imagePath, 
            ]);

            Log::info([$category]);

            return back()->with('success-message', 'Food Category added successfully.');
        }
    }




    /*
    *  Function to delete resource
    */
    public function destroy($id) : RedirectResponse
    {
        $category = FoodCategory::findOrFail($id);
        
        $category->delete();

        Log::info([$category]);

        return back()->with('success-message', 'Category is deleted successfully.');
    }
}
