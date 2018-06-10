<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company');
    }
}
