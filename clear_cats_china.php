<?php

// Скрипт для очистки всех значений name_china в cats.json
$inputFile = 'b2b-project/frontend/cats.json';
$outputFile = 'b2b-project/frontend/cats_cleared.json';

// Читаем JSON файл
$jsonContent = file_get_contents($inputFile);
$categories = json_decode($jsonContent, true);

if (!$categories) {
    die("Ошибка при чтении JSON файла\n");
}

echo "Найдено категорий: " . count($categories) . "\n";

// Счетчики
$categoriesProcessed = 0;
$subcategoriesProcessed = 0;

// Обрабатываем каждую категорию
foreach ($categories as &$category) {
    $categoriesProcessed++;
    
    // Очищаем name_china для категории
    $category['name_china'] = '';
    
    // Обрабатываем подкатегории
    if (isset($category['subcategories']) && is_array($category['subcategories'])) {
        foreach ($category['subcategories'] as &$subcategory) {
            $subcategoriesProcessed++;
            
            // Очищаем name_china для подкатегории
            $subcategory['name_china'] = '';
        }
    }
}

// Сохраняем обновленный JSON
$updatedJson = json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents($outputFile, $updatedJson);

echo "Очищено категорий: $categoriesProcessed\n";
echo "Очищено подкатегорий: $subcategoriesProcessed\n";
echo "Файл сохранен как: $outputFile\n";
echo "\nТеперь можно начать качественный перевод вручную.\n"; 