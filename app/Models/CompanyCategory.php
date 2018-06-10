<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyCategory extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function companies()
    {
        return $this->hasMany('App\Models\Company', 'category_id');
    }
}
