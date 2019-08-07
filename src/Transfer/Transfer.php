<?php
namespace TrueMoneyWallet\Transfer;
use TrueMoneyWallet\Transfer\Wallet;
use TrueMoneyWallet\Api\Execute;
class Transfer {
	public $request_header = array();
	public $Execute;
	function create_api_execute($base_url, $path_url, $post_field) {
		$this->set_method('POST');
		$this->set_url($base_url, $path_url);
		$this->set_post_field($post_field);
		$this->set_post_header($this->request_header);
		$this->set_post_timeout(30);
		$this->Execute = new Execute($this->method, $this->url, $this->post_header, $this->post_field, $this->post_timeout);
		return $this;
	}
	function create_request_header($params = NULL) {
		$this->set_header('Content-Type', 'application/x-www-form-urlencoded');
		if (isset($params)) {
			if ((is_array($params)) && (count($params) > 0)) {
				foreach ($params as $key => $val) {
					$this->set_header($key, $val);
				}
			}
			
		}
	}
	function set_header($key, $val) {
		$this->request_header[$key] = $val;
		return $this;
	}
	function set_method($method) {
		$this->method = $method;
		return $this;
	}
	function set_url($url, $url_path = '/transfers') {
		if (substr($url_path, 0, strlen('/')) === '/') {
			$this->url = "{$url}{$url_path}";
		} else {
			$this->url = "{$url}/{$url_path}";
		}
		return $this;
	}
	function set_post_header($headers = null) {
		if (!isset($headers)) {
			$this->post_header = array(
				'Content-type' 	=> 'application/x-www-form-urlencoded',
				'Accept'		=> 'application/json',
			);
		}
		if (is_array($headers) && (count($headers) > 0)) {
			foreach ($headers as $key => $val) {
				$this->post_header[$key] = $val;
			}
		}
		return $this;
	}
	function set_post_field($post_field) {
		$this->post_field['wallet_account'] = (isset($post_field->wallet_account) ? $post_field->wallet_account : '');
		$this->post_field['amount'] = (isset($post_field->amount) ? $post_field->amount : 0);
		$this->post_field['amount'] = (is_numeric($this->post_field['amount']) ? round($this->post_field['amount'], 2, PHP_ROUND_HALF_ODD) : 0);
		$this->post_field['amount'] = number_format($this->post_field['amount'], 2);
		$this->post_field['merchant_ref'] = (isset($post_field->merchant_ref) ? $post_field->merchant_ref : '');
		return $this;
	}
	function set_post_timeout($post_timeout = 30) {
		$this->post_timeout = $post_timeout;
		return $this;
	}
	
}