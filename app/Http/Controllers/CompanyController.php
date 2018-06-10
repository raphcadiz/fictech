<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyRepository;
use App\Repositories\CompanyLocationRepository;
use App\Repositories\CertificationRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\ProductRepository;

use App\Models\Service;
use App\Models\Certification;
use App\Models\Product; 
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public $companyRepository;
    public $companyLocationRepository;
    public $certificationRepository;
    public $serviceRepository;
    public $productRepository;

    public function __construct()
    {
        $this->companyRepository = new CompanyRepository();
        $this->companyLocationRepository = new CompanyLocationRepository();
        $this->certificationRepository = new CertificationRepository();
        $this->serviceRepository = new ServiceRepository();
        $this->productRepository = new ProductRepository();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if ($request->input('featured')) {
                $companies = $this->companyRepository->getFeaturedCompanies($request->input());
            } else {
                $companies = $this->companyRepository->getAll();
            }
 
            return response()->json($companies)->setStatusCode(200); 
        } catch (\Exception $e) {
            return response()->json($e->getMessage())->setStatusCode(400);
        } 
    }

    public function search(Request $request)
    {
        try {
            $inputs = $request->input();
            $companies = $this->companyRepository->getSearch($inputs);

            return response()->json($companies)->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage())->setStatusCode(400);
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'exists:company_categories,id',
            'description' => 'required|min:5|max:400',
            'website' => 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'
        ]);

        try {
            
            $company = $this->companyRepository->store($request);

            if ($company) {
                if ($request['locations']) {
                    foreach ($request['locations'] as $location) {
                        $location = $this->companyLocationRepository->store($company, $location);
                    }
                }

                if ($request['certifications']) {
                    foreach ($request['certifications'] as $certification) {
                        $certification_model = Certification::where('name', $certification)->first();
                        if (!$certification_model) {
                            $certification_model = $this->certificationRepository->store(['name' => $certification['name']]);
                        }

                        $company->certifications()->attach([
                            $certification_model->id => ['type' => $certification['type']] 
                        ]);
                    }

                }

                if ($request['services']) {
                    $services = array();
                    foreach ($request['services'] as $service) {
                        $service_model = Service::where('name', $service)->first();
                        if ($service_model) {
                            $services[] = $service_model->id;
                        } else {
                            $service_model = $this->serviceRepository->store(['name' => $service]);
                            $services[] = $service_model->id;
                        }
                    }

                    $company->services()->attach($services);
                }

                if ($request['products']) {
                    $products = array();
                    foreach ($request['products'] as $product) {
                        $product_model = Product::where('name', $product)->first();
                        if ($product_model) {
                            $products[] = $product_model->id;
                        } else {
                            $product_model = $this->productRepository->store(['name' => $product]);
                            $products[] = $product_model->id;
                        }
                    }

                    $company->products()->attach($products);
                }
            }
 
            return response()->json($company)->setStatusCode(200); 
        } catch (\Exception $e) {
            $company->delete();
            return response()->json($e->getMessage())->setStatusCode(400);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
           $company = $this->companyRepository->find($id);

           if (!$company) {
               return response()->json('Company not found!')->setStatusCode(404);
           }
           $company->category;
           $company->certifications;
           $company->locations;
           $company->products;
           $company->services;

           return response()->json($company)->setStatusCode(200);
       } catch(\Exception $e) {
           return response()->json($e->getMessage())->setStatusCode(400);
       }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'exists:company_categories,id',
            'description' => 'required|min:5|max:400',
            'website' => 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/'
        ]);

        try {
            
            $company = $this->companyRepository->find($id);

            if ($company) {
                $this->companyRepository->updateFill($company, $request);

                if ($request['locations']) {
                    $location_ids = array();
                    foreach ($request['locations'] as $location) {
                        if (isset($location['id']) && $location['id'] != null) {
                            $location_model = $this->companyLocationRepository->find($location['id']);
                            if ($location_model) {
                                $location = $this->companyLocationRepository->updateFill($location_model, $company, $location);
                                $location_ids[] = $location_model->id;
                            }
                        } else {
                            $location = $this->companyLocationRepository->store($company, $location);
                            $location_ids[] = $location->id;
                        }
                    }
                    $deleted_locations = $this->companyLocationRepository->all()->whereNotIn('id', $location_ids)->where('company_id', $company->id);
                    foreach ($deleted_locations as $deleted_location) {
                        $deleted_location->delete();
                    }
                }

                if ($request['certifications']) {
                    $company->certifications()->detach();
                    foreach ($request['certifications'] as $certification) {
                        $certification_model = Certification::where('name', $certification)->first();
                        if (!$certification_model) {
                            $certification_model = $this->certificationRepository->store(['name' => $certification['name']]);
                        }

                        $company->certifications()->attach([
                            $certification_model->id => ['type' => $certification['type']] 
                        ]);
                    }

                }

                if ($request['services']) {
                    $services = array();
                    foreach ($request['services'] as $service) {
                        $service_model = Service::where('name', $service)->first();
                        if ($service_model) {
                            $services[] = $service_model->id;
                        } else {
                            $service_model = $this->serviceRepository->store(['name' => $service]);
                            $services[] = $service_model->id;
                        }
                    }

                    $company->services()->sync($services);
                }

                if ($request['products']) {
                    $products = array();
                    foreach ($request['products'] as $product) {
                        $product_model = Product::where('name', $product)->first();
                        if ($product_model) {
                            $products[] = $product_model->id;
                        } else {
                            $product_model = $this->productRepository->store(['name' => $product]);
                            $products[] = $product_model->id;
                        }
                    }

                    $company->products()->sync($products);
                }
            }
 
            return response()->json($company)->setStatusCode(200); 
        } catch (\Exception $e) {
            return response()->json($e->getMessage())->setStatusCode(400);
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $company = $this->companyRepository->find($id);

            if (!$company) {
               return response()->json('Company not found!')->setStatusCode(404);
            }

            $company->delete();
            return  response()->json(['message' => 'Company successfully deleted!'])->setStatusCode(200);
       } catch(\Exception $e) {
           return response()->json($e->getMessage())->setStatusCode(400);
       }
    }
}
