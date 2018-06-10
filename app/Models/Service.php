<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsToMany('App\Models\Company', 'company_services', 'certification_id', 'company_id');
    }
}
