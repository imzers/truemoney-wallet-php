<?php
use TrueMoneyWallet\Create\Items;
use TrueMoneyWallet\Create\Payer;
use TrueMoneyWallet\Create\BillingAddress;
use TrueMoneyWallet\Create\Redirect;
use TrueMoneyWallet\Create\SHA256;
use TrueMoneyWallet\Create\Signature;
use TrueMoneyWallet\Create\PaymentInfo;
use TrueMoneyWallet\Transfer\Wallet;
use TrueMoneyWallet\Transfer\Transfer;

use TrueMoneyWallet\Api\Request;
use TrueMoneyWallet\Utils\Datezone;

class TMN_Wallet {
	protected $endpoint;
	private $config;
	public $redirect;
	private $payment;
	
	public $payment_structure;
	
	function __construct($context = array()) {
		$this->endpoint = (isset($context['endpoint']) ? $context['endpoint'] : array());
		$this->config = (isset($context['client']) ? $context['client'] : array());
		$this->redirect = (isset($context['redirect']) ? $context['redirect'] : array());
		$this->payment = (isset($context['payment']) ? $context['payment'] : array());
	}
	function set_payment_url_path($urlpath) {
		$this->payment_url_path = $urlpath;
		return $this;
	}
	function set_redirect($redirect_type = 'verify') {
		$Redirect = new Redirect();
		$Redirect->set_return_url(isset($this->redirect[$redirect_type]['return_url']) ? $this->redirect[$redirect_type]['return_url'] : "");
		$Redirect->set_cancel_url(isset($this->redirect[$redirect_type]['cancel_url']) ? $this->redirect[$redirect_type]['cancel_url'] : "");
		$this->payment_structure['redirect_urls'] = $Redirect->get_redirect_url();
	}
	function set_sha256($data, $uppercase = TRUE) {
		$SHA256 = new SHA256();
		$SHA256->set_uppercase($uppercase);
		$SHA256->set_data($data);
		$SHA256->set_secret($this->config['client_secret']);
		$this->payment_structure['sha256'] = $SHA256->create_sha256();
		$this->set_signature_string($data);
	}
	function set_signature_string($data) {
		$this->payment_structure['signature_string'] = $data;
	}
	function payment_structure_create($params, $userinput, $payment_instrument = 'tmn_wallet') {
		$error = true;
		if ((!is_array($params)) || (!is_array($userinput))) {
			return false;
		}
		$Checkparams = array('request_id','currency','items');
		$Checkuserinput = array('billing_address');
		if (0 === count(array_diff($Checkparams, array_keys($params)))) {
			$error = false;
		}
		if (0 === count(array_diff($Checkuserinput, array_keys($userinput)))) {
			$error = false;
		}
		$structure = array();
		if (!$error) {
			$Datezone = new Datezone("Asia/Bangkok");
			$this->payment_structure['app_id'] = $this->config['client_id'];
			$this->payment_structure['request_id'] = (isset($params['request_id']) ? $params['request_id'] : $Datezone->create_datetime_format("YmdHisu"));
			$this->payment_structure['merchant_request_id'] = (isset($params['request_id_transaction']) ? $params['request_id_transaction'] : time());
			$this->payment_structure['intent'] = $this->payment['intent'];
			$this->payment_structure['payment_type'] = $this->payment['type'];
			$Payer = new Payer();
			$Payer->set_payment_method($this->payment['method']);
			$Payer->set_payment_processor($this->payment['processor']);
			$this->payment['tmn_account'] = $this->get_payment_tmn('account');
			$this->payment['tmn_email'] = $this->get_payment_tmn('email');
			$Payer->set_payer_info($this->get_payment_tmn('account'), $this->get_payment_tmn('email'));
			$this->payment_structure['payer'] = $Payer;

			$PaymentInfo = new PaymentInfo();
			$item_id = 1;
			foreach ($params['items'] as $keval) {
				$Items = new Items();
				$Items->set_item_id($item_id);
				$Items->set_service($keval['service']);
				$Items->set_product_id($keval['product_id']);
				$Items->set_shop_code($this->config['client_shopcode']);
				$Items->set_price(floatval($keval['price']));
				$Items->set_detail($keval['details']);
				$Items->set_reference($keval['reference']);
				$Items->set_ref($Items->reference);
				$PaymentInfo->add_items($Items);
				$item_id += 1;
			}
			$PaymentInfo->set_currency($params['currency']);
			$this->payment_structure['payment_info'] = $PaymentInfo;
			
			$BillingAddress = new BillingAddress();
			$BillingAddress->set_forename(isset($userinput['billing_address']['forename']) ? $userinput['billing_address']['forename'] : '');
			$BillingAddress->set_surname(isset($userinput['billing_address']['surname']) ? $userinput['billing_address']['surname'] : '');
			$BillingAddress->set_email(isset($userinput['billing_address']['email']) ? $userinput['billing_address']['email'] : '');
			$BillingAddress->set_phone(isset($userinput['billing_address']['phone']) ? $userinput['billing_address']['phone'] : '');
			$BillingAddress->set_line1(isset($userinput['billing_address']['line1']) ? $userinput['billing_address']['line1'] : '');
			$BillingAddress->set_line2(isset($userinput['billing_address']['line2']) ? $userinput['billing_address']['line2'] : '');
			$BillingAddress->set_city_district(isset($userinput['billing_address']['city_district']) ? $userinput['billing_address']['city_district'] : '');
			$BillingAddress->set_state_province(isset($userinput['billing_address']['state_province']) ? $userinput['billing_address']['state_province'] : '');
			$BillingAddress->set_country(isset($userinput['billing_address']['country']) ? $userinput['billing_address']['country'] : '');
			$BillingAddress->set_postal_code(isset($userinput['billing_address']['postal_code']) ? $userinput['billing_address']['postal_code'] : '');
			$this->payment_structure['billing_address'] = $BillingAddress;
			
			/*
			switch ($payment_instrument) {
				case 'creditcard':
					$structure['payer'] = array_merge(array('funding_instrument' => array('one_time_card_token' => $this->config['client_secret'])), $structure['payer']);
					$structure['payer']['payment_method'] = 'creditcard';
					$structure['payment_type'] = 'api';
					$structure['payer_authentication'] = true;
				break;
				case 'tmn_wallet':
				default:
					$structure['payer']['payment_method'] = 'tmn_wallet';
				break;
			}
			*/
			$Signature = new Signature();
			$Signature->create_signature($this->config['client_id'], $this->payment_structure['request_id'], $PaymentInfo->item_list['items']);
			$this->set_sha256($Signature->get_signature(), TRUE);
			$this->payment_structure['signature'] = base64_encode($this->payment_structure['sha256']);
		}
	}
	function payment_request_create($params) {
		$Request = new TrueMoneyWallet\Api\Request();
		$Request->create_request_header(array('Authorization' => "Bearer {$this->config['client_token']}"));
		$Request->create_api_execute($this->endpoint['api'], '/payments/v1/payment', $params);
		$payment = $Request->Execute->make_curl();
		if (!$payment) {
			return false;
		}
		return $payment;
	}
	function payment_structure_redirect($input_params) {
		$params = array(
			'payment_id' => (isset($input_params['transaction_payment_identificator']) ? $input_params['transaction_payment_identificator'] : ''),
			'request_id' => (isset($input_params['transaction_id']) ? $input_params['transaction_id'] : ''),
		);
		$data = trim("{$params['payment_id']}{$params['request_id']}");
		$this->set_sha256($data, TRUE);
		$this->payment_structure['signature'] = base64_encode($this->payment_structure['sha256']);
		$this->payment_structure['redirect'] = "{$this->endpoint['api']}/payments/v1/payment/{$params['payment_id']}/process";
		//$this->payment_structure['redirect'] = "{$this->endpoint['api_ip'][0]}/payments/v1/payment/{$params['payment_id']}/process";
		return $this->payment_structure;
	}
	function payment_structure_result($input_params) {
		$params = array(
			'payment_id' => (isset($input_params['transaction_payment_identificator']) ? $input_params['transaction_payment_identificator'] : ''),
			'request_id' => (isset($input_params['transaction_id']) ? $input_params['transaction_id'] : ''),
		);
		$url_path = "/payments/v1/payment/{$params['payment_id']}";
		$this->set_payment_url_path("/payments/v1/payment/{$params['payment_id']}");
	}
	function get_payment_structure_result() {
		return $this->payment_url_path;
	}
	function payment_request_result($url_path = null) {
		if (!isset($url_path)) {
			$url_path = (isset($this->payment_url_path) ? $this->payment_url_path : '');
		}
		$Result = new TrueMoneyWallet\Api\Result();
		$Result->create_request_header(array('Authorization' => "Bearer {$this->config['client_token']}"));
		$Result->create_api_execute($this->endpoint['api'], $url_path);
		$payment = $Result->Execute->make_curl();
		if (!$payment) {
			return false;
		}
		return $payment;
	}
	#
	function payment_structure_transfer($input_params) {
		$params = array(
			'merchant_ref'		=> (isset($input_params['merchant_ref']) ? $input_params['merchant_ref'] : ''),
			'wallet_account'	=> (isset($input_params['wallet_account']) ? $input_params['wallet_account'] : ''),
			'amount'			=> (isset($input_params['amount']) ? $input_params['amount'] : 0),
		);
		$TransferWallet = new TrueMoneyWallet\Transfer\Wallet();
		$TransferWallet->set_merchant_ref($params['merchant_ref']);
		$TransferWallet->set_wallet_account($params['wallet_account']);
		$TransferWallet->set_amount($params['amount']);
		if (!$TransferWallet->is_ok()) {
			return false;
		} else {
			$this->set_payment_url_path("/transfers");
			$this->payment_structure['transfer'] = $TransferWallet;
			return $this;
		}
	}
	function get_payment_structure_transfer() {
		return $this->payment_structure;
	}
	function payment_request_transfer($url_path = null) {
		if (!isset($url_path)) {
			$url_path = (isset($this->payment_url_path) ? $this->payment_url_path : '');
		}
		$post_field = $this->get_payment_structure_transfer();
		if (isset($post_field['transfer'])) {
			try {
				$Transfer = new TrueMoneyWallet\Transfer\Transfer();
				$Transfer->create_request_header(array('Authorization' => "Bearer {$this->config['client_token']}"));
				$Transfer->create_api_execute($this->endpoint['transfer'], $url_path, $post_field['transfer']);
				$payment = $Transfer->Execute->make_curl();
			} catch (Exception $ex) {
				$payment = $ex->getMessage();
				throw $ex;
			}
			return $payment;
		}
		return false;
	}
	#
	function form_structure($json) {
		$form_structure = new TrueMoneyWallet\Api\Form($json, $this->config['client_secret'], $this->endpoint['api']);
		return $form_structure;
	}
	
	
	
	####
	public function set_payment_tmn($type, $value) {
		$type = (is_string($type) ? strtolower($type) : 'email');
		switch ($type) {
			case 'account':
				$this->payment['tmn_account'] = $value;
			break;
			case 'email':
			default:
				$this->payment['tmn_email'] = $value;
			break;
		}
		return $this;
	}
	private function get_payment_tmn($type = 'email') {
		switch (strtolower($type)) {
			case 'account':
				$payment_tmn = $this->payment['tmn_account'];
			break;
			case 'email':
			default:
				$payment_tmn = $this->payment['tmn_email'];
			break;
		}
		return $payment_tmn;
	}
	#
	private function create_curl_headers($params = null) {
		$headers = array();
		if (!isset($params)) {
			$params['content-type'] = 'application/x-www-form-urlencoded';
		}
		if (is_array($params) && (count($params) > 0)) {
			foreach ($params as $key => $val) {
				$headers[] = "{$key}:{$val}";
			}
		}
		return $headers;
	}
	private function getHeaders() {
		$out = array();
		foreach($_SERVER as $key => $value) {
			if ((substr($key,0,5) == "HTTP_") && (isset($value))) {
				$key = str_replace(" ","-", ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
				$out[$key] = $value;
				}
			}
		return $out;
	}
	private function createHeaders($headers = array()) {
		$curlheaders = array();
		foreach ($headers as $ke => $val) {
			$curlheaders[] = "{$ke}: {$val}";
		}
		return $curlheaders;
	}
	
}	