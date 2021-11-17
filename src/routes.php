<?php
use Illuminate\Http\Request;

Route::get('CathaybkApi/{method}', 'Cathaybk\Api\Controllers\CathaybkAPiController');
Route::post('Cathaybk_callback', 'Cathaybk\Api\Controllers\CathaybkAPiController@callback')->name('Cathaybk_callback');

Route::get('OpenWallet/{method}', 'Cathaybk\Api\Controllers\OpenWalletController');
Route::post('OpenWallet_callback', 'Cathaybk\Api\Controllers\OpenWalletController@callback')->name('OpenWallet_callback');

