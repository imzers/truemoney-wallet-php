<?php
namespace TrueMoneyWallet\Api;
use TrueMoneyWallet\Api\Execute;
class Result {
	public $request_header = array();
	public $Execute;
	function create_api_execute($base_url, $path_url) {
		$this->set_method('GET');
		$this->set_url($base_url, $path_url);
		$this->set_post_field(NULL);
		$this->set_post_header($this->request_header);
		$this->set_post_timeout(30);
		$this->Execute = new Execute($this->method, $this->url, $this->post_header, $this->post_field, $this->post_timeout);
		return $this;
	}
	function create_request_header($params = NULL) {
		$this->set_header('Content-Type', 'application/json');
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
	function set_url($url, $url_path = '/payments/v1/payment') {
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
				'Content-type' 	=> 'application/json',
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
		$this->post_field = $post_field;
		return $this;
	}
	function set_post_timeout($post_timeout = 30) {
		$this->post_timeout = $post_timeout;
		return $this;
	}
}