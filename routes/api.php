<?php
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Middleware\DataAccessControl;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\MedicinesController;
use App\Http\Middleware\AdminOnlyMiddleware;
use App\Http\Controllers\PharmacistController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\AllUsersMiddleware;
//use App\Http\Middleware\AdminOnlyMiddleware;
use App\Http\ControllersChatController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**  Authentication and authorization */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/sendcode', [AuthController::class, 'resetPassword']);
Route::post('/verifycode', [AuthController::class, 'validateCode']);
Route::post('/resetpassword/{userId}', [AuthController::class, 'changePassword']);
Route::post('/updatepassword/{userId}', [AuthController::class, 'updatePassword']);






/**    end points for custbashomers */
Route::get('/customers', [CustomerController::class, 'index']); //->middleware(DataAccessControl::class);
Route::post('/registerCustomer', [CustomerController::class, 'registerCustomer']);
Route::post('/updatecustomer/{customer_id}', [CustomerController::class, 'updateCustomerProfile']);
Route::get('/customer/{customerId}', [CustomerController::class, 'getCustomer']);
Route::delete('/deletecustomer/{customer_id}', [CustomerController::class, 'deleteCustomer']); //->middleware(DataAccessControl::class);
Route::get('/customer/{customerId}/sale', [CustomerController::class, 'customerSales']);

//Route::get('/sales',[SalesController::class,'sales'])->middleware(SalesMiddleware::class);


/**medicines middleware */
Route::post('/addMedicine', [MedicinesController::class, 'create']);
Route::get('/medicines', [MedicinesController::class, 'index']);
Route::delete('/deletemedicine/{medicationId}', [MedicinesController::class, 'deleteMedicine']);
Route::put('/updatemedicine/{medicationId}', [MedicinesController::class, 'updateMedicine']);
Route::get('/medicine/{medicineId}', [MedicinesController::class, 'getMedicine']);


/**end points for pharmacists */
Route::post('/addPharmacist', [PharmacistController::class, 'registerPharmacist']); //->middleware(AdminOnlyMiddleware::class);
Route::get('/pharmacists', [PharmacistController::class, 'index']);
Route::post('/updatepharmacist/{pharmacist_id}', [PharmacistController::class, 'updatePharmacist']);
Route::delete('/deletepharmacist/{pharmacist_id}', [PharmacistController::class, 'deletePharmacist']);
Route::get('/pharmacist/{pharmacistId}', [PharmacistController::class, 'getPharmacist']);
Route::get('/pharmacist/{pharmacist_id}/sales', [PharmacistController::class, 'getPharmacistSales']);

//Route::post('/makeSales',[MedicinesController::class])

//Sales Route/

Route::post('/makesales', [SalesController::class, 'makeSales']); //->middleware(DataAccessControl::class);
Route::get('/sales', [SalesController::class, 'index']);
Route::get('/invoice/{sales_id}', [SalesController::class, 'getCustomerInvoice']);
Route::get('/invoices', [SalesController::class, 'Invoices']);
Route::post('/editsale/{id}', [SalesController::class, 'editSale']);


Route::get('/notifications', [NotificationsController::class, 'index']);
Route::delete('/deletenotification/{id}', [NotificationsController::class, 'deleteNotification']);
Route::post('/updateuser', [AuthController::class, 'updateUser']);

Route::post('/sendmessage', [ChatController::class, 'sendMessage']);
Route::get('/getmessage/{senderId}/{receiverId}', [ChatController::class, 'getMessage']);


/**
 * users
 *
 */


Route::get('/users', [AuthController::class, 'getUsers']);
