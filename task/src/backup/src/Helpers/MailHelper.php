<?php


namespace Tritiyo\Task\Helpers;
use App\Mail\SendMail;
use Mail;

class MailHelper
{
    public static function send($data, $subject, $addrees){
        //  dd($data);
        Mail::to($addrees)->send(new SendMail($data, $subject));
    }

}
