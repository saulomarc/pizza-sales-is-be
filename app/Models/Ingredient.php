<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ingredient extends Model
{
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at',
    ];

    public function pizza_types(): HasManyThrough
    {
        return $this->hasManyThrough(PizzaType::class, PizzaIngredient::class, 'pizza_type_id', 'id', 'id', 'ingredient_id');
    }
}
