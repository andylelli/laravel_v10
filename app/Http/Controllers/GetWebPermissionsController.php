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

			$croppedImage = $this->cropToSquare($event[0]->event_image);


			$url ="https://www.evaria.io/user/index.html?name=" . $eventname . "&id=" . $eventid . "&bg=" . $bgcolor;

			//$this->writeToLog(print_r($event, true));
        	return view('permission', ['url' => $url, 'eventname' => $event[0]->event_name, 'eventimage' => $croppedImage, 'bgcolor' => $bgcolor]);
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

	private function cropToSquare($base64Image) {
		// Remove the 'data:image/*;base64,' prefix if it exists
		$prefix = 'data:image/';
		if (strpos($base64Image, $prefix) === 0) {
			$commaPosition = strpos($base64Image, ',');
			if ($commaPosition !== false) {
				$base64Image = substr($base64Image, $commaPosition + 1);
			}
		}
	
		// Decode the Base64 string to get the image data
		$imageData = base64_decode($base64Image);
		
		// Create an image resource from the decoded data
		$image = imagecreatefromstring($imageData);
		if (!$image) {
			die('Invalid image data');
		}
	
		// Get the dimensions of the original image
		$width = imagesx($image);
		$height = imagesy($image);
	
		// Determine the size of the square (smallest dimension)
		$squareSize = min($width, $height);
	
		// Calculate the coordinates for the center cropping
		$x = ($width - $squareSize) / 2;  // Horizontal center
		$y = ($height - $squareSize) / 2; // Vertical center
	
		// Create a new image resource for the cropped square
		$croppedImage = imagecreatetruecolor($squareSize, $squareSize);
	
		// Copy the central part of the original image to the new image
		imagecopy($croppedImage, $image, 0, 0, $x, $y, $squareSize, $squareSize);
	
		// Detect the image type and set the correct content-type header
		$imageInfo = getimagesizefromstring($imageData);
	
		if ($imageInfo === false) {
			die('Error: Unable to detect image type');
		}
	
		// Output the cropped image based on the detected mime type
		header("Content-Type: " . $imageInfo['mime']);  // Dynamically set the content type
	
		if ($imageInfo['mime'] == 'image/png') {
			imagepng($croppedImage); // Output as PNG
		} elseif ($imageInfo['mime'] == 'image/jpeg' || $imageInfo['mime'] == 'image/jpg') {
			imagejpeg($croppedImage); // Output as JPEG or JPG
		} elseif ($imageInfo['mime'] == 'image/gif') {
			imagegif($croppedImage); // Output as GIF
		} else {
			die('Unsupported image type');
		}
	
		// Clean up
		imagedestroy($image);
		imagedestroy($croppedImage);
	
}
