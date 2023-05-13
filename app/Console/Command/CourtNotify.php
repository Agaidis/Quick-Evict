<?php

namespace App\Console\Command;

use App\CourtDetails;
use App\CourtNotification;
use App\Evictions;
use Illuminate\Console\Command;
use App\ErrorLog;
use App\User;

class CourtNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:notifyCourts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify a Court that the date has been set';

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

        try {
            $courtDetails = CourtNotification::whereDate('court_date', '<=', now()->subDays(10)->setTime(0, 0, 0)->toDateTimeString())->get();

            $errorMsg = new ErrorLog();
            $errorMsg->payload = 'test: ' . serialize($courtDetails);
            $errorMsg->save();

            foreach ($courtDetails as $courtDetail) {
                $eviction = Evictions::find($courtDetail->eviction_id);
                $userData = User::find($eviction->user_id);
                $userEmail = $userData->email;

                if ($eviction->status == 'Judgement Issued in Favor of Owner') {

                    $errorMsg = new ErrorLog();
                    $errorMsg->payload = 'made it in here!';
                    $errorMsg->save();

                    mail('andrew.gaidis@gmail.com', 'CourtZip 10 day Order ', 'Hello,

It has been 10 days since your Landlord-Tenant Complaint Hearing for property address _________________ and tenant(s) ___________________ . You are now eligible to file an Order for Possession via CourtZip.  Alternatively, If the tenant has satisfied the judgement, vacated the property or filed an appeal,you can change the status in the CourtZip Dashboard to "Paid Judgement".

You can file the Order for Possession directly at www.CourtZip.com.

A few common reasons an Order for Possession should not or can not be filed:

-Tenant(s) have vacated the property.

-Tenant(s) have paid the judgement in full.

-Tenant(s) have applied for assistance which states Order for Possession can not be filed.

-Tenant has filed an appeal.

If you have any questions, please let us know - we hope you find this alert helpful!

Sincerely,

CourtZip');
                }

            }

        } catch( \Exception $e) {
            mail('andrew.gaidis@gmail.com', 'Court Notifications Error', $e->getMessage() . ' Code: ' . $e->getCode() . ' File Line: ' . $e->getLine());

            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }



    }
}
