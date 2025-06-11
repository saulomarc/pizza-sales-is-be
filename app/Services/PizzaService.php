<?php

namespace App\Services;

use App\Models\Pizza;

class PizzaService implements GenericService
{
    function fetchData($request) {
        $pizzas = Pizza::filter($request)->withPizzaType();

        if($request->has('items')) {
            $pizzas = $pizzas->paginate($request->items);
        } else {
            $pizzas = $pizzas->get();
        }
        
        return response()->json(
            [
             'pizzas' => $pizzas,
            ], 200
        );
    }
}
