<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Classes\Log;
use App\Classes\Traits\General;

class GetLogController extends Controller
{
    use General;

	public function getLog($eventid)
	{
        //Set variables
		$name = "ptr";

		$response = $log->new_log_insert($name, $eventid);

        return response()->json($response, 201);
	}
}