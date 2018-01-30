<?php
//-----------------------------
// Sample for creating payment
//-----------------------------


## Include bootstrap.php
include_once(dirname(dirname(__FILE__)) . '/bootstrap.php');
#############################################################






## Create Unique request_id for Transaction (Should be product_id from Merchant Shop)
$DateObject = new TrueMoneyWallet\Utils\Datezone("Asia/Bangkok");
$request_id = $DateObject->create_datetime_format("YmdHisu");

# Build user-consent Data
// ### Get User Consent URL
// The clientId is stored in the bootstrap file
// Get Authorization URL returns the redirect URL that could be used to get user's consent
$QueryRedirect = "http://your-hostname.tld/uri/to/redirect/page";
$QueryPayment = array(
	'request_id' 	=> $request_id, /// Create unique request_id
	'currency' 		=> 'THB', // Always use THB Currency
);
# Temporary for fees items
###########################################################
$QueryPayment['items'] = Array();
$item1 = array(
	'product_id' => 1001, // This is just testing product-id
	'price' => "1.00", // Always using 2 decimal, using sprintf("%.2f") of integer value
	'details' => "TrueMoney Wallet Item1 with 1 THB",
	'reference' => array(
		'ref1'		=> "TrueMoney Wallet Item Testing Purpose",
		'ref2'		=> "{$request_id}",
		'ref3'		=> "",
	),
);
array_push($QueryPayment['items'], $item1);
$item2 = array(
	'product_id' => 1090101, // This is just testing product-id
	'price' => "5.00", // Always using 2 decimal, using sprintf("%.2f") of integer value
	'details' => "TrueMoney Wallet Item2 with 5 THB",
	'reference' => array(
		'ref1'		=> "TrueMoney Wallet Item Testing Purpose",
		'ref2'		=> "{$request_id}",
		'ref3'		=> "",
	),
);
array_push($QueryPayment['items'], $item2);
$QueryUser = array (
		'forename'			=> 'FirstName',
		'surname'			=> 'LastName',
		'email'				=> "email-address@your-hostname.com",
		'phone'				=> '0912345678',
		'line1'				=> 'Address Line',
		'line2'				=> 'Patthanakarn Rd.',
		'district'			=> 'Suan Luang',
		'province'			=> 'Bangkok',
		'country'			=> 'Thailand',
		'zipcode'			=> '10250',
);
//------------------
// Build Postfiels
//------------------
$post_fields = array(
	'service_data'				=> 'imzers',
	'input_redirect'			=> $QueryRedirect,
	'input_payment'				=> $QueryPayment,
	'input_user'				=> $QueryUser,
);
$htmlForm = "";
//------------------
// Build Form
//------------------
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
<body class="popup">
	<div class="container">	
		<h4 class="waiting-text">
			Please submit to make a payment test
		</h4>
	</div>
	<div class="waiting-text">
		<form action="02-create-payment-process.php" method="post" enctype="application/x-www-form-urlencoded">
			<textarea name="post_fields" rows="16" cols="80"><?php echo json_encode($post_fields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?></textarea>
			<p>Submit</p>
			<p>
				<input type="submit" value="Submit new payment" />
			</p>
		</form>
	</div>
</body>
</html>


