<?php
namespace App\Console;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Notifications\MedicineReminderNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\reminder;
use App\Models\Time;


//use Illuminate\Console\Scheduling\Schedule;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

//    $reminders =  reminder::join('dosages','reminder.dosageId','=','dosages.id')
//    ->join('customer','reminder.customer_id','customer.id')
//    ->select('customer.customerEmail','customer.customerFirstName')->get();

//    $times = Time::all();
// //either send reminder everyday at a particular time 
// //or if the time matches the particular time , send notification

//  foreach ($reminders as $reminder) {
//            for($i = 0; $i < count($times);$i++){
//                     $time = $times[$i];
//                     if($time->dosageId === $reminder->dosageId){
//                          $currentTime = date('H:i');
//                          //  if($time->time == $currentTime){
//                            //   Mail::to($reminder->customerEmail)->send(new ReminderMail($reminder));  
//                        //  }
                         
//                     } 
//            }
 //}


//      })->everyMinute()->name("Medicine Reminder for Dosage: {$time->dosageId}");
//  }
         $schedule->command('reminder:message')->everyMinute()->withoutOverlapping()
         ->appendOutputTo('scheduler.log');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
