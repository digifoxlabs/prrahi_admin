<?php

namespace App\Services;

use App\Models\RetailOrder;
use App\Models\RetailOrderActivity;

class RetailOrderActivityLogger
{
    public static function log(
        RetailOrder $order,
        string $event,
        ?string $remarks = null
    ): void {
        RetailOrderActivity::create([
            'retail_order_id'          => $order->id,
            'event'             => $event,
            'remarks'           => $remarks,
            'performed_by_id'   => auth()->id(),
            'performed_by_type' => auth()->check()
                ? get_class(auth()->user())
                : null,
        ]);
    }
}
