<?php
if (isset($_SERVER['REQUEST_METHOD'])) {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		exit("POST method required.");
	}
}
//-----------------------------
// Sample for creating payment
//-----------------------------
## Include bootstrap.php
include_once(dirname(dirname(__FILE__)) . '/bootstrap.php');
#############################################################
parse_str(file_get_contents("php://input"), $post_fields);
$post_fields = json_decode($post_fields['post_fields'], true);


extract($post_fields);
//----------------------------------------
## Create @transaction_log
$transaction_log = array();
$transaction_log['account'] = (isset($input_user['account']) ? $input_user['account'] : '');
$transaction_log['account'] = (is_string($transaction_log['account']) ? substr($transaction_log['account'], 0, 16) : '');
$transaction_log['email'] = (isset($input_user['email']) ? $input_user['email'] : '');
$transaction_log['redirect'] = array(
	'return_url'				=> (isset($input_redirect['return_url']) ? $input_redirect['return_url'] : ''),
	'cancel_url'				=> (isset($input_redirect['cancel_url']) ? $input_redirect['cancel_url'] : ''),
);
$transaction_log['user'] = array(
	'billing_address'			=> array(
		'forename'					=> (isset($input_user['forename']) ? $input_user['forename'] : ''),
		'surname'					=> (isset($input_user['surname']) ? $input_user['surname'] : ''),
		'email'						=> (isset($input_user['email']) ? $input_user['email'] : 'user@your-email-host.tld'),
		'phone'						=> (isset($input_user['phone']) ? $input_user['phone'] : ''),
		'line1'						=> (isset($input_user['line1']) ? $input_user['line1'] : ''),
		'line2'						=> (isset($input_user['line2']) ? $input_user['line2'] : ''),
		'city_district'				=> (isset($input_user['city']) ? $input_user['city'] : ''),
		'state_province'			=> (isset($input_user['province']) ? $input_user['province'] : ''),
		'country'					=> (isset($input_user['country']) ? $input_user['country'] : 'Thailand'),
		'postal_code'				=> (isset($input_user['zipcode']) ? $input_user['zipcode'] : ''),
	),
);
$transaction_log['payment'] = array(
	'request_id'					=> $input_payment['request_id'],
	'currency'						=> (isset($input_payment['currency']) ? (is_string($input_payment['currency']) ? strtoupper($input_payment['currency']) : 'THB') : 'THB'),
	'items'							=> array(),
	'amount'						=> 0,
);
if (isset($input_payment['items'])) {
	if (is_array($input_payment['items']) && (count($input_payment['items']) > 0)) {
		$for_i = 0;
		foreach ($input_payment['items'] as $val) {
			$transaction_log['payment']['items'][$for_i] = array();
			$transaction_log['payment']['items'][$for_i]['service'] = $service_data;
			$transaction_log['payment']['items'][$for_i]['product_id'] = (isset($val['product_id']) ? $val['product_id'] : '');
			$transaction_log['payment']['items'][$for_i]['price'] = (isset($val['price']) ? $val['price'] : 0);
			$transaction_log['payment']['items'][$for_i]['details'] = (isset($val['details']) ? $val['details'] : '');
			$transaction_log['payment']['items'][$for_i]['reference'] = (isset($val['reference']) ? $val['reference'] : array());
			$transaction_log['payment']['amount'] += (isset($val['price']) ? $val['price'] : 0);
			$for_i++;
		}
	}
}
# Set payer-id and payer-email
$TMN_Wallet->set_payment_tmn('account', $transaction_log['account']);
$TMN_Wallet->set_payment_tmn('email', $transaction_log['email']);
# payment_structure: create - redirect: verify
$TMN_Wallet->set_redirect('verify');
if (isset($TMN_Wallet->payment_structure['redirect_urls'])) {
	if (count($TMN_Wallet->payment_structure['redirect_urls']) > 0) {
		foreach ($TMN_Wallet->payment_structure['redirect_urls'] as $key => &$val) {
			if (strtolower($key) === strtolower('return_url')) {
				$val = str_replace('##transaction##', 'accept', $val);
			} else if (strtolower($key) === strtolower('cancel_url')) {
				$val = str_replace('##transaction##', 'cancel', $val);
			} else {
				$val = str_replace('##transaction##', 'unknown', $val);
			}
		}
	}
}
####### Make payment structure
try {
	$TMN_Wallet->payment_structure_create($transaction_log['payment'], $transaction_log['user'], 'tmn_wallet');
} catch (Exception $ex) {
	throw $ex;
	exit;
}
if (isset($TMN_Wallet->payment_structure['sha256']) && (isset($TMN_Wallet->payment_structure['signature_string'])) && (isset($TMN_Wallet->payment_structure['signature']))) {
	unset($TMN_Wallet->payment_structure['sha256']);
	unset($TMN_Wallet->payment_structure['signature_string']);
}
##### Doing payment-create Request
try {
	$transaction_payment_structure = json_decode(json_encode($TMN_Wallet->payment_structure, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), true);
	$transaction_payment_request = $TMN_Wallet->payment_request_create($transaction_payment_structure);
} catch (Exception $ex) {
	$transaction_payment_request = $ex->getMessage();
}

##### get body of payment-request
//-------------------------------
$RequestApi = array();
$error = FALSE;
if (!$error) {
	$RequestApi['request'] = array(
		'method'	=> (isset($transaction_payment_request['request']['method']) ? $transaction_payment_request['request']['method'] : ''),
		'header'	=> (isset($transaction_payment_request['request']['header']) ? $transaction_payment_request['request']['header'] : array()),
		'body'		=> (isset($transaction_payment_request['request']['body']) ? $transaction_payment_request['request']['body'] : NULL),
	);
	try {
		$RequestApi['request']['body'] = json_decode($RequestApi['request']['body'], true);
	} catch (Exception $ex) {
		$error = true;
		throw $ex;
	}
}
if (!$error) {
	$RequestApi['response'] = array(
		'code'		=> (isset($transaction_payment_request['response']['code']) ? $transaction_payment_request['response']['code'] : 0),
		'header'	=> (isset($transaction_payment_request['response']['header']['content']) ? $transaction_payment_request['response']['header']['content'] : array()),
		'body'		=> (isset($transaction_payment_request['response']['body']) ? $transaction_payment_request['response']['body'] : NULL),
	);
	try {
		$RequestApi['response']['body'] = json_decode($RequestApi['response']['body'], true);
	} catch (Exception $ex) {
		$error = true;
		throw $ex;
	}
}
if (!$error) {
	if (isset($RequestApi['response']['body']['result']['response_code']) && (isset($RequestApi['response']['body']['result']['developer_message'])) && (isset($RequestApi['response']['body']['result']['user_message']))) {
		### Set transaction-payment-identificator
		$input_params['transaction_payment_identificator'] = (isset($RequestApi['response']['body']['payment_id']) ? $RequestApi['response']['body']['payment_id'] : '');
		$input_params['transaction_id'] = (isset($RequestApi['request']['body']['request_id']) ? $RequestApi['request']['body']['request_id'] : '');
		try {
			$transaction_redirect = $TMN_Wallet->payment_structure_redirect($input_params);
		} catch (Exception $ex) {
			$error = true;
			$transaction_redirect = $ex->getMessage();
		}
		
	}
}
//--------------------------------------------------------------
// Show HTML Form Auto-Submit
//--------------------------------------------------------------
if (!$error) {
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
		<meta name="robots" content="noindex" />
		<title>Loading....</title>
		<!--
		<link rel="stylesheet" href="/assets/stylesheets/cingular.css" type="text/css" />
		-->
	</head>
	<body class="popup" onload="javascript:countRedirect();">
		<div class="container">	
			<h4 class="waiting-text">
				Loading ... (<span id="countdown">5</span>), Please wait, we are validating your payment request.
			</h4>
		</div>
		<div class="waiting-text">
			<form name="formRedirect" id="formRedirect" action="<?=$TMN_Wallet->payment_structure['redirect'];?>" method="post" enctype="application/x-www-form-urlencoded">
				<input type="hidden" name="signature" id="signature" value="<?=$TMN_Wallet->payment_structure['signature'];?>" />
			</form>
		</div>
		<script type="text/javascript">
			function onSubmitToPayment() {
				document.getElementById("formRedirect").submit();
			}
			var count = document.getElementById('countdown').innerHTML;
			count = parseInt(count);
			function countRedirect() {
				if (count < 1) {
					onSubmitToPayment();
				} else {
					count--;
					document.getElementById("countdown").innerHTML = count;
					setTimeout("countRedirect()", 1000);
				}
			}
		</script>
	</body>
	</html>
	<?php
} else {
	echo "Something went wrong...";
	
}





