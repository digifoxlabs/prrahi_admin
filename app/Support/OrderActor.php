<?php

namespace App\Support;

use App\Models\User;
use App\Models\SalesPerson;
use App\Models\Distributor;

class OrderActor
{
    public static function resolve(): array
    {
        if (auth('admin')->check()) {
            return [
                'type' => User::class,
                'id'   => auth('admin')->id(),
                'role' => 'admin',
            ];
        }

        if (auth('sales')->check()) {
            return [
                'type' => SalesPerson::class,
                'id'   => auth('sales')->id(),
                'role' => 'sales',
            ];
        }

        if (auth('distributor')->check()) {
            return [
                'type' => Distributor::class,
                'id'   => auth('distributor')->id(),
                'role' => 'distributor',
            ];
        }

        abort(403);
    }
}
