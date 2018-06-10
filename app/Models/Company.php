<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\CompanyCategory');
    }

    public function locations()
    {
        return $this->hasMany('App\Models\CompanyLocation');
    }

    public function certifications()
    {
        return $this->belongsToMany('App\Models\Certification', 'company_certifications', 'company_id', 'certification_id')->withPivot('type');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'company_products', 'company_id', 'product_id');
    }

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'company_services', 'company_id', 'service_id');
    }
}
