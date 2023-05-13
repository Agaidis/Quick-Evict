<?php

namespace App\Console\Command;

use App\Classes\Mailer;
use App\CourtDetails;
use App\CourtNotification;
use App\Evictions;
use Illuminate\Console\Command;
use App\ErrorLog;
use App\User;
use Illuminate\Support\Facades\Auth;

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
            $courtDetails = CourtNotification::whereDate('court_date', '<=', now()->subDays(11)->setTime(0, 0, 0)->toDateTimeString())->get();

            foreach ($courtDetails as $courtDetail) {
                $eviction = Evictions::find($courtDetail->eviction_id);
                $userData = User::find($eviction->user_id);
                $propertyAddress = str_replace('-1', ', ', $eviction->property_address );
                $tenantName = $eviction->tenant_name;
                $userEmail = $userData->email;

                if ($eviction->status == 'Judgement Issued in Favor of Owner') {

                    $mailer = new Mailer();
                    $mailer->sendMail('andrew.gaidis@gmail.com', 'CourtZip 10 day Order','Hello,

It has been 10 days since your Landlord-Tenant Complaint Hearing for property address ' . $propertyAddress . ' and tenant(s) ' . $tenantName . '. You are now eligible to file an Order for Possession via CourtZip.  Alternatively, If the tenant has satisfied the judgement, vacated the property or filed an appeal,you can change the status in the CourtZip Dashboard to "Paid Judgement".

You can file the Order for Possession directly at www.CourtZip.com.

A few common reasons an Order for Possession should not or can not be filed:

-Tenant(s) have vacated the property.

-Tenant(s) have paid the judgement in full.

-Tenant(s) have applied for assistance which states Order for Possession can not be filed.

-Tenant has filed an appeal.

If you have any questions, please let us know - we hope you find this alert helpful!

Sincerely,

CourtZip', '<p>Hello,</p>

<p>It has been 10 days since your Landlord-Tenant Complaint Hearing for property address  ' . $propertyAddress . '  and tenant(s)  ' . $tenantName . '  . You are now eligible to file an Order for Possession via CourtZip.  Alternatively, If the tenant has satisfied the judgement, vacated the property or filed an appeal,you can change the status in the CourtZip Dashboard to "Paid Judgement".</p>

<p>You can file the Order for Possession directly at www.CourtZip.com.</p>

<p>A few common reasons an Order for Possession should not or can not be filed:</p>

<p>-Tenant(s) have vacated the property.</p>

<p>-Tenant(s) have paid the judgement in full.</p>

<p>-Tenant(s) have applied for assistance which states Order for Possession can not be filed.</p>

<p>-Tenant has filed an appeal.</p>

<p>If you have any questions, please let us know - we hope you find this alert helpful!</p>

<p>Sincerely,</p>

<p>CourtZip</p>');


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
