<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Classes\_User;
use App\Classes\Traits\General;

class PostLoginController extends Controller
{
    use General;

	public function postLogin(Request $request)
	{

		$email = $request->email;
		$password = $request->password;
		$token = $request->token;
        $action = $request->action;

        //$this->writeToLog($email);
        //$this->writeToLog($password);
        //$this->writeToLog($token);
        //$this->writeToLog($action);

		$user = new _User();
        $result = $user->login($email, $password, $token, $action);

        if($result->status == 'success') {

            return response()->json($result, 200);

        }
        else {

            return response()->json($result, 400);

        }
	}
}
