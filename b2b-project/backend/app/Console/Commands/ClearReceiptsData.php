<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearReceiptsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipts:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистить все данные из таблиц оприходований';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начинаем очистку данных из таблиц оприходований...');

        try {
            DB::beginTransaction();

            // Удаляем файлы оприходований
            $filesDeleted = DB::table('receipt_files')->delete();
            $this->info("Удалено записей из receipt_files: {$filesDeleted}");

            // Удаляем позиции оприходований
            $positionsDeleted = DB::table('receipt_positions')->delete();
            $this->info("Удалено записей из receipt_positions: {$positionsDeleted}");

            // Удаляем оприходования
            $receiptsDeleted = DB::table('receipts')->delete();
            $this->info("Удалено записей из receipts: {$receiptsDeleted}");

            DB::commit();

            $this->info('Очистка завершена успешно!');

            // Показываем статистику
            $this->table(
                ['Таблица', 'Количество записей'],
                [
                    ['receipts', DB::table('receipts')->count()],
                    ['receipt_positions', DB::table('receipt_positions')->count()],
                    ['receipt_files', DB::table('receipt_files')->count()],
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Ошибка при очистке данных: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 