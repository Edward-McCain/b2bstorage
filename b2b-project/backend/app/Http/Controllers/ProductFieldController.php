<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductField;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\ProductSklad;
use Illuminate\Support\Facades\DB;

class ProductFieldController extends Controller
{
    // Получить все кастомные поля пользователя
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        $fields = ProductField::where('user_id', $user->id)->orderBy('id', 'asc')->get();
        return response()->json(['success' => true, 'data' => $fields]);
    }

    // Добавить новое поле
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,number,date,list',
            'list_options' => 'required_if:field_type,list|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        
        $fieldData = [
            'user_id' => $user->id,
            'field_name' => $request->field_name,
            'field_type' => $request->field_type,
        ];
        
        if ($request->field_type === 'list' && $request->has('list_options')) {
            $fieldData['list_options'] = json_encode($request->list_options);
        }
        
        $field = ProductField::create($fieldData);
        return response()->json(['success' => true, 'data' => $field], 201);
    }

    // Обновить поле
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        $field = ProductField::where('id', $id)->where('user_id', $user->id)->first();
        if (!$field) {
            return response()->json(['success' => false, 'message' => 'Поле не найдено'], 404);
        }
        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,number,date,list',
            'list_options' => 'required_if:field_type,list|array',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Ошибка валидации', 'errors' => $validator->errors()], 422);
        }
        $field->field_name = $request->field_name;
        $field->field_type = $request->field_type;
        
        if ($request->field_type === 'list' && $request->has('list_options')) {
            $field->list_options = json_encode($request->list_options);
        } else {
            $field->list_options = null;
        }
        $field->save();
        return response()->json(['success' => true, 'data' => $field]);
    }

    // Удалить поле
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Пользователь не авторизован'], 401);
        }
        $field = ProductField::where('id', $id)->where('user_id', $user->id)->first();
        if (!$field) {
            return response()->json(['success' => false, 'message' => 'Поле не найдено'], 404);
        }
        
        // Проверка: используется ли поле в products_sklad.fields у пользователя
        // Используем более безопасный способ проверки JSONB
        try {
            $used = ProductSklad::where('user_id', $user->id)
                ->whereRaw("fields::jsonb ? ?", [$field->field_name])
                ->exists();
        } catch (\Exception $e) {
            // Если SQL-запрос не работает, используем альтернативный способ
            $used = ProductSklad::where('user_id', $user->id)
                ->whereNotNull('fields')
                ->get()
                ->filter(function($product) use ($field) {
                    $fields = $product->fields; // Уже массив благодаря casts
                    return is_array($fields) && array_key_exists($field->field_name, $fields);
                })
                ->count() > 0;
        }
            
        if ($used) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить поле: оно используется хотя бы в одном товаре.'
            ], 400);
        }
        
        $field->delete();
        return response()->json(['success' => true, 'message' => 'Поле удалено']);
    }
} 