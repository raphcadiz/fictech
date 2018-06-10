<?php

namespace App\Repositories;


use App\Models\CompanyCategory;

class CompanyCategoryRepository extends AbstractRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new CompanyCategory();
    }

    public function getAll($request)
    {
        $query = $this->model->select('*');

        if (isset($request['unfiltered']) && $request['unfiltered'] == 1) {
            $query->with('companies', 'companies.services', 'companies.products', 'companies.certifications');
        }

        $categories = $query->get();

        return $categories;
    }
}