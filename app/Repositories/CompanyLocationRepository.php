<?php

namespace App\Repositories;


use App\Models\CompanyLocation;

class CompanyLocationRepository extends AbstractRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new CompanyLocation();
    }

    public function getAll()
    {
        return $this->model->select('*')
            ->get();
    }

    public function store($company, $request)
    {
        $data = [
            'company_id' => $company->id,
            'name' => $request['name'],
            'address' => $request['address'],
            'lat' => $request['lat'],
            'lng' => $request['lng']
        ];

        $location = $this->model->create($data);

        return $location;
    }

    public function updateFill($location, $company, $request)
    {
        $data = [
            'company_id' => $company->id,
            'name' => $request['name'],
            'address' => $request['address'],
            'lat' => $request['lat'],
            'lng' => $request['lng']
        ];

        $location = $location->fill($data)->save();

        return $location;
    }
}