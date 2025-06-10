<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pizza;
use App\Models\PizzaIngredient;
use App\Models\PizzaType;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class DataUploadService
{
    function readUploadFiles() {
        try {
            DB::beginTransaction();

            $this->uploadPizzaType();
            $this->uploadPizzaData();
            $this->uploadOrdersData();
            $this->uploadOrderDetailsData();

            DB::commit();

            return response()->json([
                'message' => 'Successfully Uploaded Pizza Data',
                'status' => 'Ok',
            ], 200);

        } catch (Throwable $ex) {
            Log::info($ex);

            DB::rollBack();
        }
    }

    function uploadPizzaType() {
        $filename = storage_path('/app/private/pizza_types.csv');
        $file = fopen($filename, "r");
        $line_count = 0;

        $pizza_types = [];
        $pizza_ingredients = [];
        $all_ingredients = [];

        while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
            if ($line_count < 1) {
                $line_count++;
                continue;
            }

            //get all data from pizza types and store in an array for easy upsert
            array_push($pizza_types, [
                'code' => $data[0],
                'name' => $data[1],
                'category' => $data[2]
            ]);

            //parsed ingredients
            $ingredient = explode(',', $data[3]);
            
            //get the ingredients and corresponding code
            $pizza_ingredients[$data[0]] = $ingredient;

            //unique ingredients
            $all_ingredients = array_unique(array_merge($all_ingredients, $ingredient));

            $line_count++;
        }

        $all_ingredients_clean = [];
        foreach($all_ingredients as $ingredient) {
            array_push($all_ingredients_clean, [
                'name' => trim($ingredient),
            ]);
        }

        //Upsert Pizza Types
        if (!empty($pizza_types)) {
            PizzaType::upsert($pizza_types, ['code'], ['code']);
        }

        //Upsert Ingredients
        if (!empty($all_ingredients_clean)) {
            Ingredient::upsert($all_ingredients_clean, ['name'], ['name']);
        }
    
        $this->processPizzaIngredients($pizza_ingredients);
    }

    function processPizzaIngredients($pizza_ingredients) {
        $pizza_types = PizzaType::get();
        $ingredients = Ingredient::get();

        $toBeUpserted = [];
        foreach ($pizza_ingredients as $key => $pi) {
            $pizza_type = $pizza_types->where('code', $key)->first();
            foreach ($pi as $pizza_ingredient) {
                $ingredient = $ingredients->where('name', trim($pizza_ingredient))->first();

                array_push($toBeUpserted, [
                    'pizza_type_id' => $pizza_type->id,
                    'ingredient_id' => $ingredient->id,
                ]);
            }
        }

        PizzaIngredient::upsert($toBeUpserted, ['pizza_type_id', 'ingredient_id'], ['updated_at']);
    }

    function uploadPizzaData()
    {
        $filename = storage_path('/app/private/pizzas.csv');
        $file = fopen($filename, "r");
        $line_count = 0;

        $pizzas = [];
        $pizza_types = PizzaType::get();

        while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
            if ($line_count < 1) {
                $line_count++;
                continue;
            }

            $pizza_type = $pizza_types->where('code', $data[1])->first();

            array_push($pizzas, [
                'name' => $data[0],
                'pizza_type_id' => $pizza_type->id,
                'size' => $data[2],
                'price' => $data[3]
            ]);
        }

        //Upsert Pizza Types
        if (!empty($pizzas)) {
            Pizza::upsert($pizzas, ['name'], ['name']);
        }
    }

    function uploadOrdersData()
    {
        $filename = storage_path('/app/private/orders.csv');
        $file = fopen($filename, "r");
        $line_count = 0;

        $orders = [];

        while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
            if ($line_count < 1) {
                $line_count++;
                continue;
            }

            array_push($orders, [
                'id' => $data[0],
                'created_at' => $data[1] . ' ' . $data[2],
            ]);
        }

        //Upsert Pizza Types
        if (!empty($orders)) {
            Order::upsert($orders, ['id']);
        }
    }

    function uploadOrderDetailsData()
    {
        $filename = storage_path('/app/private/order_details.csv');
        $file = fopen($filename, "r");
        $line_count = 0;

        $order_details = [];
        $pizzas = Pizza::get();

        while (($data = fgetcsv($file, 200, ",")) !== FALSE) {
            if ($line_count < 1) {
                $line_count++;
                continue;
            }

            $pizza = $pizzas->where('name', $data[2])->first();

            array_push($order_details, [
                'id' => $data[0],
                'order_id' => $data[1],
                'pizza_id' => $pizza->id,
                'quantity' => $data[3],
            ]);
        }

        //Upsert Pizza Types
        if (!empty($order_details)) {
            collect($order_details)
                ->map(function (array $row) {
                    return Arr::only($row, ['id', 'order_id', 'pizza_id', 'quantity']);
                })
                ->chunk(10000)
                ->each(function (Collection $chunk) {
                    Log::info($chunk);
                    OrderDetail::upsert($chunk->all(), 'id');
                });
        }
    }
}
