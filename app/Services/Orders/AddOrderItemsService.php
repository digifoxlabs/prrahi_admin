<?php

namespace App\Services\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Exception;

class AddOrderItemsService
{
    public static function handle(Order $order, array $items): void
    {
        DB::transaction(function () use ($order, $items) {

            $subtotal = 0;

            foreach ($items as $row) {

                $product_id  = $row['product_id']; // already resolved Product model
                $qty      = (int) $row['quantity'];
                $price    = (float) $row['price'];
                $baseUnit  = $row['base_unit'];
                $discountPercent  = $row['discount_percent'];

                if ($qty <= 0) {
                    throw new Exception('Invalid quantity');
                }

                // OPTIONAL: validate master inventory here
                // InventoryValidator::check($product, $qty);

                $lineGross = $qty * $price;
                $lineDiscount = $lineGross * ($discountPercent / 100);
                $lineTotal = $lineGross - $lineDiscount;

                $order->items()->create([
                    'order_id'  =>  $order->id,
                    'product_id' => $product_id,
                    'rate'       => $price,
                    'base_unit'     => $baseUnit,
                    'quantity'   => $qty,
                    'discount_percent'   => $discountPercent,
                    'total'      => $lineTotal,
                ]);

                $subtotal += $lineTotal;
            }

            // tax calculation (adjust if needed)
            // $sgst  = round($subtotal * 0.025, 2);
            // $cgst  = round($subtotal * 0.025, 2);
            // $total = $subtotal + $sgst + $cgst - $order->discount;

            // $order->update([
            //     'subtotal'     => $subtotal,
            //     'sgst'         => $sgst,
            //     'cgst'         => $cgst,
            //     'total_amount' => $total,
            // ]);


        });
    }
}
