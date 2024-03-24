<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\medication;
use App\Models\Dosages;
use App\Http\Requests\MedicationRequest;
use App\Http\Requests\DosageRequest;
use App\Models\Time;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;



class MedicinesController extends Controller
{

    protected $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'POST'
    ];

    public function index()
    {

        $allMedications = dosages::join('medications', 'medications.id', '=', 'dosages.medication_id')
            ->select('medications.*', 'dosages.AgeFrom', 'dosages.AgeTo', 'dosages.dosageInstruction')->get();
        /**
         *
         */

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

        $today = date('Y:m:d');

        $monthlyExpiryAnalytics = DB::table('medications')->selectRaw('MONTH(expiryDate) as month , count(*) as total_medicines ')
            ->groupBy(DB::raw('MONTH(expiryDate)'))->where('expiryDate', '<', $today)->orderBy(DB::raw('Month(expiryDate)'))->limit(4)->get();

        $outOfStockMedicine = medication::where('totalQuantity', '<', 1)->get();
        $medGettingOutOfStock = medication::where('totalQuantity', '<=', 5)->where('totalQuantity', '>', 1)->get();




        /*
        extracts the month using month number
        **/


        foreach ($monthlyExpiryAnalytics as $expiryAnalytics) {
            $month = $expiryAnalytics->month;
            $expiryAnalytics->month = $monthsNames["$month"];
        }







        $today = date('Y-m-d');
        $expiredMedicines = medication::whereDate('expiryDate', '<', $today)->get();
        $medicineData = [
            'allMedicines' => $allMedications,
            'expiredMedicine' => $expiredMedicines,
            'monthlyExpiryAnalytics' => $monthlyExpiryAnalytics,
            'outOfStockMedicines' => $outOfStockMedicine,
            'gettingOutOfStockMedicines' => $medGettingOutOfStock
        ];

        return response()->json($medicineData, 200, $this->headers);

    }
    public function create(MedicationRequest $request)
    {
        $data = $request->validated();
        $medicineData = [
            'totalQuantity' => $data["totalQuantity"],
            'description' => $data["description"],
            'medicineName' => $data["medicineName"],
            'medicinePrice' => $data["medicinePrice"],
            'expiryDate' => $data["expiryDate"],
            'notificationCount' => '0',

        ];

        //return  $data;

        //  return $data;
        $medicine = medication::create($medicineData);
        if ($medicine) {
            $dosages = $data["dosages"];
            $medicineId = $medicine->id;
            $a = $this->createDosage($dosages, $medicineId);

            return response()->json("medicine created successfully", 200);

        }




    }

    private function createDosage($dosages, $medicationId)
    {
        // return count($dosage);

        // return $a["dosageInstruction"];


        for ($i = 0; $i < count($dosages); $i++) {
            $dosageInfo = $dosages[$i];

            $dosageResult = Dosages::create([
                'medication_id' => $medicationId,
                'AgeFrom' => $dosageInfo["AgeFrom"],
                'AgeTo' => $dosageInfo["AgeTo"],
                //  'ageCategory' => $dosageInfo["AgeCategory"],
                //'dosageStrength' => $dosageInfo["dosageStrength"],
                'dosageInstruction' => $dosageInfo["dosageInstruction"]
            ]);
            $dosage_id = $dosageResult->id;
            $timeData = $dosageInfo["times"];
            $this->createTimes($timeData, $dosage_id);
        }

    }

    private function createTimes($timeData, $dosage_id)
    {
        for ($i = 0; $i < count($timeData); $i++) {
            Time::create([
                'time' => $timeData[$i],
                'dosageId' => $dosage_id
            ]);
        }



    }



    public function updateMedicine(Request $request, $medicineId)
    {
        // $medication =  medication::find($medicationId);


    }



    public function deleteMedicine($medicationId)
    {
        $medication = medication::find($medicationId);



        $medicineDosages = Dosages::where('medication_id', $medicationId)->get();

        $this->setInvoiceMedicineIdNull($medicationId);
        for ($i = 0; $i < count($medicineDosages); $i++) {
            $dosageId = $medicineDosages[$i]->id;
            $dosage = Dosages::find($dosageId);



            $dosageTimes = Time::where('dosageId', $dosageId)->get();
            $this->deleteTimes($dosageTimes);
            $dosage->delete();


        }

        $medication->delete();

        return response()->json("medicine deleted successfully", 200);
    }


    private function deleteTimes($dosageTime)
    {
        for ($i = 0; $i < count($dosageTime); $i++) {
            $timeId = $dosageTime[$i]->id;
            $time = Time::find($timeId);
            $time->delete();
        }

    }



    private function setInvoiceMedicineIdNull($medicineId)
    {
        $invoices = Invoice::where('medication_id', $medicineId)->get();
        //$invoiceCount = Invoice::where('medication_id',$medicineId)->count();

        for ($i = 0; $i < count($invoices); $i++) {
            $invoiceId = $invoices[$i]->id;
            $invoice = Invoice::find($invoiceId);
            $invoice->medication_id = null;
            $invoice->save();
        }

    }



    public function getMedicine($medicineId)
    {
        $medicine = medication::find($medicineId);
        return response()->json($medicine, 200);
    }



/**
*
*totalQuantity' => ['required'],
'
*  medicine[
* description:"sm",
*   medicineName:paracetamol,
*  medicineQuantity:3,
* medicinePrice:1,
* expiryDate,
* totalQuantity:34,
*
* dosages [
*  {
*       ageFrom:1
*       ageTo: 10
*         dosageInstruction: to be taken 3 times daily, after eating,
*       AgeCategory:
*       dosageStrength
*       times: [
*             2:00
*             5:00
*             3:00
*           ]
*}
{
*       ageFrom:12
*       ageTo: 25
*         dosageInstruction: to be taken 3 times daily, after eating,
*       AgeCategory:
*       dosageStrength
*       times: [
*             2:00
*             5:00
*             3:00
*           ]
*}
*
* ]
*
*
*
*/













}
