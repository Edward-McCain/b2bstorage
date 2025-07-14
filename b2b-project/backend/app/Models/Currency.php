<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'currency_id',
        'full_name',
        'currency_type',
        'rate',
        'date',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'date' => 'datetime',
    ];

    /**
     * Get currency by type
     */
    public static function getByType($type)
    {
        return static::where('currency_type', $type)->first();
    }

    /**
     * Get latest rates for all currencies
     */
    public static function getLatestRates()
    {
        return static::orderBy('date', 'desc')
            ->orderBy('currency_type')
            ->get();
    }

    /**
     * Convert amount from one currency to another
     */
    public static function convert($amount, $fromCurrency, $toCurrency)
    {
        $from = static::getByType($fromCurrency);
        $to = static::getByType($toCurrency);

        if (!$from || !$to) {
            return null;
        }

        // Convert to USD first (base currency), then to target currency
        $usdAmount = $amount / $from->rate;
        return $usdAmount * $to->rate;
    }
} 