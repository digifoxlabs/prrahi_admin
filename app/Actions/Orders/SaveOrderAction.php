<?php 

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Services\Orders\{
    OrderCalculationService,
    CreateOrderService,
    AddOrderItemsService
};
use App\Support\OrderActor;
use App\Services\OrderActivityLogger;
use App\Actions\Activity\LogActivityAction;

class SaveOrderAction
{
    public static function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $actor = OrderActor::resolve();


            //Get Calculations from OrderCalculationService
            $calculation = OrderCalculationService::calculateForDistributor(
                $data['items'],
                $data['distributor_id'],
                $data['discount'] ?? 0
            );



            $order = CreateOrderService::create([
                'order_number'     => $data['order_number'],
                'order_date'       => $data['order_date'],
                'distributor_id'   => $data['distributor_id'],
                'billing_address'  => $data['billing_address'] ?? null,

                'subtotal'         => $calculation['subtotal'],
                'discount'         => $data['discount'] ?? 0,
                'cgst'             => $calculation['cgst'] ?? 0,
                'sgst'             => $calculation['sgst'] ?? 0,
                'igst'             => $calculation['igst'] ?? 0,
                'round_off'        => $calculation['round_off'] ?? 0,
                'total_amount'     => $calculation['total_amount'],

                'status'           => 'pending',
                'created_by_type'  => $actor['type'],
                'created_by_id'    => $actor['id'],
            ]);

            AddOrderItemsService::handle($order, $calculation['items']);

            OrderActivityLogger::log($order, 'created', 'Order created');

            LogActivityAction::handle([
                'actor_type' => $actor['type'],
                'actor_id'   => $actor['id'],
                'activity_type' => 'order_created',
                'subject_type'  => \App\Models\Order::class,
                'subject_id'    => $order->id,
                'latitude'  => $data['latitude'],
                'longitude' => $data['longitude'],
                'meta' => [
                    'order_number' => $order->order_number,
                    'total_amount' => $order->total_amount,
                ],
            ]);

            return $order;
        });
    }

    public static function update(Order $order, array $data)
    {
        abort_if($order->status !== 'pending', 403);

        return DB::transaction(function () use ($order, $data) {

            //Get Calculations from OrderCalculationService
            $calculation = OrderCalculationService::calculateForDistributor(
                $data['items'],
                $data['distributor_id'],
                $data['discount'] ?? 0
            );

            $order->update([
                'distributor_id'  => $data['distributor_id'],
                'order_number'    => $data['order_number'],
                'order_date'      => $data['order_date'],
                'billing_address' => $data['billing_address'] ?? null,
                'subtotal'        => $calculation['subtotal'],
                'discount'        => $data['discount'] ?? 0,
                'cgst'            => $calculation['cgst'] ?? 0,
                'sgst'            => $calculation['sgst'] ?? 0,
                'igst'            => $calculation['igst'] ?? 0,
                'round_off'       => $calculation['round_off'] ?? 0,
                'total_amount'    => $calculation['total_amount'],
            ]);

            $order->items()->delete();

            AddOrderItemsService::handle($order, $calculation['items']);

            OrderActivityLogger::log($order, 'updated', 'Order updated');

            return $order;
        });
    }
}
