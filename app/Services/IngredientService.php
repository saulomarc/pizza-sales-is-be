<?php

namespace App\Services;

use App\Models\Ingredient;

class IngredientService implements GenericService
{
    function fetchData($request) {
        $ingredients = Ingredient::filter($request);

        if($request->has('items')) {
            $ingredients = $ingredients->paginate($request->items);
        } else {
            $ingredients = $ingredients->get();
        }
        
        return response()->json(
            [
             'ingredients' => $ingredients,
            ], 200
        );
    }
}
