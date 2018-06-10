<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsToMany('App\Models\Company', 'company_products', 'company_id', 'product_id');
    }
}
