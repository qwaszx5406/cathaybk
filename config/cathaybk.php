<?php
//GC_vic:2021-10-28 國泰世華金流config
return [
	'normal' => [
		'merchantKey' 		=> '', //銀行提供
		'corporateId' 		=> '', //銀行提供
		'merchant_id' 		=> '990231456', //銀行分配
		'terminal_id' 		=> '80100844', //銀行分配
		'storeId'			=> '',
		'storeName'			=> '',
		'merchantTid'		=> '',
		
		// 'gateway' 			=> 'http://192.168.1.40:9090/api/v1',
		'gateway' 			=> 'http://127.0.0.1:9090/api/v1',
	],
	'openWallet' => [
		'merchantKey' 		=> '', //銀行提供
		'corporateId' 		=> '', //銀行提供
		'merchant_id' 		=> '990231454', //銀行分配
		'terminal_id' 		=> '80100843', //銀行分配
		'storeId'			=> '',
		'storeName'			=> '',
		'merchantTid'		=> '',
		'walletId'			=> 'OP000035',
		'walletId_subscription'	=> 'OP000036',
		
		// 'gateway' 			=> 'http://192.168.1.40:9090/api/v1',
		'gateway' 			=> 'http://127.0.0.1:9090/api/v1',
	]
];