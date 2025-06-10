<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pizza extends Model
{
    protected $fillable = [
        'name',
        'pizza_type_id',
        'size',
        'price'
    ];

    public function pizza_type(): BelongsTo
    {
        return $this->belongsTo(PizzaType::class, 'pizza_type_id', 'id');
    }
}
