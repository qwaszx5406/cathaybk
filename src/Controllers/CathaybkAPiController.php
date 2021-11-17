<?php

namespace Cathaybk\Api\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cathaybk\Api\CathaybkApi;

class CathaybkAPiController extends Controller
{
	public function __invoke(Request $request, $method){
		$CathaybkApi = new CathaybkApi();
		if( method_exists( $this, $method ) ){
			$this->$method($CathaybkApi, $request);
		}else{
			return json_encode([ 'error' => true ]);
		}		
	}
	
	public function req_payment_info( $CathaybkApi, $request ){
		$data = [
			'resultCallbackUrl' => route('Cathaybk_callback'),
		];
		$value = $CathaybkApi->req_payment_info($data);
		return dump($value);
	}
	
	public function charge_web_status( $CathaybkApi, $request ){
		if( isset($request['seesionId']) ){
			$value = $CathaybkApi->charge_web_status($request['seesionId']);
		}else{
			$data = [];
			$value = $CathaybkApi->charge_web_status($data);
		}
		return dump($value);
	}
	
	public function callback(){
		$data = file_get_contents('php://input');
		
		$data = json_decode($data, true);
		
		return $data;
	}
}