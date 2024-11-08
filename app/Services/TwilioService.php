<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $twilio;
    protected $twilioClient;


    public function __construct()
    {   
        //--------------- V1
        // $sid = config('services.twilio.sid');
        // $token = config('services.twilio.token');
        // $this->twilioClient = new Client($sid, $token);

        //--------------- V2
        $this->twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));

    }

    public function sendSms(string $to, string $message)
    {  
        $this->twilio->messages->create($to, [
            'from' => config('services.twilio.from'),
            'body' => $message
        ]);
    }
}
