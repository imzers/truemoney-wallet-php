<?php
//-----------------------------
// Sample for creating payment
//-----------------------------
## Include config.php from doc-root
include_once(dirname(dirname(__FILE__)) . '/config.php');
#########################################################






## Create Unique request_id for Transaction (Should be product_id from Merchant Shop)
$DateObject = new TrueMoneyWallet\Utils\Datezone("Asia/Bangkok");
$request_id = $DateObject->create_datetime_format("YmdHisu");

# Build user-consent Data
// ### Get User Consent URL
// The clientId is stored in the bootstrap file
// Get Authorization URL returns the redirect URL that could be used to get user's consent
$QueryRedirect = "https://your-hostname.tld/uri/to/redirect/page";
$QueryPayment = array(
	'request_id' => $request_id, /// Create unique request_id
	'currency' => 'THB', // Always use THB Currency
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
	'details' => "TrueMoney Wallet Item1 with 5 THB",
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
		'email'				=> "email-address@your-hostname.tld",
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
	'merchant_service'		=> 'myarena',
	'redirect'			=> $QueryRedirect,
	'payment'			=> $QueryPayment,
	'user'				=> $QueryUser,
);

//--------
// Make payment-structure
//--------
try {
	$TMN_Wallet->payment_structure_create($post_fields['payment'], $post_fields['user'], 'tmn_wallet');
} catch (Exception $ex) {
	throw $ex;
}





