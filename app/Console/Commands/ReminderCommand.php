<?php


namespace App\Console\Commands;

//$//GLOBALS["notificationSentCount"] = 0;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\MedicineReminderAlert;
use App\Models\reminder;
use Carbon\Carbon;
use App\Models\Time;
use App\Models\customer;
use App\Models\Dosages;
use App\Models\medication;
use \App\Events\Customers;
use App\Models\Notifications;

class ReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'customer reminder message';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $medication = medication::where('totalQuantity', '<', 1)->where('notificationCount', '<', 1)->get();




        $reminders = reminder::all();
        $times = Time::all();
        echo ("working somehow");

        /**  ======= */

        foreach ($reminders as $reminder) {
            for ($i = 0; $i < count($times); $i++) {
                $time = $times[$i];
                if ($time->dosageId === $reminder->dosageId) {

                    $today = Carbon::now();
                    $medicineDate = Carbon::createFromFormat('H:i:a', $time->time);

                    $currentTime = $today->format('H:i:a');
                    $medicineTime = $medicineDate->format('H:i:a');
                    echo ("when first do first");

                    // $currentTime = Carbon::now();
                    //  $medicineTime = Carbon::createFromFormat('H:i', $time->time);


                    echo $medicineTime === $currentTime;


                    if ($medicineTime === $currentTime) {
                        $customer = customer::find($reminder->customerId);
                        $data =
                            Dosages::join('medications', 'dosages.medication_id', 'medications.id')
                                ->select('dosages.dosageInstruction', 'medications.medicineName')
                                ->first();
                        $customerEmail = $customer->customerEmail;

                        Mail::to($customerEmail)->send(new MedicineReminderAlert($data));
                        echo ("notification sent");

                    }

                }
            }
        }
        // echo ("working");


        if (!$medication->isEmpty()) {
            for ($i = 0; $i < count($medication); $i++) {
                $notificationsCount = $medication[$i]->notificationCount;
                $medicineName = $medication[$i]->medicineName;
                Notifications::create([
                    "message" => "please $medicineName is out of stock"
                ]);


                $notifications = Notifications::all();

                event(new Customers($notifications));

                $medication[$i]->notificationCount = $medication[$i]->notificationCount + 1;
                $medication[$i]->save();

            }

        }

    }

}
