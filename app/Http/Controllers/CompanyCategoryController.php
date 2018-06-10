<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyCategoryRepository;
use Illuminate\Http\Request;

class CompanyCategoryController extends Controller
{
    public $companyCategoryRepository;

    public function __construct()
    {
        $this->companyCategoryRepository = new CompanyCategoryRepository();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $company_categories = $this->companyCategoryRepository->getAll($request->input());
 
            return response()->json($company_categories)->setStatusCode(200); 
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
            'name' => 'required'
        ]);

        try {
            
            $category = $this->companyCategoryRepository->create($request->input());
 
            return response()->json($category)->setStatusCode(200); 
        } catch (\Exception $e) {
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
           $query = $this->companyCategoryRepository->findBy(['id' => $id]);

           if (!$query) {
               return response()->json('Category not found!')->setStatusCode(404);
           }

           $query->with('companies', 'companies.services', 'companies.products', 'companies.locations', 'companies.certifications');
           $category = $query->first();

           return response()->json($category)->setStatusCode(200);
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
            'name' => 'required'
        ]);

        try {
            $category = $this->companyCategoryRepository->find($id);

            if ( $category ) {
                $category = $category->fill($request->input())->save();
 
                return response()->json($category)->setStatusCode(200); 
            }

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
        //
    }
}
