<?php

namespace App\Repositories;

use JWTAuth;
use App\Models\Company;
use DB;

class CompanyRepository extends AbstractRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Company();
    }

    public function getAll()
    {
        return $this->model->select('*')
            ->with('certifications', 'locations', 'products', 'services')
            ->get();
    }

    public function getSearch($request)
    {
        $keyword = $request['s'];

        $query = $this->model->select('companies.*');

        if (!empty($keyword)) {
            $query->leftJoin('company_services', 'company_services.company_id', '=', 'companies.id')
            ->leftJoin('services', 'services.id', '=', 'company_services.service_id')
            ->leftJoin('company_products', 'company_products.company_id', '=', 'companies.id')
            ->leftJoin('products', 'products.id', '=', 'company_products.product_id')
            ->whereRaw("(services.name LIKE '%{$keyword}%' OR products.name LIKE '%{$keyword}%')");
        }
            

        if ($request['cert_type'] && !empty($request['cert_type'])) {
            $cerfication_types = explode(',', $request['cert_type']);
            $query->join('company_certifications', 'company_certifications.company_id', '=', 'companies.id')
                ->whereIn('company_certifications.type', $cerfication_types);
        }


       
        $query->groupBy('companies.id');
        $companies = $query->get();

        foreach ($companies as $company) {
            $company->services;
            $company->locations;
        }

        return $companies;
    }

    public function getFeaturedCompanies($request)
    {
        $query = $this->model->select('companies.*')
            ->where('companies.featured', '=', '1');

        if (isset($request['cert_type']) && !empty($request['cert_type'])) {
            $cerfication_types = explode(',', $request['cert_type']);
            $query->join('company_certifications', 'company_certifications.company_id', '=', 'companies.id')
                ->whereIn('company_certifications.type', $cerfication_types);
        }

        $companies = $query->get();
        foreach ($companies as $company) {
            $company->services;
            $company->locations;
            $company->certifications;
        }

        return $companies;
    }

    public function store($request)
    {
        $data = [
            'user_id' => JWTAuth::parseToken()->authenticate()->id,
            'name' => $request['name'],
            'category_id' => $request['category_id'],
            'description' => $request['description'],
            'website' => $request['website'],
            'year_founded' => $request['year_founded'],
            'company_size' => $request['company_size'],
        ];

        $company = $this->model->create($data);

        return $company;
    }

    public function updateFill($company, $request)
    {
        $data = [
            'user_id' => JWTAuth::parseToken()->authenticate()->id,
            'name' => $request['name'],
            'category_id' => $request['category_id'],
            'description' => $request['description'],
            'website' => $request['website'],
            'year_founded' => $request['year_founded'],
            'company_size' => $request['company_size'],
        ];

        $company = $company->fill($data)->save();

        return $company;
    }
}