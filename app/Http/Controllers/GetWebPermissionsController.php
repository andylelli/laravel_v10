<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;
use App\Classes\Traits\General;

class GetWebPermissionsController extends Controller
{
    use General;

	public function getWebPermissionsPage($eventname, $eventid, $bgcolor)
    {

		try {
        	$event = DB::table('event')
            ->where('event_id', $eventid)
            ->where('event_name', $eventname)
            ->get();

			$this->writeToLog(print_r($event, true));
        	return response()->json($event, 200);
		}

		catch(Exception $ex) {
			$error = $ex->getMessage();
			$this->writeToLog($error);
	
			$response = array(
				'status' => 'fail',
				'message' => $error
			);
	
			return $response;
		}
    }
}
