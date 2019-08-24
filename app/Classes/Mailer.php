<?php


namespace App\Classes;
use Mailgun\Mailgun;


class Mailer
{

    public function sendMail($mailTo, $subject, $message) {

        // First, instantiate the SDK with your API credentials
        $mg = Mailgun::create('1f6b8c342bbe30ae8a7e9afbeebe0be0-3939b93a-12b78a07'); // For US servers

        $mg->messages()->send('mg.courtzip.com', [
            'from'    => 'Court Zip Administration <mailgun@mg.courtzip.com>',
            'to'      => $mailTo,
            'subject' => $subject,
            'text'    => $message
        ]);
    }
}