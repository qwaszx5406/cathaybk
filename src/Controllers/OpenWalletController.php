<?php

namespace Cathaybk\Api\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cathaybk\Api\OpenWallet;

class OpenWalletController extends Controller
{
	public function __invoke(Request $request, $method){
		$OpenWallet = new OpenWallet();
		if( method_exists( $this, $method ) ){
			$this->$method($OpenWallet, $request);
		}else{
			return json_encode([ 'error' => true ]);
		}		
	}
	
	public function charge_pos( $OpenWallet, $request ){
		$data = [];
		$value = $OpenWallet->charge_pos($data);
		return dump($value);
	}
	
	public function getPaymentUrl( $OpenWallet, $request ){
		$data = [
			'callbackUrl' => route('OpenWallet_callback'),
			'redirectUrl' => ''
		];
		$value = $OpenWallet->getPaymentUrl($data);}
		return dump($value);
	}
	
	public function callback(){
		$data = file_get_contents('php://input');
		
		$data = json_decode($data, true);
		
		return $data;
	}
}