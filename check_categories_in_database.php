<?php
/**
 * Проверка категорий в базе данных
 * Запуск: php check_categories_in_database.php
 */

// Конфигурация базы данных
$host = 'localhost';
$dbname = 'b2bstorage';
$username = 'postgres';
$password = 'your_password';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔍 ПРОВЕРКА КАТЕГОРИЙ В БАЗЕ ДАННЫХ\n";
    echo "====================================\n\n";
    
    // 1. Проверяем системные категории
    echo "📋 1. СИСТЕМНЫЕ КАТЕГОРИИ\n";
    echo "-------------------------\n";
    
    $stmt = $pdo->query("SELECT category_id, name_ru FROM categories ORDER BY category_id LIMIT 10");
    $systemCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Найдено системных категорий: " . count($systemCategories) . "\n";
    foreach ($systemCategories as $cat) {
        echo "  {$cat['category_id']}: {$cat['name_ru']}\n";
    }
    
    // 2. Проверяем пользовательские категории
    echo "\n📋 2. ПОЛЬЗОВАТЕЛЬСКИЕ КАТЕГОРИИ\n";
    echo "--------------------------------\n";
    
    $stmt = $pdo->query("SELECT category_id, name, user_id FROM user_categories ORDER BY category_id LIMIT 10");
    $userCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Найдено пользовательских категорий: " . count($userCategories) . "\n";
    foreach ($userCategories as $cat) {
        echo "  {$cat['category_id']}: {$cat['name']} (пользователь: {$cat['user_id']})\n";
    }
    
    // 3. Проверяем товары с категориями
    echo "\n📋 3. ТОВАРЫ С КАТЕГОРИЯМИ\n";
    echo "----------------------------\n";
    
    $stmt = $pdo->query("
        SELECT 
            id, 
            name, 
            category, 
            subcategory,
            user_id
        FROM products_sklad 
        WHERE category IS NOT NULL OR subcategory IS NOT NULL
        ORDER BY id 
        LIMIT 20
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Найдено товаров с категориями: " . count($products) . "\n\n";
    
    foreach ($products as $product) {
        echo "📦 Товар: {$product['name']} (ID: {$product['id']}, пользователь: {$product['user_id']})\n";
        echo "  Категория: {$product['category']}\n";
        echo "  Подкатегория: {$product['subcategory']}\n";
        
        // Проверяем, есть ли категория в системных
        if ($product['category']) {
            $stmt = $pdo->prepare("SELECT name_ru FROM categories WHERE category_id = ?");
            $stmt->execute([$product['category']]);
            $systemCat = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($systemCat) {
                echo "  ✅ Категория найдена в системных: {$systemCat['name_ru']}\n";
            } else {
                echo "  ❌ Категория НЕ найдена в системных\n";
            }
            
            // Проверяем, есть ли категория в пользовательских
            $stmt = $pdo->prepare("SELECT name FROM user_categories WHERE category_id = ? AND user_id = ?");
            $stmt->execute([$product['category'], $product['user_id']]);
            $userCat = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userCat) {
                echo "  ✅ Категория найдена в пользовательских: {$userCat['name']}\n";
            } else {
                echo "  ❌ Категория НЕ найдена в пользовательских\n";
            }
        }
        
        // Проверяем подкатегорию
        if ($product['subcategory']) {
            $stmt = $pdo->prepare("SELECT name_ru FROM subcategories WHERE subcategory_id = ?");
            $stmt->execute([$product['subcategory']]);
            $systemSubcat = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($systemSubcat) {
                echo "  ✅ Подкатегория найдена в системных: {$systemSubcat['name_ru']}\n";
            } else {
                echo "  ❌ Подкатегория НЕ найдена в системных\n";
            }
            
            // Проверяем, есть ли подкатегория в пользовательских
            $stmt = $pdo->prepare("SELECT name FROM user_subcategories WHERE subcategory_id = ? AND user_id = ?");
            $stmt->execute([$product['subcategory'], $product['user_id']]);
            $userSubcat = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userSubcat) {
                echo "  ✅ Подкатегория найдена в пользовательских: {$userSubcat['name']}\n";
            } else {
                echo "  ❌ Подкатегория НЕ найдена в пользовательских\n";
            }
        }
        
        echo "\n";
    }
    
    // 4. Статистика
    echo "📋 4. СТАТИСТИКА\n";
    echo "-----------------\n";
    
    // Товары с системными категориями
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM products_sklad p 
        JOIN categories c ON p.category = c.category_id
    ");
    $systemCatCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Товаров с системными категориями: {$systemCatCount}\n";
    
    // Товары с пользовательскими категориями
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM products_sklad p 
        JOIN user_categories uc ON p.category = uc.category_id AND p.user_id = uc.user_id
    ");
    $userCatCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Товаров с пользовательскими категориями: {$userCatCount}\n";
    
    // Товары с неопределенными категориями
    $stmt = $pdo->query("
        SELECT COUNT(*) as count 
        FROM products_sklad p 
        WHERE p.category IS NOT NULL 
        AND p.category NOT IN (SELECT category_id FROM categories)
        AND p.category NOT IN (SELECT category_id FROM user_categories WHERE user_id = p.user_id)
    ");
    $undefinedCatCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "Товаров с неопределенными категориями: {$undefinedCatCount}\n";
    
    echo "\n🎯 ПРОВЕРКА ЗАВЕРШЕНА!\n";
    
} catch (PDOException $e) {
    echo "❌ Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
} 