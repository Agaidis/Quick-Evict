<?php

namespace App\Http\Controllers;

use App\Classes\Mailer;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\User;

class NotificationController extends Controller
{
    private $courtId;
    private $userEmail;
    private $mailer;

    public function __construct($courtId, $userEmail)
    {
        $this->courtId = $courtId;
        $this->userEmail = $userEmail;
        $this->mailer = new Mailer();
    }

    public function getAdminEmails() {
        $admins = User::where('role', 'Administrator')->get();
        $adminEmails = '';

        foreach ($admins as $admin) {
            $adminEmails .= $admin->email . ', ';
        }

        return rtrim($adminEmails, ', ');
    }

    public function getJudgeEmail() {
        $judgeEmail = User::where('role', 'Court')->where('court_id', $this->courtId)->value('email');

        return $judgeEmail;
    }

    public function notifyMaker() {
        try {
            $subject = 'CourtZip File Creation';
            $message = 'Your File has successfully been submitted. You can view this at the dashboard.';

            $this->mailer->sendMail($this->userEmail,  $subject, $message);

        } catch ( Exception $e) {
            $this->mailer->sendMail('andrew.gaidis@gmail.com', 'Notification Maker Error' . Auth::user()->id, $e->getMessage() );
        }
    }

    public function notifyJudge() {
        try {
            $judgeEmail = $this->getJudgeEmail();
            $subject = 'CourtZip File Creation';
            $message = 'Hello,<br>
This email is just a notice that a filing was just submitted via CourtZip for your court.<br>
Please log-in to CourtZip.com, click Dashboard, and you\'ll see the latest filing at the top of the screen.<br>
To print it out, click the blue download button, which will download the filing on your computer.  Then you can print it out and complete the filing using the CourtZip card.<br>  
If you forgot your password, please click "Forgot your password"<br>
If you have any questions or issues, please call CourtZip customer service at 717-413-6976.<br><br>
Thank you,<br>
CourtZip Customer Service Team';

            $this->mailer->sendMail($judgeEmail,  $subject, $message);
        } catch ( Exception $e) {
            $this->mailer->sendMail('andrew.gaidis@gmail.com', 'Notification Judge Error' . Auth::user()->id, $e->getMessage() );
        }
    }

    public function notifyAdmin() {
        try {
          //  $adminEmails = ['andrew.gaidis@gmail.com', 'chad@slatehousegroup.com', 'racheller@slatehousegroup.com', 'nate@slatehousegroup.com', 'amandaa@slatehousegroup.com'];

            $subject = 'CourtZip File Creation';
            $message = 'A CourtZip filing has been made by ' . $this->userEmail . ' under court id ' . $this->courtId;

            $this->mailer->sendMail('andrew.gaidis@gmail.com',  $subject, $message);

        } catch ( Exception $e) {
            $this->mailer->sendMail('andrew.gaidis@gmail.com', 'Notification Admin Error' . Auth::user()->id, $e->getMessage() );
        }
    }
}
