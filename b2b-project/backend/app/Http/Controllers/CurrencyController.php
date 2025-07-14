<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyController extends Controller
{
    /**
     * Get currency rates from external API and save to database
     */
    public function fetchAndSaveRates(): JsonResponse
    {
        try {
            $response = Http::get('https://b2bmarket.uz/api/api/currency');
            
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch currency rates from external API'
                ], 500);
            }

            $currencies = $response->json();
            
            foreach ($currencies as $currencyData) {
                Currency::updateOrCreate(
                    ['currency_id' => $currencyData['currency_id']],
                    [
                        'full_name' => $currencyData['full_name'],
                        'currency_type' => $currencyData['currency_type'],
                        'rate' => $currencyData['rate'],
                        'date' => $currencyData['date'],
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Currency rates updated successfully',
                'count' => count($currencies)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching currency rates: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching currency rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all currency rates from database
     */
    public function getRates(): JsonResponse
    {
        try {
            $currencies = Currency::getLatestRates();
            
            return response()->json([
                'success' => true,
                'data' => $currencies
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting currency rates: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting currency rates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get currency rate by type
     */
    public function getRateByType(Request $request): JsonResponse
    {
        $request->validate([
            'currency_type' => 'required|string|max:10'
        ]);

        try {
            $currency = Currency::getByType($request->currency_type);
            
            if (!$currency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Currency not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $currency
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting currency rate: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting currency rate: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert amount between currencies
     */
    public function convert(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'from_currency' => 'required|string|max:10',
            'to_currency' => 'required|string|max:10'
        ]);

        try {
            $convertedAmount = Currency::convert(
                $request->amount,
                $request->from_currency,
                $request->to_currency
            );

            if ($convertedAmount === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to convert currencies. Check if both currencies exist.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'original_amount' => $request->amount,
                    'from_currency' => $request->from_currency,
                    'to_currency' => $request->to_currency,
                    'converted_amount' => round($convertedAmount, 2)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error converting currency: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error converting currency: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user's preferred currency
     */
    public function updateUserCurrency(Request $request): JsonResponse
    {
        $request->validate([
            'currency' => 'required|string|max:10'
        ]);

        try {
            $user = $request->user();
            $user->currency = $request->currency;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User currency updated successfully',
                'data' => [
                    'currency' => $user->currency
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating user currency: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating user currency: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's preferred currency
     */
    public function getUserCurrency(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'currency' => $user->currency
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting user currency: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error getting user currency: ' . $e->getMessage()
            ], 500);
        }
    }
} 