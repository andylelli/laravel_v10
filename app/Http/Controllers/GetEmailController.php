<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Email;

class GetEmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $userEmail = $request->input('email');

        Mail::to($userEmail)->send(new Email());

        return response()->json(['message' => 'Welcome email sent!']);
    }
}
