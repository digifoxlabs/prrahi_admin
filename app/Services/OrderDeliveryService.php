<?php 
namespace App\Services;

use App\Models\Order;
use App\Models\Distributor;
use App\Models\User;
use App\Models\SalesPerson;
use Illuminate\Auth\Access\AuthorizationException;
use App\Models\DistributorProduct;
use App\Models\DistributorStock;
use App\Models\DistributorInventoryTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderDeliveryService
{
    /**
     * Credit distributor inventory when order is delivered
     */
    public static function handle(Order $order): void
    {
        // ✅ Safety: already synced
        if ($order->inventory_synced_at) {
            throw new Exception('Inventory already credited for this order.');
        }

        // ✅ Safety: must have distributor
        if (!$order->distributor_id) {
            throw new Exception('Order has no distributor assigned.');
        }

        DB::transaction(function () use ($order) {


             // Ensure items have product loaded
            $order->loadMissing('items.product.parent');

            foreach ($order->items as $item) {



                $product = $item->product;

                if (!$product) {
                    throw new Exception('Order item product not found.');
                }

                /**
                 * 1️⃣ Derive product name (SAME LOGIC AS BLADE)
                 */
                $baseName = $product->type === 'variant'
                    ? optional($product->parent)->name
                    : $product->name;

                $attributeText = '';

                if (is_array($product->attributes)) {
                    $fragrance = $product->attributes['fragrance'] ?? null;
                    $size      = $product->attributes['size'] ?? null;

                    if ($fragrance || $size) {
                        $attributeText .= ' — ' . trim(
                            ($fragrance ?? '') .
                            ($size ? " ({$size})" : '')
                        );
                    }
                }

                $finalProductName = trim($baseName . $attributeText);



                /**
                 * 1️⃣ Distributor Product Snapshot
                 */
                $distributorProduct = DistributorProduct::firstOrCreate(
                    [
                        'distributor_id' => $order->distributor_id,
                        'product_id'     => $item->product_id,
                    ],
                    [
                        'product_name' => $finalProductName,
                        'sku'          => $product->code ?? null,
                        'variant'      => $product->type === 'variant'
                            ? json_encode($product->attributes)
                            : null,
                        'mrp'          => $item->product->mrp_per_unit ?? null,
                        'ptr'          => $item->product->ptr_per_dozen ?? null,
                    ]
                );

                /**
                 * 2️⃣ Distributor Stock (current balance)
                 */
                $stock = DistributorStock::firstOrCreate(
                    [
                        'distributor_id' => $order->distributor_id,
                        'distributor_product_id' => $distributorProduct->id,
                    ],
                    [
                        'available_qty' => 0,
                    ]
                );

                $stock->increment('available_qty', $item->quantity);

                /**
                 * 3️⃣ Inventory Ledger Entry
                 */
                DistributorInventoryTransaction::create([
                    'distributor_id'         => $order->distributor_id,
                    'distributor_product_id'=> $distributorProduct->id,
                    'type'                   => 'in',
                    'quantity'               => $item->quantity,
                    'source_type'            => Order::class,
                    'source_id'              => $order->id,
                    'remarks'                => 'Stock received from Order #' . $order->order_number,
                ]);
            }

            /**
             * 4️⃣ Mark order inventory synced
             */
            $order->update([
                'inventory_synced_at' => now(),
            ]);
        });
    }
}