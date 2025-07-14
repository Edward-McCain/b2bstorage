<?php

namespace App\Console\Commands;

use App\Http\Controllers\CurrencyController;
use Illuminate\Console\Command;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency rates from external API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating currency rates...');
        
        $controller = new CurrencyController();
        $response = $controller->fetchAndSaveRates();
        
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            $this->info('Currency rates updated successfully. Count: ' . $data['count']);
        } else {
            $this->error('Failed to update currency rates');
        }
    }
} 