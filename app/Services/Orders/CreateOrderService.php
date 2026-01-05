<?php

namespace App\Services\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CreateOrderService
{
    /**
     * Create base order (without items)
     */
    public static function create(array $data): Order
    {

        return DB::transaction(function () use ($data) {

            return Order::create([
                'order_number'     => $data['order_number'],
                'order_date'     => $data['order_date'],
                'distributor_id'   => $data['distributor_id'],
                'billing_address'   => $data['billing_address'],

                'subtotal'         => $data['subtotal'] ?? 0,
                'discount'         => $data['discount'] ?? 0,
                'sgst'             => $data['sgst'] ?? 0,
                'cgst'             => $data['cgst'] ?? 0,
                'igst'             => $data['igst'] ?? 0,
                'round_off'        => $data['round_off'] ?? 0,
                'total_amount'     => $data['total_amount'] ?? 0,

                'status'           => 'pending',
                'dispatch_status'  => 'pending',

                // polymorphic creator
                'created_by_type'  => $data['created_by_type'],
                'created_by_id'    => $data['created_by_id'],
            ]);
        });
    }
}
