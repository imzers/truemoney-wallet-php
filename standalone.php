<?php
$Endpoint = Array(
	'sandbox'		=> array(
			'api'			=> 'https://api-payment.tmn-dev.com',
			'transfer'		=> 'https://api-payment-transfer.tmn-dev.com',
			'cdn'			=> 'https://cdn.tmn-dev.com',
			'api_ip'		=> 'https://54.255.152.85',
			'ip_transfer' 	=> array('https://54.254.214.156', 'https://54.254.251.248'),
	),
	'live'			=> array(
			'api'		=> 'https://api-payment.truemoney.com',
			'transfer'	=> 'https://api-payment-transfer.truemoney.com',
			'cdn'		=> 'https://cdn.truemoney.com',
			'api_ip'	=> 'https://52.74.220.189',
	),
);
$TMConfig = array(
	'client_id' 		=> '#TrueMoney Wallet Merchant# ClienID', // You will get from Truemoney
	'client_secret' 	=> '#TrueMoney Wallet Merchant# ClientSecret', // You will get from Truemoney
	'client_token' 		=> '#TrueMoney Wallet Merchant# Token', // You will get from Truemoney
	'client_shopcode'	=> '#TrueMoney Wallet Merchant# Shop Code', // You will get from Truemoney
	'client_appname'	=> '#TrueMoney Wallet Merchant# App Name', // You will get from Truemoney
);
/*
**********************************************************
Content need to change
*/
$params = array(
	'payment_id' => '9160629143916',
	'request_id' => '36b1a1fc06aee733c2404e6dacd7bb0d51', // or generate random unique?
);
$headers = array(
	'Content-Type' => 'application/json',
	'Authorization' => "Bearer {$TMConfig['client_token']}",
);
/***********************************************************/
$RawBody = generateConcatination(array($params['payment_id'], $params['request_id']));
$SignRawBody = base64_encode(generateSha256($RawBody, $TMConfig['client_secret'], true));
$xhttp_data = "signature={$SignRawBody}";
$xhttp_array = array('signature' => $SignRawBody);
$xhttp_json = json_encode($xhttp_array, JSON_UNESCAPED_SLASHES);
/*
**********************************************************
Content need to change
*/
// CreateStructure
$Queryparams = array(
	'request_id' => md5(date('YmdHis')), /// Create unique request_id
	'currency' => 'THB',
	'item_id' => 1,
	'service' => 'MyArena Account', // or MyArena Services?
	'product_id' => "202138944",
	'price' => "0.80",
	'details' => "MyArena Verifying with 1 THB",
	'return_url' => 'http://localhost/standalone.php?accept',
	'cancel_url' => 'http://localhost/standalone.php?cancel',
);
$Queryinput = array(
	'tmn_account' => "myaccount@payment.myarena.goodgames.net", // Should be an user input tmn account
	'tmn_email' => "myaccount@payment.myarena.goodgames.net", // Should be an user input tmn email or myarena email
	'billing_address'	=> array(					// Input or generate randomly or API to MyArena Account?
		'forename'			=> 'MyArena',
		'surname'			=> 'Goodgames',
		'email'				=> "myaccount@payment.myarena.goodgames.net",
		'phone'				=> '0962898028',
		'line1'				=> '32 True Tower 2, 2nd Floor',
		'line2'				=> 'Patthanakarn Rd.',
		'city_district'		=> 'Suan Luang',
		'state_province'	=> 'Bangkok',
		'country'			=> 'Thailand',
		'postal_code'		=> '10250',
	),
);
$structure = createVerifyStructure($TMConfig, $Queryparams, $Queryinput, 'tmn_wallet');
$JSONstructure = json_encode($structure, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
// Generate Request
$contentReq = fopen_execute('POST', "{$Endpoint['sandbox']['api']}/payments/v1/payment", $JSONstructure, $headers);
if (strtolower($contentReq['remark']) !== strtolower('success')) {
	if (!$Response = json_decode($contentReq['body'], true)) {
		Exit('Error un-expected: malformed json response.');
	}
	if (((int)$Response['result']['response_code'] === 0) && (strtoupper($Response['result']['developer_message']) === strtoupper('SUCCESS'))) {
		$RawBody = generateConcatination(array($Response['payment_id'], $Response['request_id']));
		$SignRawBody = base64_encode(generateSha256($RawBody, $TMConfig['client_secret'], true));
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="utf-8" />
			<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
			<meta name="robots" content="noindex" />
			<title>Secured Payment by truemoney</title>
			<script type="text/javascript">
				function formRedirectNow() {
					var formRedirect = document.getElementById('formRedirect');
					formRedirect.submit();
				}
			</script>
		</head>
		<body onload="javascript:formRedirectNow();">
			<form id="formRedirect" action="<?=$Endpoint['sandbox']['api_ip'];?>/payments/v1/payment/<?=$Response['payment_id'];?>/process" method="post">
				<input type='hidden' name='signature' id='signature' value='<?=$SignRawBody;?>' />
			</form>
		</body>
		</html>
		<?php
		exit;
	} else {
		?>
		<!DOCTYPE html>
		<html lang="en">
		<head>
			<meta charset="utf-8" />
			<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
			<meta name="robots" content="noindex" />
			<title>Secured Payment by truemoney</title>
			<script type="text/javascript">
				function formRedirectNow() {
					var formRedirect = document.getElementById('formRedirect');
					formRedirect.submit();
				}
			</script>
		</head>
		<body>
			<?php
			$printOut = json_decode($contentReq['body'], true);
			print_r($printOut);
			?>
		</body>
		</html>
		<?php
	}
} else {
	echo "<pre>";
	print_r($contentReq);
	exit('<script type="text/javascript">alert(\'Error un-expected: Unknown reponse structure.\');</script>');
}
/***********************************************************/


// Function collections
function generateSha256($data, $secret, $uppercase = true) {
	return hash_hmac('sha256', $data, $secret, $uppercase);
}
function generateConcatination($inputs = array()) {
	if (!is_array($inputs)) {
		return false;
	}
	$returnText = "";
	foreach ($inputs as $val) {
		$returnText .= $val;
	}
	return $returnText;
}
function fopen_context($apiContext, $method, $uri, $params = null, $headers = array(), $handle = null) {
	return fopen_execute($method, "{$apiContext['sandbox']['api_ip']}{$uri}", $params, $headers, $handle);
}
function fopen_execute($method, $uri, $params = null, $headers = array(), $handle = null) {
	$header = "";
	$QueryString = "";
	if (is_array($params)) {
		$QueryString = http_build_query($params);
	} else {
		if (!empty($params) || ($params != '')) {
			if (!json_decode($params, true)) {
				$QueryString = urlencode($params);
			} else {
				$QueryString = $params;
			}
		} else {
			$QueryString = null;
		}
	}
	foreach ($headers as $k => $v) {
		$header .= "{$k}:{$v}\r\n";
	}
	$header .= "Content-Length:" . strlen($QueryString) . "\r\n";
	$opts = array(
		'http'	=> array(
			'method'			=> "{$method}",
			'header'			=> "{$header}",
			'content'			=> "{$QueryString}"
		),
		'ssl'	=> array(
			'verify_peer'		=> false,
			'verify_peer_name'	=> false,
		),
	);
	$context = stream_context_create($opts);
	$content = array('body' => '');
	if (!$handle = fopen("{$uri}", 'r', false, $context)) {
		$content['result'] = false;
		$content['remark'] = "Cannot read url: {$uri}\n";
	} else {
		while (!feof($handle)) {
			$content['result'] = true;
			$content['body'] .= fread($handle, 8192);
			$content['remark'] = "Success\n";
		}
		fclose($handle);
	}
	return $content;
}



function createVerifyStructure($app, $params, $userinput, $payment_instrument = 'tmn_wallet') {
	$error = true;
	if ((!is_array($app)) || (!is_array($params)) || (!is_array($userinput))) {
		return false;
	}
	$Checkparams = array('request_id','currency','item_id','service','product_id','price','details','return_url','cancel_url');
	$Checkuserinput = array('tmn_account','$userinput','billing_address');
	if (0 === count(array_diff($Checkparams, array_keys($params)))) {
		$error = false;
	}
	if (0 === count(array_diff($Checkuserinput, array_keys($userinput)))) {
		$error = false;
	}
	$structure = array();
	if (!$error) {
		$structure = array(
			'app_id'			=> $app['client_id'],
			'request_id'		=> $params['request_id'],
			'intent'			=> 'sale', // 'sale', 'authorization'
			'payment_type'		=> 'redirect', // 'redirect', 'api'
			'payer'				=> array(
				'payment_method'	=> 'tmn_wallet', // 'tmn_wallet', 'creditcard'
				'payer_info'		=> array(
					'payer_id'				=> $userinput['tmn_account'],
					'email'					=> $userinput['tmn_email'],
				),
				'payment_processor'	=> 'TMN', // 'TMN', 'CYBS-BAY', 'KASIKORN', etc...
			),
			'payment_info'		=> array(
				'currency'			=> $params['currency'],
				'item_list'			=> array(
					'items'			=> array(
						array(
						'item_id'				=> $params['item_id'], // List of ItemId
						'shop_code'				=> $app['client_shopcode'],
						'service'				=> $params['service'], // Mobile
						'product_id'			=> $params['product_id'], // CartId
						'price'					=> $params['price'], // Total Price
						'detail'				=> $params['details'], // Item Details
						),
					),
				),
			),
			'redirect_urls'		=> array(
				'return_url'		=> $params['return_url'],
				'cancel_url'		=> $params['cancel_url'],
			),
		);
		$structure['billing_address'] = array();
		if (isset($userinput['billing_address'])) {
			foreach ($userinput['billing_address'] as $k => $v) {
				$structure['billing_address'][$k] = $v;
			}
		}
		switch ($payment_instrument) {
			case 'creditcard':
				$structure['payer'] = array_merge(array('funding_instrument' => array('one_time_card_token' => getTokenFromSecret())), $structure['payer']);
				$structure['payer']['payment_method'] = 'creditcard';
				$structure['payment_type'] = 'api';
				$structure['payer_authentication'] = true;
			break;
			case 'tmn_wallet':
			default:
				$structure['payer']['payment_method'] = 'tmn_wallet';
			break;
		}
		$structure['signature'] = base64_encode(generateSha256(createPaymentSignature($app['client_id'], $params['request_id'], $structure['payment_info']['item_list']['items']), $app['client_secret'], true));
	}
	return $structure;
}
function createPaymentSignature($appid, $request_id, $params = array()) {
	$signText = "";
	$signText .= "{$appid}{$request_id}";
	foreach ($params as $k => $v) {
		if (is_array($v)) {
			$signText .= "{$v['shop_code']}{$v['price']}";
		}
	}
	return $signText;
}