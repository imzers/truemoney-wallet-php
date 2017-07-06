<?php
use TrueMoneyWallet\Create\Items;
use TrueMoneyWallet\Create\Payer;
use TrueMoneyWallet\Create\BillingAddress;
use TrueMoneyWallet\Create\Redirect;
use TrueMoneyWallet\Create\SHA256;
use TrueMoneyWallet\Create\Signature;
use TrueMoneyWallet\Create\PaymentInfo;

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
	function create_payment_structure($params, $userinput, $payment_instrument = 'tmn_wallet') {
		$error = true;
		if ((!is_array($params)) || (!is_array($userinput))) {
			return false;
		}
		$Checkparams = array('request_id','currency','items');
		$Checkuserinput = array('tmn_account','$userinput','billing_address');
		if (0 === count(array_diff($Checkparams, array_keys($params)))) {
			$error = false;
		}
		if (0 === count(array_diff($Checkuserinput, array_keys($userinput)))) {
			$error = false;
		}
		$structure = array();
		if (!$error) {
			$Payer = new Payer();
			$Payer->set_payment_method($this->payment['method']);
			$Payer->set_payment_processor($this->payment['processor']);
			$Payer->set_payer_info($userinput['tmn_account'], $userinput['tmn_email']);
			$this->set_redirect('verify');
			$this->payment_structure['app_id'] = $this->config['client_id'];
			$this->payment_structure['request_id'] = $params['request_id'];
			$this->payment_structure['intent'] = $this->payment['intent'];
			$this->payment_structure['payment_type'] = $this->payment['type'];
			$this->payment_structure['payer'] = $Payer;

			$PaymentInfo = new PaymentInfo();
			foreach ($params['items'] as $keval) {
				$Items = new Items();
				$Items->set_item_id($keval['item_id']);
				$Items->set_service($keval['service']);
				$Items->set_product_id($keval['product_id']);
				$Items->set_shop_code($this->config['client_shopcode']);
				$Items->set_price(floatval($keval['price']));
				$Items->set_details($keval['details']);
				$Items->set_reference($keval['reference']);
				$Items->set_ref($Items->reference);
				$PaymentInfo->add_items($Items);
			}
			$PaymentInfo->set_currency($params['currency']);
			$this->payment_structure['payment_info'] = $PaymentInfo;
			
			$BillingAddress = new BillingAddress();
			$BillingAddress->set_forename(isset($userinput['billing_address']['forename']) ? $userinput['billing_address']['forename'] : '');
			$BillingAddress->set_surname(isset($userinput['billing_address']['surename']) ? $userinput['billing_address']['surename'] : '');
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
			$Signature->create_signature($this->config['client_id'], $params['request_id'], $PaymentInfo->item_list['items']);
			$this->set_sha256($Signature->get_signature(), TRUE);
			$this->payment_structure['signature'] = base64_encode($this->payment_structure['sha256']);
		}
	}
  
  
  }
