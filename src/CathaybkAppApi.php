<?php
//GC_vic:國泰金流 OP錢包
namespace Cathaybk\Api;

use Illuminate\Support\Facades\Http;

class CathaybkAppApi{
	
	private $gateway;
	private $merchantKey; 		
	private $corporateId;		
	private $merchant_id; 		
	private $terminal_id;
	private $walletId;	
	private $companyTaxId;
	private $storeId;			
	private $storeName;		
	private $cid;
	private $logoUrl;
	private $merchantTid;
	
	/**
	 * $walletType: 1 一次付清 2 訂閱制
	 */
	public function __construct( $walletType = 1, $authParty = 'OPW' ){
		$openWallet = config('cathaybk.openWallet');
		$this->gateway = $openWallet['gateway'];
		$this->merchantKey = $openWallet['merchantKey']; 		
		$this->corporateId = $openWallet['corporateId'];		
		$this->merchant_id = $openWallet['merchant_id']; 		
		$this->terminal_id = $openWallet['terminal_id'];
		$this->walletId = $openWallet['walletId'];
		if( $walletType == 2 ){
			$this->companyTaxId = $openWallet['companyTaxId_subscription']; 
		}else{
			$this->companyTaxId = $openWallet['companyTaxId'];
		}
		
		$this->storeId = $openWallet['storeId'];			
		$this->storeName = $openWallet['storeName'];		
		$this->merchantTid = $openWallet['merchantTid'];
		$this->cid = $openWallet['cid'];
		$this->logoUrl = $openWallet['logoUrl'];
		$this->authParty = $authParty;
	}
	
	private function get_posRefNo(){
		$posRefNo_len = 6;
		$posRefNo = '';

		$word = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
		$len = strlen($word);

		for ($i = 0; $i < $posRefNo_len; $i++) {
			$posRefNo .= $word[rand() % $len];
		}

		return $posRefNo;
	}
	
	private function request_post( $endpoint, $data ){
		try{
			$response = Http::timeout(15)->post($this->gateway . '/' . $endpoint, $data );
			if( $response->status() == 200 ){
				if( $response->json() == null ){
					return false;
				}
				return $response->json();
			}
			return [
				'res' => $response->body(),
				'url' => $this->gateway,
				'endpoint' => $endpoint,
				'data' => $data,
			];
		}catch(\Exception $ex){
			return [
				'res' => $ex->getMessage(),
				'url' => $this->gateway,
				'endpoint' => $endpoint,
				'data' => $data,
			];
			// return false;   
        }
	}
	
	//1112 timeout錯誤 
	public function get_cathay_return( $method, $request, $return = [], $index = 1 ){
		if( isset($return['statusCode']) && $return['statusCode'] != '1112' ){
			return  $return;
		}
		
		if( $index == 3 ){
			return false;
		}
		
		$index++;
		$return = $this->$method($request);
		return $this->get_cathay_return( $method, $request, $return, $index );
	}
	
	/**
	 * Init 透過 Payment Hub 取得商家設定。
	 */
	public function opw_init( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'memberId'		=> '',
			'payload'		=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'init', $data );
	}
	
	/**
	 * Bind Card 從 Payment Hub 啟動綁卡並取得 OTP。
	 */
	public function bind_card( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'memberId'		=> '',
			'payload'		=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'bind_card', $data );
	}
	
	/**
	 * Verify OTP 
	 * 進行綁定國泰世華卡時，將 OTP 傳遞至 Payment Hub 以驗證及取得綁卡結果。
	 * 進行綁定其他銀行卡時，將綁卡資訊傳遞至 Payment Hub 以取得綁卡結果。
	 */
	public function verify_otp( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'payload'		=> $request,
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'verify_otp', $data );
	}
	
	/**
	 * Resend OTP 
	 * 進行綁定國泰世華卡時，請 Payment Hub 產生新的 OTP 並發送給會員。
	 *
	 */
	public function resend_otp( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'payload'		=> $request,
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'resend_otp', $data );
	}
	
	/**
	 * Sync Data 
	 * 從 Payment Hub 同步綁卡和條碼資料到商家行動 app。
	 */
	public function sync_data( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'memberId'		=> '',
			'payload'		=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'sync_data', $data );
	}
	
	/**
	 * POS Purchase 
	 * 由 POS 機讀取條碼進行交易.
	 */
	public function charge_pos( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'walletId'		=> $this->walletId,
			'authParty'		=> $this->authParty,
			'entryMode'		=> '01',
			'barcode'		=> '',
			'amount'		=> '',
			'nonDiscountAmount'	=> '',
			'nonRewardsAmount'	=> '',
			'rewardsDiscountType' => '',
			'rewardsClassification' => '',
			'rewardsCode'	=> '',
			'mid'			=> $this->merchant_id,
			'tid'			=> $this->terminal_id,
			'storeId'		=> $this->storeId,
			'storeName'		=> $this->storeName,
			// 'storeAddress'	=> '',
			'posRefNo'		=> $this->get_posRefNo(),
			'merchantTid'	=> '',
			'merchantTradeNo' => '',
			'merchantTradeDate'	=> '',
			'merchantTradeTime'	=> '',
			// 'eppTenure'		=> '',
			// 'transName'		=> '',
			// 'productList'	=> [
				// [
					// 'skuCode'	=> '',
					// 'amount'	=> '',
					// 'quantity'	=> ''
				// ]
			// ],
			'locale'		=> 'zh-Hant',
			// 'merRedempCode'	=> '',
			// 'merRedempAmount'	=> '',
			// 'eventInfo'		=> [
				// [
					// 'code'		=> '',
					// 'number'	=> '',
					// 'remark'	=> ''
				// ]
			// ],
			// 'field1' 		=> '', 
			// 'field2' 		=> '',
			// 'field3' 		=> '',
			// 'field4' 		=> '',
			// 'field5' 		=> '',
			// 'field6' 		=> '',
			// 'field7' 		=> '',
			// 'field8' 		=> '',
			// 'field9' 		=> '',
			// 'field10' 		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'charge_pos', $data );
	}
	
	/**
	 * In-App Purchase
	 * 由會員從商家行動 app 進行購物支付消費金額。
	 */
	public function charge_app( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'walletId'		=> $this->walletId,
			'merchantTradeNo' => '',
			'merchantTradeDate'	=> '',
			'merchantTradeTime'	=> '',
			// 'isEpp'			=> '',
			// 'eppTenure'		=> '',
			// 'productList'	=> [
				// [
					// 'skuCode'	=> '',
					// 'amount'	=> '',
					// 'quantity'	=> ''
				// ]
			// ],
			'locale'		=> 'zh-Hant',
			// 'merRedempCode'	=> '',
			// 'merRedempAmount'	=> '',
			// 'storeId'		=> $this->storeId,
			// 'memberId'		=> '',
			// 'amount'		=> '',
			'payload'		=> '',
			// 'field1' 		=> '', 
			// 'field2' 		=> '',
			// 'field3' 		=> '',
			// 'field4' 		=> '',
			// 'field5' 		=> '',
			// 'field6' 		=> '',
			// 'field7' 		=> '',
			// 'field8' 		=> '',
			// 'field9' 		=> '',
			// 'field10' 		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'charge_app', $data );
	}
	
	/**
	 * Void Transaction 
	 * 由商家伺服器或商家行動 app 進行取消交易。
	 */
	public function void_transaction( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'transactionId'	=> '',
			'authParty'		=> $this->authParty,
			// 'entryMode'		=> '01',
			'mid'			=> $this->merchant_id,
			'tid'			=> $this->terminal_id,
			'storeId'		=> $this->storeId,
			'storeName'		=> $this->storeName,
			// 'merchantTid'	=> '',
			'merchantTradeNo' => '',
			'merchantTradeDate'	=> '',
			'merchantTradeTime'	=> '',
			// 'memberId'		=> '',
			// 'payload'		=> '',
			'locale'		=> 'zh-Hant',
			// 'field1' 		=> '', 
			// 'field2' 		=> '',
			// 'field3' 		=> '',
			// 'field4' 		=> '',
			// 'field5' 		=> '',
			// 'field6' 		=> '',
			// 'field7' 		=> '',
			// 'field8' 		=> '',
			// 'field9' 		=> '',
			// 'field10' 		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'void_transaction', $data );
	}
	
	/**
	 * Refund Transaction 
	 * 由商家伺服器或商家行動 app 進行退貨交易。
	 */
	public function refund_transaction( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'transactionId'	=> '',
			'authParty'		=> $this->authParty,
			// 'entryMode'		=> '01',
			'barcode'		=> '',
			'amount'		=> '',
			'mid'			=> $this->merchant_id,
			'tid'			=> $this->terminal_id,
			'storeId'		=> $this->storeId,
			'storeName'		=> $this->storeName,
			'posRefNo'		=> $this->get_posRefNo(),
			'merchantTid'	=> $this->merchantTid,
			'merchantTradeNo' => '',
			'merchantTradeDate'	=> '',
			'merchantTradeTime'	=> '',
			'locale'		=> 'zh-Hant',
			// 'field1' 		=> '', 
			// 'field2' 		=> '',
			// 'field3' 		=> '',
			'field4' => json_encode([
				'companyTaxId' => $this->companyTaxId,
			]),
			// 'field5' 		=> '',
			// 'field6' 		=> '',
			// 'field7' 		=> '',
			// 'field8' 		=> '',
			// 'field9' 		=> '',
			// 'field10' 		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'refund_transaction', $data );
	}
	
	/**
	 * Remove Card
	 * 由商家伺服器或商家行動 app 移除會員綁定的卡。
	 */
	public function remove_card( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'memberId'		=> '',
			'cardId'		=> '',
			'payload'		=> '',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'remove_card', $data );
	}
	
	/**
	 * Remove Member
	 * 由商家伺服器移除會員帳號。
	 */
	public function remove_member( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'memberId'		=> '',
			'locale'		=> 'zh-Hant',
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'remove_member', $data );
	}
	
	/**
	 * Charge App Status
	 * 由商家伺服器查詢交易狀態。
	 * 由商家行動 app 查詢 3D 交易狀態。
	 */
	public function charge_app_status( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'transactionId'	=> '',
			'merchantTradeNo' => '',
			'authParty'		=> $this->authParty,
			// 'entryMode'		=> '01',
			'locale'		=> 'zh-Hant',
			'memberId'		=> '',
			'payload'		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'charge_app_status', $data );
	}
	
	/**
	 * Barcode Query
	 * 查詢條碼相關信息，如會員 ID、卡號前六後四碼和卡片 ID。
	 */
	public function barcode_query( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'barcode'		=> '',
			'locale'		=> 'zh-Hant'
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'barcode_query', $data );
	}
	
	/**
	 * Confirm Transaction
	 * 由商家伺服器確認交易。
	 */
	public function confirm_transaction( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'transactionId'		=> '',
			'authParty'		=> $this->authParty,
			'entryMode'		=> '01',
			'mid'			=> $this->merchant_id,
			'tid'			=> $this->terminal_id,
			'merchantTradeNo' => '',
			'confirmPayment'	=> '',
			'locale'		=> 'zh-Hant'
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'confirm_transaction', $data );
	}
	
	/**
	 * Get Payment URL / QR
	 * 取得第三方錢包支付的交易 URL／QR。
	 */
	public function getPaymentUrl( $request ){
		
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'walletId'		=> $this->walletId,
			'authParty'		=> $this->authParty,
			'entryMode'		=> '02',
			'mid'			=> $this->merchant_id,
			'tid'			=> $this->terminal_id,
			'storeId'		=> $this->storeId,
			'storeName'		=> $this->storeName,
			// 'storeAddress'	=> '',
			'posRefNo'		=> $this->get_posRefNo(),
			'merchantTid'	=> $this->merchantTid,
			'merchantTradeNo' => '',
			'merchantTradeDate'	=> '',
			'merchantTradeTime'	=> '',
			'transName'		=> 'OP錢包交易',
			'amount'		=> '',
			'callbackUrl'	=> '',
			'redirectUrl'	=> '',
			// 'nonDiscountAmount'	=> '',
			// 'nonRewardsAmount'	=> '',
			// 'productList'	=> [
				// [
					// 'skuCode'	=> '',
					// 'amount'	=> '',
					// 'quantity'	=> ''
				// ]
			// ],
			'locale'		=> 'zh-Hant',
			// 'field1' 		=> '', 
			// 'field2' 		=> '',
			// 'field3' 		=> '',
			'field4' => json_encode([
				'cid' => $this->cid ? $this->cid : '',
				'logoUrl' => $this->logoUrl ? $this->logoUrl : '',
				'companyTaxId' => $this->companyTaxId ? $this->companyTaxId : '',
				'walletId' => $this->walletId ? $this->walletId : '',
			]),
			// 'field5' 		=> '',
			// 'field6' 		=> '',
			// 'field7' 		=> '',
			// 'field8' 		=> '',
			// 'field9' 		=> '',
			// 'field10' 		=> ''
		];
		
		if( is_array($request) ){
			
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'getPaymentUrl', $data );
	}
	
	/**
	 * Retrieve Mobile Error Log
	 * 將來自行動 App 的 Error log 進行保存
	 */
	public function retrieve_mobile_error_log( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'payload'		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'retrieve_mobile_error_log', $data );
	}
	
	/**
	 * Activate Card
	 * 從行動 App 啓動待啟用的卡片
	 */
	public function activate_card( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'payload'		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'activate_card', $data );
	}
	
	/**
	 * Top-up Payment
	 * 透過商家支付儲值交易。
	 */
	public function charge_topup( $request ){
		$data = [
			'merchantKey' 	=> $this->merchantKey,
			'corporateId' 	=> $this->corporateId,
			'walletId'		=> $this->walletId,
			'authParty'		=> $this->authParty,
			'entryMode'		=> '07',
			'mid'			=> $this->merchant_id,
			'tid'			=> $this->terminal_id,
			'storeId'		=> $this->storeId,
			'storeName'		=> $this->storeName,
			// 'storeAddress'	=> '',
			// 'posRefNo'		=> '',
			// 'merchantTid'	=> '',
			'merchantTradeNo' => '',
			'merchantTradeDate'	=> '',
			'merchantTradeTime'	=> '',
			'amount'		=> '',
			'locale'		=> 'zh-Hant',
			// 'field1' 		=> '', 
			// 'field2' 		=> '',
			// 'field3' 		=> '',
			// 'field4' 		=> '',
			// 'field5' 		=> '',
			// 'field6' 		=> '',
			// 'field7' 		=> '',
			// 'field8' 		=> '',
			// 'field9' 		=> '',
			// 'field10' 		=> ''
		];
		
		if( is_array($request) ){
			$data = array_merge( $data, $request );
		}
		
		return $this->request_post( 'charge_topup', $data );
	}
	
	public function custom_array_merge( $array1, $array2 ){
		foreach( $array1 as $key => $value ){
			if( isset($array2[$key]) ){
				if( $key == 'field4' ){
					$array1[$key] = json_encode(array_merge($value, $array2[$key]));
				}else{
					$array1[$key] = $array2[$key];
				}
			}
		}
		return $array1;
	}
	
}