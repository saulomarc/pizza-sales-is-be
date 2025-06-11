<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pizza;
use Illuminate\Support\Facades\DB;

class StatService
{
    function getDashboardCardStats($request) {
        $total_orders = Order::count();
        $total_pizzas_orderd = OrderDetail::get()->sum('quantity');
        
        $most_ordered = DB::table('pizzas')
        ->selectRaw('pizzas.id, pt.name, od.pizza_id, SUM(od.quantity) as total_orders')
        ->join('order_details as od', 'pizzas.id', '=', 'od.pizza_id')
        ->join('pizza_types as pt', 'pizzas.pizza_type_id', '=', 'pt.id')
        ->groupBy('od.pizza_id')
        ->orderBy('total_orders', 'DESC')
        ->first();

        return response()->json(
            [
                'most_ordered' => $most_ordered,
                'total_orders' => $total_orders,
                'total_pizzas_ordered' => $total_pizzas_orderd
            ],
            200
        ); 
    }
}
