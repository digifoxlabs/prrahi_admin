<?php

namespace App\Services\Orders;

use App\Models\Product;
use App\Models\Distributor;

class OrderCalculationService
{
    public static function calculate(array $items, int $distributorId, float $discount = 0): array
    {
        $distributor = Distributor::findOrFail($distributorId);

        $baseState = config('tax.base_state');
        $isIntraState = strcasecmp(trim($distributor->state), trim($baseState)) === 0;

        $calculatedItems = [];
        $subtotal = 0;

        foreach ($items as $row) {

            $product = Product::findOrFail($row['product_id']);

            $rate = self::resolveRate($product);
            $quantity = (int) $row['quantity'];

            $discount_percent = self::resolveDiscount($product);


            $lineGrossTotal = round($rate * $quantity, 2);

            $lineDiscount = $lineGrossTotal * ($discount_percent/100);

            $lineTotal = $lineGrossTotal - $lineDiscount;

            $calculatedItems[] = [
                'product_id' => $product->id,
                'price' => $rate,
                'base_unit' => $product->base_unit,
                'quantity' => $quantity,
                'discount_percent' => self::resolveDiscount($product),
                'total' => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        // ───────── APPLY ORDER-LEVEL DISCOUNT ─────────
        $discount = max(0, round($discount, 2)); // safety
        $taxableAmount = max(0, $subtotal - $discount);

        // ───────── TAX CALCULATION ─────────

        $cgst = 0;
        $sgst = 0;
        $igst = 0;

        if ($isIntraState) {
            $cgst = round($taxableAmount * config('tax.cgst') / 100, 2);
            $sgst = round($taxableAmount * config('tax.sgst') / 100, 2);
        } else {
            $igst = round($taxableAmount * config('tax.igst') / 100, 2);
        }

        $grossTotal = $taxableAmount + $cgst + $sgst + $igst;

         // ───────── CUSTOM ROUND-OFF LOGIC ─────────
        $integerPart = floor($grossTotal);
        $decimalPart = $grossTotal - $integerPart;

        if ($decimalPart < 0.5) {
            $finalTotal = $integerPart;
        } else {
            $finalTotal = $integerPart + 1;
        }

        $roundOff = round($finalTotal - $grossTotal, 2);

        return [
            'items' => $calculatedItems,
            'subtotal' => round($subtotal, 2),
            'discount' => $discount,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'round_off' => $roundOff,
            'total_amount' => round($grossTotal),
        ];
    }

    private static function resolveRate(Product $product): float
    {
        // Adjust as per your business rule
        return $product->ptd_per_dozen
            ?? 0;
    }

    private static function resolveDiscount(Product $product): float
    {
        return $product->distributor_discount_percent ?? 0;
    }
}
