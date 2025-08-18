<?php

// Скрипт для добавления китайских переводов в cats.json
$inputFile = 'b2b-project/frontend/cats.json';
$outputFile = 'b2b-project/frontend/cats_with_china.json';

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
    
    // Добавляем name_china для категории (пока оставляем пустым)
    $category['name_china'] = '';
    
    // Обрабатываем подкатегории
    if (isset($category['subcategories']) && is_array($category['subcategories'])) {
        foreach ($category['subcategories'] as &$subcategory) {
            $subcategoriesProcessed++;
            
            // Добавляем name_china для подкатегории (пока оставляем пустым)
            $subcategory['name_china'] = '';
        }
    }
}

// Сохраняем обновленный JSON
$updatedJson = json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents($outputFile, $updatedJson);

echo "Обработано категорий: $categoriesProcessed\n";
echo "Обработано подкатегорий: $subcategoriesProcessed\n";
echo "Файл сохранен как: $outputFile\n";
echo "\nТеперь нужно заполнить китайские переводы вручную или с помощью переводчика.\n";
echo "После заполнения переименуйте файл в cats.json\n"; 