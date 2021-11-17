<?php
//GC_vic:2021-10-28 國泰世華金流config
return [
	'normal' => [
		'merchantKey' 		=> '', //銀行提供
		'corporateId' 		=> '', //銀行提供
		'merchant_id' 		=> '', //銀行分配
		'terminal_id' 		=> '', //銀行分配
		'storeId'			=> '',
		'storeName'			=> '',
		'merchantTid'		=> '',
		
		// 'gateway' 			=> 'http://192.168.1.40:9090/api/v1',
		'gateway' 			=> 'http://127.0.0.1:9090/api/v1',
	],
	'openWallet' => [
		'merchantKey' 		=> '', //銀行提供
		'corporateId' 		=> '', //銀行提供
		'merchant_id' 		=> '', //銀行分配
		'terminal_id' 		=> '', //銀行分配
		'storeId'			=> '',
		'storeName'			=> '',
		'merchantTid'		=> '',
		'walletId'			=> '',
		'walletId_subscription'	=> '',
		
		// 'gateway' 			=> 'http://192.168.1.40:9090/api/v1',
		'gateway' 			=> 'http://127.0.0.1:9090/api/v1',
	]
];
