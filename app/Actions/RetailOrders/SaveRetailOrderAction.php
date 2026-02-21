<?php

namespace App\Actions\RetailOrders;

use App\Models\RetailOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Support\OrderActor;
use App\Services\Orders\OrderCalculationService;
use App\Models\RetailOrderItem;
use App\Services\RetailOrderActivityLogger;
use App\Actions\Activity\LogActivityAction;

class SaveRetailOrderAction
{
    public static function create(array $data): RetailOrder
    {
        return DB::transaction(function () use ($data) {

            $actor = OrderActor::resolve();

            // 🔹 Calculate totals (RETAIL CONTEXT)
            $calculation = OrderCalculationService::calculateForRetailer(
                $data['items'],
                $data['retailer_id'],
                $data['discount'] ?? 0
            );

            // 🔹 Create Retail Order
            $order = RetailOrder::create([
                'order_number'    => $data['order_number']
                    ?? 'RORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
                'order_date'      => $data['order_date'],
                'retailer_id'     => $data['retailer_id'],
                'distributor_id'  => $data['distributor_id'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,

                'subtotal'        => $calculation['subtotal'],
                'discount'        => $data['discount'] ?? 0,
                'cgst'            => $calculation['cgst'],
                'sgst'            => $calculation['sgst'],
                'igst'            => $calculation['igst'],
                'round_off'       => $calculation['round_off'],
                'total_amount'    => $calculation['total_amount'],

                'status'          => 'pending',
                'created_by_type' => $actor['type'],
                'created_by_id'   => $actor['id'],
            ]);

            // 🔹 Save Items
            foreach ($calculation['items'] as $item) {
                RetailOrderItem::create([
                    'retail_order_id' => $order->id,
                    'product_id'      => $item['product_id'],
                    'rate'            => $item['price'],
                    'base_unit'       => $item['base_unit'],
                    'quantity'        => $item['quantity'],
                    'discount_percent'=> $item['discount_percent'],
                    'total'           => $item['total'],
                ]);
            }

            RetailOrderActivityLogger::log($order, 'created', 'Retail order created');

            // 🔹 Location + activity
            LogActivityAction::handle([
                'actor_type'   => $actor['type'],
                'actor_id'     => $actor['id'],
                'activity_type'=> 'retail_order_created',
                'subject_type' => RetailOrder::class,
                'subject_id'   => $order->id,
                'latitude'     => $data['latitude'] ?? null,
                'longitude'    => $data['longitude'] ?? null,
                'meta' => [
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                ],
            ]);

            return $order;
        });
    }

    public static function update(RetailOrder $order, array $data): RetailOrder
    {
        abort_if($order->status !== 'pending', 403);

        return DB::transaction(function () use ($order, $data) {

            $calculation = OrderCalculationService::calculateForRetailer(
                $data['items'],
                $data['retailer_id'],
                $data['discount'] ?? 0
            );

            $order->update([
                'order_number'    => $data['order_number'] ?? $order->order_number,
                'order_date'      => $data['order_date'],
                'retailer_id'     => $data['retailer_id'],
                'distributor_id'  => $data['distributor_id'] ?? $order->distributor_id,
                'billing_address' => $data['billing_address'] ?? null,

                'subtotal'        => $calculation['subtotal'],
                'discount'        => $data['discount'] ?? 0,
                'cgst'            => $calculation['cgst'],
                'sgst'            => $calculation['sgst'],
                'igst'            => $calculation['igst'],
                'round_off'       => $calculation['round_off'],
                'total_amount'    => $calculation['total_amount'],
            ]);

            // 🔹 Replace items
            $order->items()->delete();

            foreach ($calculation['items'] as $item) {
                RetailOrderItem::create([
                    'retail_order_id' => $order->id,
                    'product_id'      => $item['product_id'],
                    'rate'            => $item['price'],
                    'base_unit'       => $item['base_unit'],
                    'quantity'        => $item['quantity'],
                    'discount_percent'=> $item['discount_percent'],
                    'total'           => $item['total'],
                ]);
            }

           RetailOrderActivityLogger::log($order, 'updated', 'Retail order updated');

            return $order;
        });
    }
}
