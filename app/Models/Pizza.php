<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'pizza_id', 'id');
    }

    public function scopeFilter($query, $filters, $role = null)
    {
        //select fields
        if ($filters->has('fields')) {
            foreach ($filters->fields as $key => $value) {
                $newField = $value;

                if ($key === 0) {
                    $query = $query->select($newField);
                } else {
                    $query = $query->addSelect($newField);
                }
            }
        }

        //order
        if ($filters->has('order_type')) {
            $query->orderBy($filters->order_field, $filters->order_type);
        }

        //distinct
        if ($filters->has('distinct')) {
            $query->select($filters->column_name)->distinct();
        }

        $query = $this->filterData($query, $filters);
    }

    public function filterData($query, $filters)
    {
        if ($filters->has('pizza_name_like') && $filters->pizza_name_like != '' && $filters->pizza_name_like != '--') {
            $query->where('name', 'LIKE', '%' . $filters->pizza_name_like . '%');
        }

        if ($filters->has('pizza_type_name_like') && $filters->pizza_type_name_like != '' && $filters->pizza_type_name_like != '--') {
            $query->whereRelation('pizza_type', 'name', 'LIKE', '%' . $filters->pizza_type_name_like . '%');
        }

        if ($filters->has('size') && $filters->size != '' && $filters->size != '--') {
            $query->where('size', $filters->size);
        }

        return $query;
    }

    public function scopeWithPizzaType($query)
    {
        $query->with(['pizza_type' => function ($query) {
            $query->select('id', 'name');
        }]);
    }
}
