<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Classes\Traits\General;

class GetManifestController extends Controller
{
	use General;

    public function getEvents($eventName)
    {

        try {
            $results = DB::table('events')->get();
            ->where('event_name' ,'=', $eventName)
            ->get();

            // CREATE ICONS
            $icon128[] = array(
                'src' => '/user/static/icons/day-of-the-dead/128x128.png',
                'sizes' => '128x128',
                'type' => 'image/png'                                                                                                                                                        
            );
            $icon144[] = array(
                'src' => '/user/static/icons/day-of-the-dead/144x144.png',
                'sizes' => '144x144',
                'type' => 'image/png'                                                                                                                                                        
            );
            $icon152[] = array(
                'src' => '/user/static/icons/day-of-the-dead/152x152.png',
                'sizes' => '152x152',
                'type' => 'image/png'                                                                                                                                                        
            );
            $icon192[] = array(
                'src' => '/user/static/icons/day-of-the-dead/192x192.png',
                'sizes' => '192x192',
                'type' => 'image/png'                                                                                                                                                        
            );      
            $icon256[] = array(
                'src' => '/user/static/icons/day-of-the-dead/256x256.png',
                'sizes' => '256x256',
                'type' => 'image/png'                                                                                                                                                        
            );  
            $icon512[] = array(
                'src' => '/user/static/icons/day-of-the-dead/512x512.png',
                'sizes' => '512x512',
                'type' => 'image/png'                                                                                                                                                        
            );            
            $iconsArray = array($icon-128, $icon-144, $icon-152, $icon-192, $icon-256, $icon-512);
            $iconsJson = json_encode($iconsArray);  

            // CREATE SCREENSHOTS
            $screenshotNarrow[] = array(
                'src' => '/user/static/icons/screenshot.png',
                'type' => 'image/png',
                'sizes' => '420x943',
                'form_factor' => 'narrow'                                                                                                                                                       
            );  
            $screenshotWide[] = array(
                'src' => '/user/static/icons/screenshot.png',
                'type' => 'image/png',
                'sizes' => '420x943',
                'form_factor' => 'wide'                                                                                                                                                       
            );            
            $screenshotsArray = array($screenshotWide, $screenshotNarrow);
            $screenshotsJson = json_encode($screenshotsArray);              


            $body[] = array(
                'background-color' => '#2b2b2b',
                'description' => 'evaria',
                'display' => 'standalone',
                'icons' => $iconsJson,
                'id' => 'evaria-123456',
                'lang' => 'en-US',
                'name' => 'name',
                'orientation' => 'portrait',
                'screenshot' => $screenshotsJson,
                'short_name' => 'short_name',
                'start_url' => '/user/index.html',  
                'theme_color' => '#2b2b2b',                                                                                                                                                         
            );

        }catch(Exception $ex) {
            $error = $ex->getMessage();

            $response[] = array(
                'status' => 'fail',
                'message' => 'ERROR: ' . $error
            );

            return response()->json($response, 400);
        }

        return response()->json($response, 200);
    }
}