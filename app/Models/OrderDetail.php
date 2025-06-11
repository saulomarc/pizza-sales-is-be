<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderDetail extends Model
{
    protected $appends = ['total_price_per_pizza'];

    protected $fillable = [
        'id',
        'order_id',
        'pizza_id',
        'quantity'
    ];

    public function pizza(): BelongsTo
    {
        return $this->belongsTo(Pizza::class, 'pizza_id', 'id');
    }

    protected function totalPricePerPizza(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                //get attributes
                return Pizza::where('id', $attributes['pizza_id'])->pluck('price')->first() * $attributes['quantity'];
            } 
        );
    }
}
