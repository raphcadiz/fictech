<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{path}', function () {
    return view('index');
})->where('path', '(.*)');

// Route::get('test', function () {
//     $company = \App\Models\Company::find(1);
//     // dd($company->name);
//     $company->services()->sync([1,2,3]);
// });
