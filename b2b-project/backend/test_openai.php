<?php

require_once 'vendor/autoload.php';

// Загружаем переменные окружения
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "Проверка подключения к OpenAI API...\n";
echo "API Key: " . substr($_ENV['OPENAI_API_KEY'], 0, 20) . "...\n";

try {
    $client = OpenAI::client($_ENV['OPENAI_API_KEY']);
    
    echo "Клиент OpenAI создан успешно\n";
    
    // Простой тестовый запрос
    $response = $client->chat()->create([
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'user', 'content' => 'Привет! Как дела?']
        ],
        'max_tokens' => 50
    ]);
    
    echo "Ответ от OpenAI: " . $response->choices[0]->message->content . "\n";
    echo "Тест успешен!\n";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 