<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pizza;
use Illuminate\Support\Facades\DB;

class StatService
{
    function getDashboardCardStats($request) {
        //count the number orders
        $total_orders = Order::count();

        //count the total number of pizzas ordered
        $total_pizzas_ordered = OrderDetail::get()->sum('quantity');
        
        //get the top 5 pizza with the most number of orders
        $most_ordered = DB::table('pizzas')
        ->selectRaw('pizzas.id, pt.name, od.pizza_id, SUM(od.quantity) as total_orders')
        ->join('order_details as od', 'pizzas.id', '=', 'od.pizza_id')
        ->join('pizza_types as pt', 'pizzas.pizza_type_id', '=', 'pt.id')
        ->groupBy('od.pizza_id')
        ->orderBy('total_orders', 'DESC')
        ->take(5)
        ->get();

        return response()->json(
            [
                'top_five_most_ordered' => $most_ordered,
                'most_ordered' => $most_ordered[0],
                'total_orders' => $total_orders,
                'total_pizzas_ordered' => $total_pizzas_ordered
            ],
            200
        ); 
    }
}
