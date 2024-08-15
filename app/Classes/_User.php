<?php

namespace App\Classes;
use App\Classes\Traits\General;
use App\Classes\QR_Code;
use App\Classes\Log;
use DB;

class _User{

	use General;

	private $permitedAttemps = 99999;
    private $registrationCode = 'superstar';
	private $adminRole = 3;
	//private $permitedAttemps = -1;

    /**
    * Return the logged in user.
    * @return user array data
    */
    public function getUser(){
        return $this->user;
    }

    /**
    * Login function
    * @param string $email User email.
    * @param string $password User password.
    *
    * @return bool Returns login success.
    */
    public function login($email, $password, $token){
  
        
        if(strlen($password) > 0) {

			$user = DB::table('user')
			->where('user_email' ,'=', $email)
			->get();     

			if(count($user) == 1) {

				if(password_verify($password,$user[0]->user_password)){

					if($user[0]->user_wronglogins <= $this->permitedAttemps){

						$event = DB::table('event')
						->select('event_id', 'event_name')
						->where('event_userid' ,'=', $user[0]->user_id)
						->get();

						$array = json_decode(json_encode($event), true);

						$user[0]->status = 'success';
						$user[0]->message = 'You are now logged in.';
						$user[0]->eventslist = $array;
						unset($user[0]->user_password);

						return $user[0];

					}
					else {

						$app = app();
						$response = $app->make('stdClass');
						$response->status = 'fail';
						$response->message = 'This account is blocked, please contact our support team.';

						return $response;
					}
				}
                else{

                    $app = app();
                    $response = $app->make('stdClass');
                    $response->status = 'fail';
                    $response->message = 'This user does not exist.';

                    return $response;
                }
			}
			else{

				$app = app();
				$response = $app->make('stdClass');
				$response->status = 'fail';
				$response->message = 'This user does not exist.';

				return $response;
			}

        }
        elseif(strlen($token) > 0) {

			$user = DB::table('guest')
			->where('guest_email' ,'=', $email)
			->where('guest_token' ,'=', $token)
			->get();

			$count = count($user);

			if($count > 0) {

                $event_list = array();

                $event = DB::table('event')
                ->select('event_id', 'event_name')
                ->where('event_id' ,'=', $user[0]->guest_eventid)
                ->get();

                $json = json_decode(json_encode($event[0]), true);

                array_push($event_list, $json);

                $app = app();
                $response = $app->make('stdClass');
                $response->status = 'success';
                $response->user_id = $user[0]->guest_id;
                $response->user_firstname = $user[0]->guest_firstname;
                $response->user_lastname = $user[0]->guest_lastname;
                $response->user_email = $user[0]->guest_email;
                $response->user_token = $user[0]->guest_token;
                $response->user_role = $user[0]->guest_role;
                $response->user_defaulteventid = $user[0]->guest_eventid;
                $response->eventslist = $event_list;
                $response->message = 'You are now logged in';

                $log = new Log();
                $name = "downloads";
				$result = $log->update_log($name, $user[0]->guest_eventid);

                return $response;

			}
            else{

                $app = app();
                $response = $app->make('stdClass');
                $response->status = 'fail';
                $response->message = 'This event does not exist.';

                return $response;
            }
        }
        else{

            $guest = DB::table('guest')
            ->where('guest_email' ,'=', $email)
            ->get();

            $len = count($guest);

            if($len > 0) {

                $events = array();

                for ($i = 0; $i < $len; $i++) {

                    $eventid = $guest[$i]->guest_eventid;
                    
                    $event = DB::table('event')
                    ->where('event_id' ,'=', $eventid)
                    ->where('event_expirydate' ,'>', strVal(date('Ymd')))
                    ->get();

                    if(count($event) > 0) {
                        $event[0]->{'event_email'} = $guest[$i]->guest_email;
                        $event[0]->{'event_token'} = $guest[$i]->guest_token;
    
                        unset($event[0]->event_image);
                        unset($event[0]->event_userid);
    
                        $arr = json_decode(json_encode($event[0]), true);
    
                        array_push($events, $arr);
                    }
                    else{

                        $app = app();
                        $response = $app->make('stdClass');
                        $response->status = 'fail';
                        $response->message = 'This event does not exist.';
        
                        return $response;
                    }
                }

                $app = app();
                $response = $app->make('stdClass');
                $response->status = 'success';
                $response->user_id = $guest[0]->guest_id;
                $response->user_firstname = $guest[0]->guest_firstname;
                $response->user_lastname = $guest[0]->guest_lastname;
                $response->user_email = $guest[0]->guest_email;
                $response->user_token = $guest[0]->guest_token;
                $response->user_role = $guest[0]->guest_role;
                $response->user_defaulteventid = $guest[0]->guest_eventid;
                $response->eventslist = $events;
                $response->message = 'You are now logged in.';

                return $response;
            }
            else{

                $app = app();
                $response = $app->make('stdClass');
                $response->status = 'fail';
                $response->message = 'This event does not exist.';

                return $response;
            }
        }
    }

    /**
    * Register a new user account function
    * @param string $email User email.
    * @param string $fname User first name.
    * @param string $lname User last name.
    * @param string $pass User password.
    * @return boolean of success.
    */
    public function new_guest_insert($params){

        $firstname = $params[0];
        $lastname = $params[1];
        $email = $params[2];
        $hyphen_name = $params[3];
        $eventid = $params[4];
        if(count($params) == 6) {
            $role = $params[5];
        }
        else {
            $role = 2;
        }

        if($role = 1) {
            $qr_code = new QR_Code();
            $string = $qr_code->setQRCodeString($hyphen_name, $eventid);
            $exists = $qr_code->checkQRCode($string, $eventid);
        }
        else {
            $qr_code = new QR_Code();
            $string = $qr_code->setQRCodeString(urlencode($email), $eventid);
            $exists = $qr_code->checkQRCode($string, $eventid);
            }        

        if($exists == false) {

            //Set other variables
            $uxtime = $this->unixTime();
            $usertoken = $this->getToken(32);

            try {
                $guestid = DB::table('guest')->insertGetId([
                    'guest_firstname' => $firstname,
                    'guest_lastname' => $lastname,
                    'guest_email' => $email,
                    'guest_role' => $role,
                    'guest_token' => $usertoken,
                    'guest_eventid' => $eventid,
                    'guest_uxtime' => $uxtime,
                ]);

                $qr = new QR_Code();
                if($role == 1) {
                    $new_qr_create = $qr->new_qr_create($email, $eventid, 0, $usertoken, $string);
                }
                elseif($role == 2) {
                    $new_qr_create = $qr->new_qr_create($email, $eventid, $guestid, $usertoken, $string);
                }

                extract($new_qr_create);

                if($qrcode_status == "success") {

                    if($guestid){
                        $response = array(
                            'status' => 'success',
                            'guest_id' => $guestid,
                            'guest_firstname' => $firstname,
                            'guest_lastname' => $lastname,
                            'guest_email' => $email,
                            'guest_role' => $role,
                            'guest_token' => $usertoken,
                            'guest_eventid' => $eventid,
                            'qrcode_id' => $qrcode_id,
                            'qrcode_value' => $qrcode_value,
                            'qrcode_image' => $qrcode_image,
                            'guest_message' => $qrcode_message
                        );
                        return $response;
                    }
                }
                else {
                    $response = array(
                        'status' => 'fail',
                        'message' => $qrcode_message
                    );
                    return $response;
                }
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
        else {
            $response = array(
                'status' => 'fail',
                'message' => 'Email address already exists!'
            );
            return $response;
        }
    }

    /**
    * Register a new user account function
    * @param string $email User email.
    * @param string $fname User first name.
    * @param string $lname User last name.
    * @param string $pass User password.
    * @return boolean of success.
    */
    public function registration($firstname, $lastname, $email, $password, $code){

        if(!(isset($firstname) && isset($lastname) && isset($password) && isset($email) && isset($code))){
            $response = array(
                'status' => 'fail',
                'message' => 'Please fill in all fields'
            );
            return $response;
        }
        if (!(filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $response = array(
                'status' => 'fail',
                'message' => 'Invalid email'
            );
            return $response;
        }
        if($this->checkEmail($email)){
            $response = array(
                'status' => 'fail',
                'message' => 'This email is already regsistered'
            );
            return $response;
        }
        if(!(strlen($password) > 7)){
            $response = array(
                'status' => 'fail',
                'message' => 'Password must be at least 8 characters'
            );
            return $response;
        }
		if($code != $this->registrationCode){
            $response = array(
                'status' => 'fail',
                'message' => 'Registration code is incorrect'
            );
            return $response;
        }

        try {
            $uxtime = $this->unixTime();
			$hashPassword = $this->hashPass($password);
			$token = $this->getToken(32);
            $uxtime = $this->unixTime();

            //Insert new poll and return intert id
    		$insert = DB::table('user')->insertGetId([
                'user_firstname' => $firstname,
                'user_lastname' => $lastname,
                'user_email' => $email,
                'user_password' => $hashPassword,
                'user_token' => $token,
                'user_defaulteventid' => 0,
                'user_role' => $this->adminRole,
                'user_uxtime' => $uxtime,
            ]);

            $response = array(
                'status' => 'success',
                'message' => 'Your registeration has been successful. Please log in.'
            );

            return  $response;
        }
        //Return error to the client
        catch(Exception $ex) {
            $response = array(
                'status' => 'fail',
                'message' => 'Registration error'
            );
            return $response;
		}
    }

    /**
    * Email the confirmation code function
    * @param string $email User email.
    * @return boolean of success.
    */
    //private function sendConfirmationEmail($email){
    //    $pdo = $this->pdo;
    //    $stmt = $pdo->prepare('SELECT confirm_code FROM user WHERE email = ? limit 1');
    //    $stmt->execute([$email]);
    //    $code = $stmt->fetch();

    //    $subject = 'Confirm your registration';
    //    $message = 'Please confirm you registration by pasting this code in the confirmation box: '.$code['confirm_code'];
    //    $headers = 'X-Mailer: PHP/' . phpversion();

    //    if(mail($email, $subject, $message, $headers)){
    //        return true;
    //    }else{
    //        return false;
    //    }
    //}

    /**
    * Activate a login by a confirmation code and login function
    * @param string $email User email.
    * @param string $confCode Confirmation code.
    * @return boolean of success.
    */
    //public function emailActivation($email,$confCode){
    //    $pdo = $this->pdo;
    //    $stmt = $pdo->prepare('UPDATE user SET confirmed = 1 WHERE email = ? and confirm_code = ?');
    //    $stmt->execute([$email,$confCode]);
    //    if($stmt->rowCount()>0){
    //        $stmt = $pdo->prepare('SELECT id, fname, lname, email, wrong_logins, user_role FROM user WHERE email = ? and confirmed = 1 limit 1');
    //        $stmt->execute([$email]);
    //        $user = $stmt->fetch();

    //        $this->user = $user;
    //        session_regenerate_id();
    //        if(!empty($user['email'])){
    //        	$_SESSION['user']['id'] = $user['id'];
	//            $_SESSION['user']['fname'] = $user['fname'];
	//            $_SESSION['user']['lname'] = $user['lname'];
	//            $_SESSION['user']['email'] = $user['email'];
	//            $_SESSION['user']['user_role'] = $user['user_role'];
	//            return true;
    //        }else{
    //        	$_SESSION['message'] = 'Account activitation failed.';
    //        	return false;
    //        }
    //    }else{
    //        $_SESSION['message'] = 'Account activitation failed.';
    //        return false;
    //    }
    //}

    /**
    * Password change function
    * @param int $id User id.
    * @param string $pass New password.
    * @return boolean of success.
    */
    //public function passwordChange($id,$pass){
    //    $pdo = $this->pdo;
    //    if(isset($id) && isset($pass)){
    //        $stmt = $pdo->prepare('UPDATE user SET password = ? WHERE id = ?');
    //        if($stmt->execute([$id,$this->hashPass($pass)])){
    //            return true;
    //        }else{
    //            $_SESSION['message'] = 'Password change failed.';
    //            return false;
    //        }
    //    }else{
    //        $_SESSION['message'] = 'Provide an ID and a password.';
    //        return false;
    //    }
    //}

    /**
    * Check if email is already used function
    * @param string $email User email.
    * @return boolean of success.
    */
    private function checkEmail($email){

        $users = DB::table('user')
        ->where('user_email' ,'=', $email)
        ->get();

        $count = count($users);

        if($count > 0){
            return true;
        }
        else{
            return false;
        }
    }

    /**
    * Register a wrong login attemp function
    * @param string $email User email.
    * @return void.
    */
    private function registerWrongLoginAttemp($email){
        $pdo = $this->pdo;
        $stmt = $pdo->prepare('UPDATE user SET user_wronglogins = user_wronglogins + 1 WHERE user_email = ?');
        $stmt->execute([$email]);
    }

    /**
    * Password hash function
    * @param string $password User password.
    * @return string $password Hashed password.
    */
    private function hashPass($pass){
        return password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
    * Generate user login token
    * @return void.
    */
    public function crypto_rand_secure($min, $max) {
        $range = $max - $min;
        if ($range < 1) return $min; // not so random...
        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd > $range);
        return $min + $rnd;
    }

    public function getToken($length) {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i=0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
        }

        return $token;
    }

    /**
    * Validate token
    * @param string $token
    * @param string $event_id
    * @param string $user_id
    *
    * @return bool Returns validate success or fail
    */
    public function validate_token($token, $eventid, $userid, $role) {

        if($role == 'user') {
            $results = DB::table('guest')
            ->select('guest_token', 'guest_eventid')
            ->where('guest_id' ,'=', $userid)
            ->get();

            $count = count($results);

            if($count == 1) {

                $guest_token = $results[0]->guest_token;
                $guest_eventid = $results[0]->guest_eventid;

                if($guest_token == $token && $guest_eventid == $eventid) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }

        else if($role == 'admin') {
            $results = DB::table('user')
            ->select('user_token')
            ->where('user_id' ,'=', $userid)
            ->get();

            $user_token = $results[0]->user_token;

            if($user_token == $token) {

                $results = DB::table('event')
                ->select('event_userid')
                ->where('event_id' ,'=', $eventid)
                ->where('event_userid' ,'=', $userid)
                ->get();

                $count = count($results);

                if($count == 1) {
                    return true;
                }
                else {
                    if($eventid == 'new') {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
}
