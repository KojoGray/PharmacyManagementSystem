<?php

namespace App\Http\Controllers;

use App\Models\customer;
use App\Models\User;
use App\Models\Sales;
use Illuminate\Http\Request;
use App\Http\Requests\customerRegistrationRequest;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;
use Twilio\Rest\Client;
use App\Models\reminder;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationEmail;
use App\Mail\ReminderMail;
use Illuminate\Support\Facades\DB;
use App\Models\Notifications;
use \App\Events\Customers;
use App\Models\Invoice;




use App\Models\Dosages;


class CustomerController extends Controller
{
    private $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET'
    ];

    protected function getMessage($email, $defaultPassword)
    {

        // return $message;

    }




    /*
    |--------------------------------------------------------------------------
    |                     index
    |--------------------------------------------------------------------------
    |
    |this method fetches all customers details from the database
    |
    |
    |
    */

    public function index()
    {

        $allCustomers = customer::all();
        $customersCount = customer::count();


        $customersOnMonthlyBasis = DB::table('customers')->selectRaw('count(*) as numberOfCustomers , MONTH(created_at) as month')->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('Month(created_at) '))->limit(5)->get();

        $monthsNames = array(
            "1" => "January",
            "2" => "February",
            "3" => "March",
            "4" => "April",
            "5" => "May",
            "6" => "June",
            "7" => "July",
            "8" => "August",
            "9" => "September",
            "10" => "October",
            "11" => "November",
            "12" => "December",
        );



        foreach ($customersOnMonthlyBasis as $monthlyCustomers) {
            $month = $monthlyCustomers->month;
            $monthlyCustomers->month = $monthsNames["$month"];
        }

        $data = [
            'customers' => $allCustomers,
            'totalCustomers' => $customersCount,
            'monthlyStatistics' => $customersOnMonthlyBasis

        ];



        return (response()->json($data, 200, $this->headers));

    }





    /*
    |--------------------------------------------------------------------------
    | getCustomer
    |--------------------------------------------------------------------------
    |
    | this function fetches one particular customer based on the custome's id
    |
    |
    |
    */



    public function getCustomer($customerId)
    {
        $customer = customer::find($customerId);

        return response()->json($customer, 200);
    }






    /*
    |--------------------------------------------------------------------------
    | sendSms2 and sendSms methods
    |--------------------------------------------------------------------------
    |   the sendSms2 and the sendSms methods are
    implementation of sms alert
    by usint two different sms apis
    |
    |
    |
    |
    */

    private function sendSms2($customer_contact, $customer_email, $message, $defaultPassword)
    {

        try {
            $account_sid = env('TWILIO_SID');
            $auth_token = env('TWILIO_TOKEN');
            $sender_contact = env('TWILIO_FROM');
            $defaultPassword = "Password@123";

            //$message =


            $client = new Client($account_sid, $auth_token);
            $client->messages->create(
                '+233' . $customer_contact,
                [
                    'from' => $sender_contact,
                    'body' => $message

                ]
            );
        } catch (Exception $e) {
            return response()->json("success", 200);
        }

    }




    private function sendSms($customer_contact, $customer_name, $customer_email)
    {

        $basic = new Basic(env('VONAGE_KEY'), env('VONAGE_SECRET'));
        $client = new Client($basic);
        $message = "Dear $customer_name ";

        $customer_contact = $customer_contact;
        $defaultPassword = 'Password@123';
        $senderContact = '+233544780024';

        $message =



            $sms = new SMS($customer_contact, $senderContact, $message);

        $response = $client->sms()->send($sms);
    }





    /*
    |--------------------------------------------------------------------------
    | registerCustomer method
    |--------------------------------------------------------------------------
    |
    | the registerCustomer   method
    |  adds customes to the database and
    | sends login credentials through email
    |
    */




    public function registerCustomer(customerRegistrationRequest $request)
    {

        $password = "Password@123";

        $data = $request->validated();

        $existingCustomer = customer::where('customerEmail', $data["customerEmail"])->count();
        if ($existingCustomer > 0) {
            return response()->json("customer already exist", 409);

        } else {
            $userData = [
                'username' => $data['customerFirstName'],
                'email' => $data['customerEmail'],
                'role' => 'customer',
                'password' => bcrypt($password)
            ];


            if (customer::create($data)) {
                User::create($userData);
                $contact = $data["customerContact"];
                $customerName = $data["customerFirstName"];
                $customerEmail = $data["customerEmail"];

                $Message = [
                    'userEmail' => $customerEmail,
                    'userName' => $customerName,
                    'password' => "Password@123",
                ];



                Mail::to($customerEmail)->send(new RegistrationEmail($Message));
                Notifications::create([
                    'message' => "new customer added"
                ]);

                $notifications = Notifications::all();

                event(new Customers($notifications));

                // $this->sendSms2($contact, $customerEmail);
                // $this->sendSms($contact,$customerName, $customerEmail);


                return response()->json("customer created successfully");
            } else {
                return response()->json("error creating new customer");
            }
        }
    }



    /*
    |--------------------------------------------------------------------------
    | updateCustomerProfile
    |--------------------------------------------------------------------------
    |
    |this method update the customer informations
    | in the database  on a PUT request
    |
    */


    public function updateCustomerProfile(customerRegistrationRequest $request, $customer_id)
    {
        $customerData = customer::find($customer_id);


        if ($request->hasFile('avatar')) {
            $image = $request->file('avatar');
            $this->uploadImage($image);
            $customerData->avatar = $image;
            $customerData->save();
        }
        $data = $request->validated();

        $customerData->customerFirstName = $data["customerFirstName"];
        $customerData->customerLastName = $data["customerLastName"];
        $customerData->customerEmail = $data["customerEmail"];
        $customerData->customerGender = $data["customerGender"];
        $customerData->customerBirthDate = $data["customerBirthDate"];


        if ($customerData->save()) {
            return response()->json("data saved successfully", 200);
        } else {
            return response()->json("there was a problem saving the data", 500);
        }
    }


    private function uploadImage($image)
    {
        $path = $image->store('/App/images');

    }


    /*
    |--------------------------------------------------------------------------
    | deleteCustomer
    |--------------------------------------------------------------------------
    |
    | this method deletes a customer
    | from  the database
    |
    |
    */

    public function deleteCustomer($customer_id)
    {

        $tupleTodelete = customer::find($customer_id);
        $customerEmail = $tupleTodelete->customerEmail;

        Invoice::where('customer_id', $customer_id)->delete();
        Sales::where('customer_id', $customer_id)->delete();






        $user = User::where('email', $customerEmail);
        if ($tupleTodelete) {
            $tupleTodelete->delete();
            $user->delete();




            return response()->json("deleted successfully");
        } else {
            abort(404, "item not found");
        }
    }


    /*
    |--------------------------------------------------------------------------
    |                    customerSales
    |--------------------------------------------------------------------------
    |
    |  this method fetches the sales belonging to a
    |  particular customer
    |
    */


    public function customerSales($customerId)
    {
        $customer = User::find($customerId);
        $customerEmail = $customer->email;
        $customerAsCustomer = customer::where('customerEmail', $customerEmail)->first();

        $customer_id = $customerAsCustomer->id;



        $allSales = Sales::join('customers', 'sales.customer_id', '=', 'customers.id')
            ->join('pharmacists', 'sales.pharmacist_id', '=', 'pharmacists.id')
            ->select('sales.id', 'sales.created_at', 'sales.total_quantity', 'sales.total_price', 'sales.status', 'sales.amount_paid', 'pharmacists.pharmFirstName as pharmacistName', )
            ->where('sales.customer_id', '=', $customer_id)
            ->get();



        return response()->json([
            'customerSales' => $allSales
        ], 200);

    }





    /*****===========alert customer to take medicine ============  */

    public function sendReminderAlert()
    {


        $reminders = reminder::join('dosages', 'reminder.dosageId', '=', 'dosages.id')
            ->join('customer', 'reminder.customer_id', 'customer.id')
            ->select('customer.customerEmail', 'customer.customerFirstName')->get();

        $times = Time::all();
        //either send reminder everyday at a particular time
//or if the time matches the particular time , send notification

        foreach ($reminders as $reminder) {
            for ($i = 0; $i < count($times); $i++) {
                $time = $times[$i];
                if ($time->dosageId === $reminder->dosageId) {
                    $currentTime = date('H:i');
                    if ($time->time == $currentTime) {
                        Mail::to($reminder->customerEmail)->send(new ReminderMail($reminder));
                    }

                }
            }
        }

        /// reminder::join('')
    }






}
