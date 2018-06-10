<?php

namespace App\Repositories;


use App\Models\Certification;

class CertificationRepository extends AbstractRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Certification();
    }

    public function getAll()
    {
        return $this->model->select('*')
            ->get();
    }

    public function store($request)
    {
        $data = [
            'name' => $request['name']
        ];

        $certification = $this->model->create($data);

        return $certification;
    }

    public function updateFill($certification, $request)
    {
        $data = [
            'name' => $request['name']
        ];

        $certification = $certification->fill($data)->save();

        return $certification;
    }
}