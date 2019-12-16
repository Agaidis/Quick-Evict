<?php


namespace App\Classes;
use Mailgun\Mailgun;


class Mailer
{

    public function sendMail($mailTo, $subject, $message, $html) {


        // First, instantiate the SDK with your API credentials
        $mg = Mailgun::create(env('MAIL_API_KEY')); // For US servers

        $mg->messages()->send('mg.courtzip.com', [
            'from'    => 'Court Zip Administration <service@courtzip.com>',
            'to'      => $mailTo,
            'subject' => $subject,
            'text'    => $message,
            'html'    => $html
        ]);
    }
}