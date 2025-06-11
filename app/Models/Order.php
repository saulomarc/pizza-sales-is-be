<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'id',
        'created_at'
    ];

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
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
    }

    public function scopeWithOrderDetails($query)
    {
        $query->with(['order_details' => function ($query) {
            $query->select('order_id', 'pizza_id', 'quantity');
        }, 'order_details.pizza' => function ($query) {
            $query->select('id', 'pizza_type_id');
        }, 'order_details.pizza.pizza_type' => function ($query) {
            $query->select('id', 'name');
        }]);
    }
}
