<?php

namespace App\Classes;
use App\Classes\Traits\General;
use DB;
use Illuminate\Http\Request;

class Log{

	use General;

    /**
    * New Event                     	*
    * @param string $userid User ID 	*
    * @return $eventid                  *
    **/
    public function new_log_insert($name, $eventid){

		//Set other variables
        $value = 0;
        $table= "log";
        $uxtime = $this->unixTime();

        //Add mandatory values
        $dbInsertArray[$table . '_name'] = $name;
        $dbInsertArray[$table . '_value'] = $value;
        $dbInsertArray[$table . '_eventid'] = $eventid;
        $dbInsertArray[$table . '_uxtime'] = $uxtime;

		//Insert new poll and return intert id
        try {
    		$logid = DB::table($table)->insertGetId($dbInsertArray);

            $response = array(
				'status' => 'success',
				'logid' => $logid,
                'message' => 'New log created'
			);

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
