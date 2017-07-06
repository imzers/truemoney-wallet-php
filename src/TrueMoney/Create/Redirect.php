<?php
namespace TrueMoneyWallet\Create;
class Redirect {
	
	function set_return_url($return_url) {
		$this->return_url = $return_url;
		return $this;
	}
	function set_cancel_url($cancel_url) {
		$this->cancel_url = $cancel_url;
		return $this;
	}
	
	function get_redirect_url() {
		return array(
			'return_url'		=> $this->return_url,
			'cancel_url'		=> $this->cancel_url,
		);
	}
}