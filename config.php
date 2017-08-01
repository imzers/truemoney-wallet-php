<?php
// SHow error reporting for debugging purpose
error_reporting(E_ALL);
ini_set('display_startup_errors', true);
ini_set('display_errors', true);

// Include necessary Lib
if (!file_exists('constant.php')) {
  exit("File constant.php is required.");
}
include_once('constant.php');
########################################################################################
# Load TrueMoneyWallet Class
require_once(ConstantConfig::THIS_SERVER_VHOST . "/lib/TMN_Wallet.php");



function getApiContext($mode = 'sandbox') {
	$ClientPoint = Array(
		'sandbox'		=> array(
				'api'			=> 'https://api-payment.tmn-dev.com',
				'api_ip'		=> array(
					'https://52.76.176.237',
					'https://52.74.215.200',
					'https://52.74.69.190',
					'https://54.169.242.66',
				),
				'transfer'		=> 'https://api-payment-transfer.tmn-dev.com',
				'cdn'			=> 'https://cdn.tmn-dev.com',
		),
		'live'			=> array(
				'api'			=> 'https://api-payment.truemoney.com',
				'api_ip'		=> array(
					'https://54.254.160.104',
					'https://54.255.160.127',
				),
				'transfer'		=> 'https://api-payment-transfer.truemoney.com',
				'cdn'			=> 'https://cdn.truemoney.com',
		),
	);
	$ClientConfig = array(
		'sandbox'		=> array(
				'client_id' 		=> 'TMN_CLIENT_ID',
				'client_secret' 	=> 'TMN_CLIENT_SECRET',
				'client_token' 		=> 'TMN_CLIENT_TOKEN',
				'client_shopcode'	=> 'TMN_CLIENT_SHOPCODE',
				'client_appname'	=> 'TMN_CLIENT_APPNAME',
		),
		'live'			=> array(
				'client_id' 		=> 'TMN_CLIENT_ID',
				'client_secret' 	=> 'TMN_CLIENT_SECRET',
				'client_token' 		=> 'TMN_CLIENT_TOKEN',
				'client_shopcode'	=> 'TMN_CLIENT_SHOPCODE',
				'client_appname'	=> 'TMN_CLIENT_APPNAME',
		),
	);
	$Redirect = array(
		'sandbox'		=> array(
			'verify'				=> array(
				'return_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
				'cancel_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
			),
			'payment'				=> array(
				'return_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
				'cancel_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
			),
		),
		'live'			=> array(
			'verify'				=> array(
				'return_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
				'cancel_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
			),
			'payment'				=> array(
				'return_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
				'cancel_url'			=> (ConstantConfig::PUBLIC_URL_PROTOCOL) . '://' . (ConstantConfig::PUBLIC_URL_ADDRESS) . (ConstantConfig::PUBLIC_URL_PATH),
			),
		),
	);
	$Payment = array(
		'sandbox'		=> array(
			'method'				=> 'tmn_wallet',
			'processor'				=> 'TMN',
			'intent'				=> 'sale',
			'type'					=> 'redirect',
			'payer'					=> array(
				'tmn_account'				=> 'default_account',
				'tmn_email'					=> 'default_email@tdp.com',
			),
		),
		'live'			=> array(
			'method'				=> 'tmn_wallet',
			'processor'				=> 'TMN',
			'intent'				=> 'sale',
			'type'					=> 'redirect',
			'payer'					=> array(
				'tmn_account'				=> 'default_account',
				'tmn_email'					=> 'default_email@tdp.com',
			),
		),
	);
	$getApiContext = array(
		'client'		=> $ClientConfig[$mode], 
		'endpoint'		=> $ClientPoint[$mode],
		'redirect'		=> $Redirect[$mode],
		'payment'		=> $Payment[$mode],
	);
	return $getApiContext;
}

# Generate apiContext global variables
$apiContext = getApiContext(ConstantConfig::THIS_SERVER_MODE);
//$apiContext = getApiContext('live');
$TMN_Wallet = new TMN_Wallet($apiContext);
