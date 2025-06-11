<?php

namespace App\Services;

use App\Models\PizzaType;

class PizzaTypeService implements GenericService
{
    function fetchData($request) {
        $pizza_types = PizzaType::filter($request);

        if($request->has('items')) {
            $pizza_types = $pizza_types->paginate($request->items);
        } else {
            $pizza_types = $pizza_types->get();
        }
        
        return response()->json(
            [
             'pizza_types' => $pizza_types,
            ], 200
        );
    }
}
