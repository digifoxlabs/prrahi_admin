<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderActivity;

class OrderActivityLogger
{
    public static function log(
        Order $order,
        string $event,
        ?string $remarks = null
    ): void {
        OrderActivity::create([
            'order_id'          => $order->id,
            'event'             => $event,
            'remarks'           => $remarks,
            'performed_by_id'   => auth()->id(),
            'performed_by_type' => auth()->check()
                ? get_class(auth()->user())
                : null,
        ]);
    }
}
