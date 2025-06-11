<?php

namespace App\Services;

use App\Models\Order;

class OrderService implements GenericService
{
    function fetchData($request) {
        $orders = Order::filter($request)->withOrderDetails();

        if($request->has('items')) {
            $orders = $orders->paginate($request->items);
        } else {
            $orders = $orders->get();
        }
        
        return response()->json(
            [
             'orders' => $orders,
            ], 200
        );
    }
}
