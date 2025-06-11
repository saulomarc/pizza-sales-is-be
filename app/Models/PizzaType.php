<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PizzaType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
    ];

    public function pizzas(): HasMany
    {
        return $this->hasMany(Pizza::class, 'pizza_type_id', 'id');
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
        if ($filters->has('code_like') && $filters->code_like != '' && $filters->code_like != '--') {
            $query->where('code', 'LIKE', '%' . $filters->code_like . '%');
        }

        if ($filters->has('name_like') && $filters->name_like != '' && $filters->name_like != '--') {
            $query->where('name', 'LIKE', '%' . $filters->name_like . '%');
        }

        if ($filters->has('category') && $filters->category != '' && $filters->category != '--') {
            $query->where('category', $filters->category);
        }

        return $query;
    }
}
