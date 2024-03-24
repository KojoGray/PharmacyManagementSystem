<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Dosages;
use App\Models\customer;
use App\Models\pharmacist;
use App\Models\reminder;
use App\Models\medication;
use App\Models\User;
use App\Models\Invoice;
use App\Http\Requests\SalesRequest;
use Symfony\Component\HttpFoundation\Response;

class SalesController extends Controller
{

    protected $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => '*'
    ];

    public function makeSales(SalesRequest $request)
    {

        /**
         * {
         *     total_quantity,
         *     total_price,
         *
         *     medicines:
         *      [
         *       {
         *     medicineName,
         *    totalQuantity,
         *
         * }
         * ]
         *
         *
         *
         * }
         *
         *
         *
         */
        $data = $request->validated();


        $totalQuantity = $data["total_quantity"];
        $totalPrice = $data["total_price"];
        $medicinesPurchased = $data['medications'];

        $customer_email = $data["customer_email"];
        $pharmacist_email = $data["pharmacist_email"];

        $customerDetails = customer::where('customerEmail', $customer_email)->first();
        $customerId = $customerDetails->id;

        $errorExist = false;



        $errors = array();
        for ($i = 0; $i < count($medicinesPurchased); $i++) {

            $medicine_id = $medicinesPurchased[$i]["medicine_id"];
            $medicinePrice = $medicinesPurchased[$i]["medicine_unitPrice"];

            $medicineTotalQuantity = $medicinesPurchased[$i]["medicine_totalQuantity"];
            $medicineName = $medicinesPurchased[$i]["medicine_name"];


            if (!$this->thereIsSufficientMedicine($medicine_id, $medicineTotalQuantity)) {

                array_push($errors, ["error" => "please the quantity of $medicineName is insufficient"]);
                $errorExist = true;

            } else {
                $errorExist = false;
            }


        }


        if ($errorExist === true) {
            return response()->json($errors, 201);
        } else {


            $pharmacist_id = '';
            $pharmacist = pharmacist::where('pharmEmail', $pharmacist_email)->first();

            if ($pharmacist === null) {
                return response()->json("pharmacist doesn't exit ", 409);
            } else {

                $pharmacist_id = $pharmacist->id;


            }



            $sale = Sales::create([
                'pharmacist_id' => $pharmacist_id,
                'customer_id' => $customerId,
                'total_quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'status' => $data['status'],
                'amount_paid' => $data['amount_paid']

            ]);
            $sales_id = $sale->id;

            $Invoices = array();


            for ($i = 0; $i < count($medicinesPurchased); $i++) {

                $medicine_id = $medicinesPurchased[$i]["medicine_id"];
                $medicinePrice = $medicinesPurchased[$i]["medicine_unitPrice"];
                $medicineTotalQuantity = $medicinesPurchased[$i]["medicine_totalQuantity"];
                $medicineName = $medicinesPurchased[$i]["medicine_name"];



                $this->reduceMedicineQuantity($medicine_id, $medicineTotalQuantity);
                $this->createReminders($customerId, $medicine_id);
                $invoice = $this->createInvoice($customerId, $medicine_id, $sales_id, $medicinePrice, $medicineTotalQuantity);

                array_push($Invoices, $invoice);

            }

        }


        $currentSalesDetails = [
            'invoices' => $Invoices,
            'customer' => $customerDetails,
            'saleStatus' => $sale->status
        ];

        return response()->json($currentSalesDetails, 200);



    }






    private function reduceMedicineQuantity($medicine_id, $totalQuantity)
    {

        $medicineBeingPurchased = medication::find($medicine_id);
        $medicineBeingPurchased->totalQuantity = $medicineBeingPurchased->totalQuantity - $totalQuantity;
        $medicineBeingPurchased->save();
    }

    private function thereIsSufficientMedicine($medicine_id, $totalQuantity)
    {

        $medicineBeingPurchased = medication::find($medicine_id);
        $medicineBeingPurchasedQuantity = $medicineBeingPurchased->totalQuantity;

        $sufficient = $medicineBeingPurchasedQuantity - $totalQuantity >= 0;
        $medicineBeingPurchasedQuantity = $medicineBeingPurchasedQuantity - $totalQuantity;
        return $sufficient;

    }





    public function createReminders($customerId, $medicine_id)
    {
        $customer = customer::find($customerId);


        $customerBirthDate = Carbon::parse($customer->customerBirthDate);


        $customerBirthYear = $customerBirthDate->year;

        $currentYear = date('Y');

        $customerAge = $currentYear - $customerBirthYear;


        $dosage = Dosages::where('medication_id', $medicine_id)->where('AgeFrom', '<=', $customerAge)->where('AgeTo', '>=', $customerAge)->first();
        if ($dosage === null) {
            return response()->json("please this medicine doesn't have a dosage for the age");
        }
        $dosageId = $dosage->id;





        $reminder = reminder::create([
            'reminderMessage' => 'Hi',
            'dosageId' => $dosageId,
            'customerId' => $customerId
        ]);

        return $dosage;


    }



    private function createInvoice($customer_id, $medication_id, $sales_id, $unit_price, $unit_quantity)
    {
        $invoice = Invoice::create([
            'customer_id' => $customer_id,
            'medication_id' => $medication_id,
            'sales_id' => $sales_id,
            'unit_price' => $unit_price,
            'unit_quantity' => $unit_quantity
        ]);


        return $invoice;
    }





    public function index()
    {


        $allSales = Sales::join('customers', 'sales.customer_id', '=', 'customers.id')
            ->join('pharmacists', 'sales.pharmacist_id', '=', 'pharmacists.id')
            ->select('sales.id', 'sales.amount_paid', 'sales.created_at', 'sales.total_quantity', 'sales.total_price', 'customers.customerFirstName', 'sales.status', 'pharmacists.pharmFirstName as  pharmacistName')
            ->get();


        $totalSales = Sales::all()->count();

        //  $yearlySales =   Sales::sum('total_price')->get();
        $yearlySales = DB::table('sales')
            ->selectRaw('YEAR(created_at) as year, SUM(total_price) as total')
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->get();

        $monthlySales = DB::table('sales')->selectRaw('SUM(total_price) as total_price , MONTH(created_at) as month ')->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))->limit(4)->get();
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

        $months = [

        ];

        $monthlyValues = [

        ];



        $colors = [
            'Monday' => '#000000',
            'Tuesday' => 'yellow',
            'Wednesday' => 'brown',
            'Thursday' => '#ff6384',
            'Friday' => '#4b60c0',
            'Saturday' => '#ffce56',
            'Sunday' => '#36a2eb'
        ];


        $dailySales = DB::table('sales')

            ->selectRaw('DATE_FORMAT(created_at, "%W") as day_name, SUM(total_price) as total_price')
            ->where('created_at', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 4 DAY)'))
            ->groupBy('day_name')
            ->get();


        foreach ($dailySales as $daily_sale) {
            $dayName = $daily_sale->day_name;
            $daily_sale->color = $colors["$dayName"];
        }



        $weeklySales = DB::table('sales')
            ->selectRaw('YEAR(created_at) as year, WEEK(created_at) as week, SUM(total_price) as total_price')
            ->where('created_at', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL 5 WEEK)'))
            ->groupBy('year', 'week')
            ->get();







        foreach ($monthlySales as $sales) {
            $month = $sales->month;
            $price = $sales->total_price;
            array_push($months, $monthsNames["$month"]);
            array_push($monthlyValues, $price);
        }

        return response()->json([
            'allSales' => $allSales,
            'yearly_sales' => $yearlySales,
            'monthly_sales' => [
                'months' => $months,
                'totalPrice' => $monthlyValues,
                'total_sales' => $totalSales

            ],
            'dailySales' => $dailySales,
            'weeklySales' => $weeklySales
        ], 200, $this->headers);
    }





    public function getCustomerInvoice($sales_id)
    {


        $customerInvoice = Invoice::join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('medications', 'medications.id', '=', 'invoices.medication_id')
            ->select(
                'medications.medicineName',
                'medications.medicinePrice',
                'medications.expiryDate',
                'invoices.id',
                'invoices.created_at',
                'invoices.unit_quantity',
                'customers.customerFirstName',
                'customers.customerLastName'
            )
            ->where('invoices.sales_id', '=', $sales_id)->get();
        $sale = Sales::find($sales_id);
        $salesStatus = $sale->status;
        $total_price = $sale->total_price;
        $total_quantity = $sale->total_quantity;

        return response()->json([
            'invoice' => $customerInvoice,
            'status' => $salesStatus,
            'totalprice' => $total_price,
            'totalQuantity' => $total_quantity
        ], 200);
    }


    public function Invoices()
    {

        $invoices = Invoice::join('customers', 'invoices.customer_id', '=', 'customers.id')
            ->join('medications', 'medications.id', '=', 'invoices.medication_id')
            ->select('medications.medicineName', 'medications.medicinePrice', 'medications.expiryDate', 'invoices.id', 'invoices.unit_quantity', 'customers.*')
            ->get();

        return response()->json($invoices, 200, $this->headers);

    }






    public function editSale(Request $request, $sales_id)
    {
        $sale = Sales::find($sales_id);

        $currentStatus = $request->status;
        $sale->status = $currentStatus;


        $sale->save();

        return response()->json("success", 200);
    }
}
