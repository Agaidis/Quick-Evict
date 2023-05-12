<?php

namespace App\Console\Command;

use Illuminate\Console\Command;
use App\ErrorLog;

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
            $errorMsg = new ErrorLog();
            $errorMsg->payload = 'testing from the planet Court Notifications!';
            $errorMsg->save();

        } catch( \Exception $e) {
            mail('andrew.gaidis@gmail.com', 'Court Notifications Error', $e->getMessage() . ' Code: ' . $e->getCode() . ' File Line: ' . $e->getLine());

            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' Line #: ' . $e->getLine();
            $errorMsg->save();
        }



    }
}
