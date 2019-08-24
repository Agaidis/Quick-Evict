<?php


namespace App\Classes;


class Mailer
{

    public function sendMail($mailTo, $subject, $message) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
        $headers .= "From: CourtZip\r\n";

        mail($mailTo, $subject, $message, $headers);
    }
}