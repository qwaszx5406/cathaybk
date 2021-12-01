<?php
//GC_vic:國泰金流
namespace Cathaybk\Api;

use Illuminate\Support\Facades\Http;

class CathaybkApi{
	
	private $gateway;
	private $merchantKey; 		
	private $corporateId;		
	private $merchant_id; 		
	private $terminal_id;		
	private $storeId;			
	private $storeName;		
	private $merchantTid;
	
	public function __construct(){
		$normal = config('cathaybk.normal');
		$this->gateway = $normal['gateway'];
		$this->merchantKey = $normal['merchantKey']; 		
		$this->corporateId = $normal['corporateId'];		
		$this->merchant_id = $normal['merchant_id']; 		
		$this->terminal_id = $normal['terminal_id'];		
		$this->storeId = $normal['storeId'];			
		$this->storeName = $normal['storeName'];		
		$this->merchantTid = $normal['merchantTid'];
	}
	
	private function request_post( $endpoint, $data ){
		try{
			$response = Http::timeout(15)->post($this->gateway . '/' . $endpoint, $data );
			if( $response->status() == 200 ){
				if( $response->json() == null ){
					return false;
				}
				return $response->json();
			}else{
				return false;
			}
		}catch(\Exception $ex){
			// return $ex;   
			return false;   
        }
	}
	
	/**
	 * Web Request Bind Card 啟動網頁綁卡
	 */
	public function req_bind_card( $request ){
		$data = [
			'merchantKey' => $this->merchantKey,
			'corporateId' => $this->corporateId,
			'memberId' => $request, 
			'resultCallbackUrl' => '',
			'locale' => 'zh-Hant' //顯示應用程式語言設定
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'req_bind_card', $data );
	}
	
	/**
	 * Web Verify Bind Card 取得綁卡結果
	 */
	public function verify_bind_card( $requset ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'sessionId'			=> $requset,
			'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'verify_bind_card', $data );
	}
	
	/**
	 * Web Sync Data 由商家伺服器和 Payment Hub 之間同步會員綁卡的資料
	 */
	public function web_sync_data( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'memberId'			=> '',
			'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'web_sync_data', $data );
	}
	
	
	/**
	 * Web Remove Card 移除會員綁定的卡
	 */
	public function web_remove_card( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'memberId'			=> '',
			'cardId'			=> '',
			'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'web_remove_card', $data );
	}
	 
	/**
	 * Web Payment 發動交易
	 */
	public function req_payment_info( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'memberId' 			=> '', //會員ID 不分大小寫及不支援特殊符號且需是 10 碼唯一
			'mobileNo' 			=> '', //手機號碼
			'email'				=> '', //email
			'amount'			=> '', //交易總金額，包括 2 位小數
			'chargeType'		=> '', //交易類型
			'mid'				=> $this->merchant_id,
			'tid'				=> $this->terminal_id,
			'storeId'			=> $this->storeId,
			'storeName'			=> $this->storeName,
			'merchantTid'		=> $this->merchantTid,
			'resultCallbackUrl' => '',
			'merchantTradeNo'	=> '', //商家交易編號
			'merchantTradeDate' => '', //網頁交易日期
			'merchantTradeTime' => '', //網頁交易時間
			// 'productList'		=> [
				// [
					// 'skuCode' 	=> '',
					// 'amount'	=> '',
					// 'quantity'	=> ''
				// ],
			// ],
			'locale' => 'zh-Hant' //顯示應用程式語言設定
			// 'merRedempCode' 	=> '',
			// 'merRedempAmount'	=> '',
			// 'field1'			=> '',
			// 'field2'			=> '',
			// 'field3'			=> '',
			// 'field4'			=> '',
			// 'field5'			=> '',
			// 'field6'			=> '',
			// 'field7'			=> '',
			// 'field8'			=> '',
			// 'field9'			=> '',
			// 'field10'			=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'req_payment_info', $data );
	}
	
	/**
	 * Web Verify Payment 確認交易結果
	 */
	public function charge_web_status( $request ){
		if( !is_array($request) ){
			$data = [
				'merchantKey' 		=> $this->merchantKey,
				'corporateId' 		=> $this->corporateId,
				'sessionId'			=> $request,
				'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
			];
		}else{
			$data = [
				'merchantKey' 		=> $this->merchantKey,
				'corporateId' 		=> $this->corporateId,
				'memberId' 			=> '',
				'merchantTradeNo'	=> '',
				'transactionId' 	=> '',
				'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
			];
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'charge_web_status', $data );
	}
	
	/**
	 * Web Bind Card with Transaction ID 交易後綁定卡片
	 */
	public function web_bind_card( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'trxId'				=> '', //transactionId 交易完成後的識別編號
			'memberId' 			=> '',
			'mobileNo' 			=> '', 
			'email'				=> '',	
			'resultCallbackUrl' => '',
			'locale' => 'zh-Hant' //顯示應用程式語言設定
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'web_bind_card', $data );
	}
	
	/**
	 * Web Payment with Binded Card 使用綁定卡做一般交易或紅利交易
     */
	public function charge_web( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'memberId' 			=> '',
			'amount' 			=> '',
			'chargeType' 		=> '',
			'mid'				=> $this->merchant_id,
			'tid'				=> $this->terminal_id,
			'storeId'			=> $this->storeId,
			'storeName'			=> $this->storeName,
			'merchantTid'		=> $this->merchantTid,
			'resultCallbackUrl' => '',
			'merchantTradeNo'	=> '',
			'merchantTradeDate' => '',
			'merchantTradeTime' => '',
			'cardId'			=> '',
			// 'productList'		=> [
				// [
					// 'skuCode' 	=> '',
					// 'amount'	=> '',
					// 'quantity'	=> ''
				// ],
			// ],
			'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
			// 'merRedempCode' 	=> '',
			// 'merRedempAmount'	=> '',
			// 'field1'			=> '',
			// 'field2'			=> '',
			// 'field3'			=> '',
			// 'field4'			=> '',
			// 'field5'			=> '',
			// 'field6'			=> '',
			// 'field7'			=> '',
			// 'field8'			=> '',
			// 'field9'			=> '',
			// 'field10'			=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'charge_web', $data );
	}
	
	
	/**
	 * Void Transaction 由商家伺服器發起的取消交易
	 */
	public function void_transaction( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'transactionId' 	=> '',
			'mid'				=> $this->merchant_id,
			'tid'				=> $this->terminal_id,
			'storeId'			=> $this->storeId,
			'storeName'			=> $this->storeName,
			'merchantTid'		=> $this->merchantTid,
			'merchantTradeNo'	=> '',
			'merchantTradeDate' => '',
			'merchantTradeTime' => '',
			'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
			// 'field1'			=> '',
			// 'field2'			=> '',
			// 'field3'			=> '',
			// 'field4'			=> '',
			// 'field5'			=> '',
			// 'field6'			=> '',
			// 'field7'			=> '',
			// 'field8'			=> '',
			// 'field9'			=> '',
			// 'field10'			=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'void_transaction', $data );
	}
	
	/**
	 * Refund Transaction 由商家伺服器發起的退貨交易
	 */
	public function refund_transaction( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'transactionId' 	=> '',
			'amount'			=> '',
			'mid'				=> $this->merchant_id,
			'tid'				=> $this->terminal_id,
			'storeId'			=> $this->storeId,
			'storeName'			=> $this->storeName,
			'merchantTid'		=> $this->merchantTid,
			'merchantTradeNo'	=> '',
			'merchantTradeDate' => '',
			'merchantTradeTime' => '',
			'locale' 			=> 'zh-Hant' //顯示應用程式語言設定
			// 'field1'			=> '',
			// 'field2'			=> '',
			// 'field3'			=> '',
			// 'field4'			=> '',
			// 'field5'			=> '',
			// 'field6'			=> '',
			// 'field7'			=> '',
			// 'field8'			=> '',
			// 'field9'			=> '',
			// 'field10'			=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'refund_transaction', $data );
	}
	
	/**
	 * Request Barcode 索取有效條碼相關信息。
	 */
	public function request_barcode( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'memberId' 			=> '', 
			'cardId' 			=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'request_barcode', $data );
	}
	
	/**
	 * Remove Member 由商家伺服器移除會員帳號
	 */
	public function remove_member( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'memberId' 			=> '', 
			'locale' 			=> 'zh-Hant', 
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'remove_member', $data );
	}
	
	/**
	 * Web Activate Card 啓動待激活的卡片
	 */
	public function web_activate_card( $request ){
		$data = [
			'merchantKey' 		=> $this->merchantKey,
			'corporateId' 		=> $this->corporateId,
			'cardId'			=> '', //transactionId 交易完成後的識別編號
			'resultCallbackUrl' => '', 
			'locale' => 'zh-Hant' //顯示應用程式語言設定
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'web_activate_card', $data );
	}
	
	public function response(){
		
        $data = file_get_contents('php://input');
		
		$data = json_decode($data, true);
		
		return $data;
	}
}