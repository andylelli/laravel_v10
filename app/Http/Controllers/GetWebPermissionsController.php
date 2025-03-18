<?php

namespace App\Http\Controllers;
use DB;
use Exception;
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
            ->get();

			$formattedEventName = str_replace(' ', '-', strtolower($event[0]->event_name));

            if ($formattedEventName != $eventname) {
                $error = "Event name does not match the event ID";
                $this->writeToLog($error);
                
                throw new Exception($error); // This triggers the catch block
            }


			$url ="https://www.evaria.io/user/index.html?name=" . $eventname . "&id=" . $eventid . "&bg=" . $bgcolor;

			//$this->writeToLog(print_r($event, true));
        	return view('permissions', ['url' => $url, 'eventid' => $event[0]->event_id, 'eventname' => $event[0]->event_nam, 'eventimage' => $event[0]->event_image, 'bgcolor' => $bgcolor]);
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
