<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = DB::table('categories')
            ->select('id', 'category_id', 'name_ru as name')
            ->orderBy('name_ru')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function subcategories(Request $request, $id = null)
    {
        $category_id = $id ?: $request->query('category_id');
        if (!$category_id) {
            return response()->json(['error' => 'ID категории не указан'], 400);
        }
        
        $subcategories = DB::table('subcategories')
            ->select('id', 'subcategory_id', 'name_ru as name')
            ->where('category_id', $category_id)
            ->orderBy('name_ru')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $subcategories
        ]);
    }
} 