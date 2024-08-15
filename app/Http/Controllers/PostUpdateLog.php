<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Classes\Log;
use App\Classes\Traits\General;

class PostInsertController extends Controller
{
	use General;

	public function postInsert(Request $request)
	{
		$name = $request->name;
		$eventid = $request->eventid;

		$log = new Log();
		$result = $log->update_log($name, $eventid);

		if($result['status'] == 'success') {
			return response()->json($response, 201);
		}
		else {
			return response()->json($response, 400);
		}

	}
}
