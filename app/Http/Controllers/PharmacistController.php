<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\pharmacistRegistrationRequest;
use App\Models\pharmacist;
use App\Models\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationEmail;
use App\Mail\ReminderMail;
use App\Models\reminder;
use App\Models\Sales;


class PharmacistController extends Controller
{

    private $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET'
    ];



    public function index()
    {

        $pharmacists = pharmacist::all();
        $pharmacistsCount = pharmacist::count();
        $data = [
            'pharmacists' => $pharmacists,
            'pharmacistsCount' => $pharmacistsCount
        ];




        return (response()->json($data, 200, $this->headers));


    }


    public function getPharmacist($pharmacistId)
    {
        $pharmacist = pharmacist::find($pharmacistId);
        return response()->json($pharmacist);


    }



    public function getPharmacistSales($pharmacistId)
    {

        // $tupleTodelete = customer::find($customer_id);
        //$customerEmail = $tupleTodelete->customerEmail;
        $pharmacistAsUser = User::find($pharmacistId);
        $pharmacistEmail = $pharmacistAsUser->email;

        $pharmacistAsPharmacist = pharmacist::where('pharmEmail', $pharmacistEmail)->first();
        $pharmacist_id = $pharmacistAsPharmacist->id;

        $pharmacistSales = Sales::join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('sales.id', 'sales.created_at', 'sales.amount_paid', 'sales.total_quantity', 'sales.total_price', 'customers.customerFirstName', 'sales.status')
            ->where('pharmacist_id', $pharmacist_id)
            ->get();

        $today = date('Y:m:d');

        $pharmTotalDailySale = Sales::where('pharmacist_id', $pharmacist_id)
            ->whereRaw('date(created_at)', $today);




        //  $pharSales = [
//    'allSales'=>  $pharmacistSales,
//    'todaySales'=> $
//  ]


        return response()->json($pharmacistSales, 200);

    }



    public function registerPharmacist(pharmacistRegistrationRequest $request)
    {
        $data = $request->validated();

        //==========creating customer as a user===========/
        $existingPharmacist = pharmacist::where('pharmEmail', $data["pharmEmail"])->count();
        $existingUser = User::where('email', $data["pharmEmail"])->count();

        if ($existingPharmacist >= 1 || $existingUser >= 1) {
            return response()->json("please this pharmacist already exist ", 409);
        } else {
            $userData = [

            ];


            if (pharmacist::create($data)) {
                $phar = User::create([
                    'username' => $data['pharmFirstName'],
                    'email' => $data['pharmEmail'],
                    'role' => 'pharmacist',
                    'password' => bcrypt('Password@123')

                ]);
                $Message = [
                    'userEmail' => $data['pharmEmail'],

                    'userName' => $data['pharmFirstName'],
                    'password' => "Password@123",
                ];
                $pharmEmail = $data["pharmEmail"];

                Mail::to($pharmEmail)->send(new RegistrationEmail($Message));
                return response()->json($phar, 200);
            } else {
                return response()->json("error adding new pharmacist", 401);
            }
        }

    }

    public function updatePharmacist(pharmacistRegistrationRequest $request, $pharmacist_id)
    {

        $data = $request->validated();
        $pharmacists = pharmacist::find($pharmacist_id);
        $pharmacists->pharmFirstName = $data["pharmFirstName"];
        $pharmacists->pharmEmail = $data["pharmEmail"];
        $pharmacists->pharmPhoneNumber = $data["pharmPhoneNumber"];



        $user = User::where('email', $data["pharmEmail"])->first();
        $user->username = $data["pharmFirstName"];
        $user->email = $data["pharmEmail"];


        if ($pharmacists->save() && $user->save()) {
            return response()->json("saved successfully", 200);
        } else {

            return response()->json("error updating pharmacist", 500);
        }

    }


    public function deletePharmacist($pharmacistId)
    {
        $pharmacist = pharmacist::find($pharmacistId);




        Sales::where('pharmacist_id', $pharmacistId)->delete();

        $user = User::where('email', $pharmacist->pharmEmail);
        if ($pharmacist->delete() && $user->delete()) {
            return response()->json("deleted", 200);
        } else {
            return response()->json("error", 500);
        }
    }




}
