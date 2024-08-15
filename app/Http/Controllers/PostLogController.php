<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Classes\Log;
use App\Classes\Traits\General;

class PostLogController extends Controller
{
	use General;

	public function postLog(Request $request)
	{
        //Set variables
		$name = $request->name;
		$eventid = $request->eventid;
        $uxtime = $this->unixTime();

		$response = $log->new_log_insert($name, $eventid, $uxtime);

        return response()->json($response, 201);
	}
}
