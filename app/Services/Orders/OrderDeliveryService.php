<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\DistributorProduct;
use App\Models\DistributorStock;
use App\Models\DistributorInventoryTransaction;
use Illuminate\Support\Facades\DB;

class OrderDeliveryService
{
    public static function deliver(Order $order): void
    {
        DB::transaction(function () use ($order) {

            foreach ($order->items as $item) {

                $product = $item->product;

                /**
                 * 🔹 DERIVE PRODUCT NAME (simple / variant safe)
                 */
                $baseName = $product->type === 'variant'
                    ? $product->parent->name
                    : $product->name;

                if (is_array($product->attributes)) {
                    $attr = trim(
                        ($product->attributes['fragrance'] ?? '') .
                        (!empty($product->attributes['size'])
                            ? ' (' . $product->attributes['size'] . ')'
                            : '')
                    );

                    if ($attr) {
                        $baseName .= ' — ' . $attr;
                    }
                }

                /**
                 * 🔹 CREATE / FETCH DISTRIBUTOR PRODUCT (SNAPSHOT)
                 */
                $distProduct = DistributorProduct::firstOrCreate(
                    [
                        'distributor_id' => $order->distributor_id,
                        'product_id'     => $product->id,
                    ],
                    [
                        'product_name' => $baseName,
                        'sku'          => $product->code ?? null,
                        'variant'      => $product->type === 'variant'
                            ? json_encode($product->attributes)
                            : null,
                        'mrp'          => $product->mrp_per_unit ?? null,
                        'ptr'          => $product->ptr_per_dozen ?? null,
                    ]
                );

                /**
                 * 🔹 UPDATE STOCK
                 */
                $stock = DistributorStock::firstOrCreate(
                    [
                        'distributor_id'         => $order->distributor_id,
                        'distributor_product_id' => $distProduct->id,
                    ],
                    ['available_qty' => 0]
                );

                $stock->increment('available_qty', $item->quantity);

                /**
                 * 🔹 INVENTORY LEDGER (IN)
                 */
                DistributorInventoryTransaction::create([
                    'distributor_id'         => $order->distributor_id,
                    'distributor_product_id' => $distProduct->id,
                    'type'                   => 'in',
                    'quantity'               => $item->quantity,
                    'source_type'            => Order::class,
                    'source_id'              => $order->id,
                    'remarks'                => 'Order delivered',
                ]);
            }
        });
    }
}
