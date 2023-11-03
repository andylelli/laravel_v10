<?php

namespace App\Classes;
use App\Classes\Traits\General;
use DB;

class Event{

	use General;

    /**
    * New Event                     	*
    * @param string $userid User ID 	*
    * @return $eventid                  *
    **/
    public function new_event_insert($params){

		$name = $params[0];
		$userid = $params[1];

		//Set other variables
		$uxtime = $this->unixTime();
        $table= "event";

        //Get JSON parameters
        $property = "dbInsertInitial";
        $dbInsertInitial = $this->getJSONParams($table, $property);

        //Convert from Object to Array
        $dbInsertArray = json_decode(json_encode($dbInsertInitial), true);

        //Add mandatory values
        $dbInsertArray[$table . '_name'] = $name;
        $dbInsertArray[$table . '_userid'] = $userid;
        $dbInsertArray[$table . '_uxtime'] = $uxtime;

		//Insert new poll and return intert id
        try {
    		$eventid = DB::table($table)->insertGetId($dbInsertArray);

            $hyphen_name = str_replace(" ", "-", strtolower($name));

			$email = $hyphen_name . '@evaria.io';
			$first = $name;
			$last = 'Evaria';
			$role = 2;

			$params = array(
				$first,
				$last,
				$email,
                $hyphen_name,
				$eventid,
				$role
			);

			$user = new _User();
			$new_guest_insert = $user->new_guest_insert($params);
			extract($new_guest_insert);


            //Prepare API response to client
            $response = $dbInsertArray;

            //Add response values
            $response['status'] = 'success';
            $response['event_id'] = $eventid;
            $response['qrcode_id'] = $qrcode_id;
            $response['qrcode_value'] = $qrcode_value;
            $response['qrcode_image'] = $qrcode_image;
            $response['message'] = 'New event added';

			return $response;
        }
        //Return error to the client
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
