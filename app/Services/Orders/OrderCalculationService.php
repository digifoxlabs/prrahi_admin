<?php

namespace App\Services\Orders;

use App\Models\Product;
use App\Models\Distributor;
use App\Models\Retailer;

class OrderCalculationService
{
    /* =========================================================
     |  PUBLIC ENTRY POINTS (BUSINESS INTENT)
     ========================================================= */

    public static function calculateForDistributor(
        array $items,
        int $distributorId,
        float $discount = 0
    ): array {
        $distributor = Distributor::findOrFail($distributorId);

        return self::calculate(
            items: $items,
            state: $distributor->state,
            discount: $discount,
            pricing: 'distributor'
        );
    }

    public static function calculateForRetailer(
        array $items,
        int $retailerId,
        float $discount = 0
    ): array {
        $retailer = Retailer::findOrFail($retailerId);

        return self::calculate(
            items: $items,
            state: $retailer->state,
            discount: $discount,
            pricing: 'retailer'
        );
    }

    /* =========================================================
     |  CORE CALCULATION ENGINE (PRIVATE)
     ========================================================= */

    private static function calculate(
        array $items,
        string $state,
        float $discount,
        string $pricing
    ): array {
        $baseState = config('tax.base_state');
        $isIntraState = strcasecmp(trim($state), trim($baseState)) === 0;

        $subtotal = 0;
        $calculatedItems = [];

        foreach ($items as $row) {
            $product = Product::findOrFail($row['product_id']);

            $rate = self::resolveRate($product, $pricing);
            $discountPercent = self::resolveDiscount($product, $pricing);
            $quantity = (int) $row['quantity'];

            $lineGross = round($rate * $quantity, 2);
            $lineDiscount = round($lineGross * ($discountPercent / 100), 2);
            $lineTotal = round($lineGross - $lineDiscount, 2);

            // $calculatedItems[] = [
            //     'product_id'       => $product->id,
            //     'price'            => $rate,
            //     'base_unit'        => $product->base_unit,
            //     'quantity'         => $quantity,
            //     'discount_percent' => $discountPercent,
            //     'total'            => $lineTotal,
            // ];

            // 🔹 Variant handling
            $displayName = $product->name ?? 'Product';

            // 🔹 Variant handling
            if ($product->type === 'variant' && $product->parent) {

                $attributes = $product->attributes;

                if (is_array($attributes) && !empty($attributes)) {
                    $attrText = implode(' / ', array_values($attributes));
                    $displayName = $product->parent->name . " ({$attrText})";
                } else {
                    $displayName = $product->parent->name;
                }
            }

            $calculatedItems[] = [
                'product_id'       => $product->id,
                'name'             => $displayName, // ✅ REQUIRED
                'price'            => $rate,
                'base_unit'        => $product->base_unit,
                'quantity'         => $quantity,
                'discount_percent' => $discountPercent,
                'total'            => $lineTotal,
            ];

            $subtotal += $lineTotal;
        }

        /* ---------- ORDER LEVEL DISCOUNT ---------- */
        $discount = max(0, round($discount, 2));
        $taxableAmount = max(0, $subtotal - $discount);

        /* ---------- TAX CALCULATION ---------- */
        [$cgst, $sgst, $igst] = self::calculateTax(
            taxableAmount: $taxableAmount,
            isIntraState: $isIntraState
        );

        $grossTotal = $taxableAmount + $cgst + $sgst + $igst;

        /* ---------- CUSTOM ROUND OFF ---------- */
        [$finalTotal, $roundOff] = self::roundOff($grossTotal);

        return [
            'items'        => $calculatedItems,
            'subtotal'     => round($subtotal, 2),
            'discount'     => $discount,
            'cgst'         => $cgst,
            'sgst'         => $sgst,
            'igst'         => $igst,
            'round_off'    => $roundOff,
            'total_amount' => $finalTotal,
        ];
    }

    /* =========================================================
     |  HELPERS
     ========================================================= */

    private static function resolveRate(Product $product, string $pricing): float
    {
        return match ($pricing) {
            'retailer'    => $product->ptr_per_dozen ?? 0,
            'distributor' => $product->ptd_per_dozen ?? 0,
            default       => 0,
        };
    }

    private static function resolveDiscount(Product $product, string $pricing): float
    {
        return match ($pricing) {
            'retailer'    => $product->retailer_discount_percent ?? 0,
            'distributor' => $product->distributor_discount_percent ?? 0,
            default       => 0,
        };
    }

    private static function calculateTax(
        float $taxableAmount,
        bool $isIntraState
    ): array {
        if ($isIntraState) {
            return [
                round($taxableAmount * config('tax.cgst') / 100, 2),
                round($taxableAmount * config('tax.sgst') / 100, 2),
                0,
            ];
        }

        return [
            0,
            0,
            round($taxableAmount * config('tax.igst') / 100, 2),
        ];
    }

    private static function roundOff(float $amount): array
    {
        $integerPart = floor($amount);
        $decimalPart = $amount - $integerPart;

        $finalTotal = $decimalPart < 0.5
            ? $integerPart
            : $integerPart + 1;

        $roundOff = round($finalTotal - $amount, 2);

        return [$finalTotal, $roundOff];
    }
}
