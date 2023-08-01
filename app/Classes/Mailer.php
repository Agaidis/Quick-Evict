<?php


namespace App\Classes;
use App\ErrorLog;
use Mailgun\Mailgun;


class Mailer
{

    public function sendMail($mailTo, $subject, $message, $html)
    {

        try {
            // First, instantiate the SDK with your API credentials
            $mg = Mailgun::create(env('MAIL_API_KEY')); // For US servers

            $mg->messages()->send('mg.courtzip.com', [
                'from' => 'Court Zip Administration <service@courtzip.com>',
                'to' => $mailTo,
                'subject' => $subject,
                'text' => $message,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            $errorMsg = new ErrorLog();
            $errorMsg->payload = $e->getMessage() . ' ' . $e->getLine();
            $errorMsg->save();
        }
    }
}