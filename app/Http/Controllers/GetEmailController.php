<?php

namespace App\Http\Controllers;
namespace App\Mail;

use DB;
use Illuminate\Http\Request;
use App\Mail\Email;

class GetEmailController extends Controller
{

    public function getEmail()
    {
        Mail::to('andylelli@yahoo.com')->send(new Mail([
            'name' => 'Demo',
       ]));
    }
}

