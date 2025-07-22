<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class UpdateCurrencyRates extends Command
{
    protected $signature = 'currencies:update';
    protected $description = 'Обновляет курсы валют из внешнего API';

    public function handle()
    {
        $url = 'https://b2bmarket.uz/api/api/currency';
        $response = Http::get($url);

        if (!$response->ok()) {
            $this->error('Ошибка при получении данных');
            return 1;
        }

        $currencies = $response->json();

        // Проверка: если таблица пуста — просто вставляем
        $count = DB::table('currencies')->count();
        if ($count > 0) {
            DB::table('currencies')->truncate();
        }

        foreach ($currencies as $currency) {
            DB::table('currencies')->insert([
                'id' => $currency['id'],
                'currency_id' => $currency['currency_id'],
                'full_name' => $currency['full_name'],
                'currency_type' => $currency['currency_type'],
                'rate' => $currency['rate'],
                'date' => $currency['date'],
                'created_at' => $currency['created_at'],
                'updated_at' => $currency['updated_at'],
            ]);
        }

        $this->info('Курсы валют успешно обновлены');
        return 0;
    }
} 