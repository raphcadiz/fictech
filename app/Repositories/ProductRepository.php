<?php

namespace App\Repositories;


use App\Models\Product;

class ProductRepository extends AbstractRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new Product();
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

        $product = $this->model->create($data);

        return $product;
    }

    public function updateFill($product, $request)
    {
        $data = [
            'name' => $request['name']
        ];

        $product = $product->fill($data)->save();

        return $product;
    }
}