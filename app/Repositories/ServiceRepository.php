<?php

namespace App\Repositories;


use App\Models\Service;

class ServiceRepository extends AbstractRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Service();
    }

    public function getAll()
    {
        return $this->model->select('*')
            ->get();
    }

    public function store($request)
    {
        $data = [
            'name' => $request['name'],
        ];

        $service = $this->model->create($data);

        return $service;
    }

    public function updateFill($service, $request)
    {
        $data = [
            'name' => $request['name']
        ];

        $service = $service->fill($data)->save();

        return $service;
    }
}