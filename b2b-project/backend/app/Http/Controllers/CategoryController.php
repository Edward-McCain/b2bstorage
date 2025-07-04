<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function categories()
    {
        $categories = DB::table('categories')->orderBy('name_ru')->get();
        return response()->json($categories);
    }

    public function subcategories(Request $request)
    {
        $category_id = $request->query('category_id');
        if (!$category_id) {
            return response()->json([], 400);
        }
        $subcategories = DB::table('subcategories')
            ->where('category_id', $category_id)
            ->orderBy('name_ru')
            ->get();
        return response()->json($subcategories);
    }
} 