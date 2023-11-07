<?php

namespace App\Http\Controllers;

use DB;
use App\Classes\Traits\General;
use Illuminate\Http\Request;

class PostImageController extends Controller
{
	use General;

    public function postImage(Request $request)
    {
        $this->validate($request, ['image' => 'required|image|max:2000']);

		$data = $request->all();
		$id = $data['id'];
		$table = $data['table'];

        $picName = $request->file('image')->getClientOriginalName();
        $destinationPath = 'uploads/images/';

        $path = $request->file('image')->move($destinationPath, $picName);

		$uxtime = $this->unixTime();
		$image = $this->generalBase64($path);

		unlink($destinationPath . $picName);

		$results = DB::table($table)
			->where($table . '_id', $id)
			->update([$table . '_image' => $image, $table . '_uxtime' => $uxtime]);

		if($results == true){

			$response[] = array(
				'status' => 'success',
				'base64image' => $image,
				'message' => 'Image uploaded successfully'
			);
			return response()->json($response, 200);
		}
		else{

			$response[] = array(
				'status' => 'fail',
				'message' => 'Error uploading image'
			);
			return response()->json($response, 400);
		}
	}

    public function postRemoveImage(Request $request)
    {
        $data = $request->all();
		$id = $request->id;
		$table = $request->table;

        $image = null;
        $uxtime = $this->unixTime();

		$results = DB::table($table)
			->where($table . '_id', $id)
			->update([$table . '_image' => $image, $table . '_uxtime' => $uxtime]);

		if($results == true){

			$response[] = array(
				'status' => 'success',
				'base64image' => $image,
				'message' => 'Image removed successfully'
			);
			return response()->json($response, 200);
		}
		else{

			$response[] = array(
				'status' => 'fail',
				'message' => 'Error removing image'
			);
			return response()->json($response, 400);
		}
	}
}
