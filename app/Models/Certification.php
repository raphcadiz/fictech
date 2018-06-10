<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsToMany('App\Models\Company', 'company_certifications', 'certification_id', 'company_id');
    }
}
