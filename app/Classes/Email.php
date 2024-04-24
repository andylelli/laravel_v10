<?php

use App\Mail\Email;
use Illuminate\Support\Facades\Mail;


class Email {}

    public function send_email()){

        Mail::to('andylelli@yahoo.com')->send(new Mail([
            'name' => 'Demo',
       ]));
    }
}
