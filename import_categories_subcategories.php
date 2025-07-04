<?php
// Скрипт для импорта categories.json и subcategories.json в PostgreSQL

$host = '5.35.85.110';
$port = 5432;
$db   = 'b2bstorage';
$user = 'b2buser';
$pass = 'B2B_Storage_2024!';

$categoriesFile = __DIR__ . '/b2b-project/frontend/src/assets/categories.json';
$subcategoriesFile = __DIR__ . '/b2b-project/frontend/src/assets/subcategories.json';

function pdo_pgsql() {
    global $host, $port, $db, $user, $pass;
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    return new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}

function import_json($pdo, $file, $table, $fields) {
    $json = json_decode(file_get_contents($file), true);
    if (!$json) {
        echo "Ошибка чтения $file\n";
        return;
    }
    $pdo->beginTransaction();
    $pdo->exec("TRUNCATE TABLE $table RESTART IDENTITY CASCADE");
    $placeholders = '(' . implode(',', array_fill(0, count($fields), '?')) . ')';
    $sql = "INSERT INTO $table (" . implode(',', $fields) . ") VALUES $placeholders";
    $stmt = $pdo->prepare($sql);
    foreach ($json as $row) {
        $values = [];
        foreach ($fields as $f) {
            $values[] = $row[$f] ?? null;
        }
        $stmt->execute($values);
    }
    $pdo->commit();
    echo "Импортировано в $table: ".count($json)." записей\n";
}

function create_tables($pdo) {
    $pdo->exec(<<<SQL
CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY,
    category_id VARCHAR(255),
    name VARCHAR(255),
    icon_url VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    product_count INTEGER,
    group_name VARCHAR(255),
    name_en VARCHAR(255),
    name_ru VARCHAR(255),
    name_uz VARCHAR(255),
    group_name_en VARCHAR(255),
    group_name_ru VARCHAR(255),
    group_name_uz VARCHAR(255)
);
CREATE TABLE IF NOT EXISTS subcategories (
    id INTEGER PRIMARY KEY,
    subcategory_id VARCHAR(255),
    name VARCHAR(255),
    category_id VARCHAR(255),
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    product_count INTEGER,
    name_en VARCHAR(255),
    name_ru VARCHAR(255),
    name_uz VARCHAR(255)
);
SQL);
}

$pdo = pdo_pgsql();
create_tables($pdo);

import_json($pdo, $categoriesFile, 'categories', [
    'id', 'category_id', 'name', 'icon_url', 'created_at', 'updated_at', 'product_count', 'group_name',
    'name_en', 'name_ru', 'name_uz', 'group_name_en', 'group_name_ru', 'group_name_uz'
]);

import_json($pdo, $subcategoriesFile, 'subcategories', [
    'id', 'subcategory_id', 'name', 'category_id', 'created_at', 'updated_at', 'product_count',
    'name_en', 'name_ru', 'name_uz'
]);

echo "Готово!\n"; 