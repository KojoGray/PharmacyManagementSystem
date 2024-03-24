<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PharmacistController;
use \Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!

*/

// Route::post('/water', function (\Illuminate\Http\Request $request) {
//     event(new \App\Events\Customers("hi"));
//    // return null;
// });


Route::post('/re', function (Request  $request){
    event(new \App\Events\Customers("hi")) ;
});

Route::get('/ws',function(){
    return view('websockets');
});