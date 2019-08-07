<?php
namespace TrueMoneyWallet\Api;
use TrueMoneyWallet\Create\SHA256;
class Form {
	protected $params;
	protected $payment_id;
	protected $request_id;
	private $signature_secret;
	private $signature_url;
	function __construct($json_object, $signature_secret, $signature_url) {
		try {
			$this->params = json_decode($json_object, true);
			$this->payment_id = (isset($this->params['payment_id']) ? $this->params['payment_id'] : '');
			$this->request_id = (isset($this->params['request_id']) ? $this->params['request_id'] : '');
			$this->signature_secret = $signature_secret;
			$this->signature_url = $signature_url;
		} catch (Exception $ex) {
			$this->params = $ex->getMessage();
			$this->payment_id = FALSE;
			$this->request_id = FALSE;
		}
		$this->set_signature_string($this->payment_id, $this->request_id);
		$this->create_form();
	}
	function create_signature_string($params = array()) {
		if (!isset($params)) {
			return FALSE;
		}
		$this->payment_id = (isset($params['payment_id']) ? $params['payment_id'] : '');
		$this->request_id = (isset($params['request_id']) ? $params['request_id'] : '');
		$this->set_signature_string($this->payment_id, $this->request_id);
		return $this;
	}
	function set_signature_string($payment_id, $request_id) {
		$this->payment_id = trim($payment_id);
		$this->request_id = trim($request_id);
		$this->signature_string = ("{$payment_id}{$request_id}");
		return $this;
	}
	function get_signature_string() {
		return $this->signature_string;
	}
	function create_form() {
		$sha256 = new SHA256();
		$sha256->set_uppercase(TRUE);
		$sha256->set_data($this->get_signature_string());
		$sha256->set_secret($this->signature_secret);
		$sha256_encoded = base64_encode($sha256->create_sha256());
		$form = "<form name=\"formRedirect\" id=\"formRedirect\" action=\"{$this->signature_url}/payments/v1/payment/{$this->payment_id}/process\" method=\"post\">" .
			"<input type=\"text\" name=\"signature\" id=\"signature\" value=\"{$sha256_encoded}\" />" . 
			"<input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />" .
			"</form>";
		$this->form = $form;
		return $this;
	}
	
}