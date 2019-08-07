<?php
namespace TrueMoneyWallet\Create;

class PaymentInfo {
	public $item_list = array();
	
	function __construct() {
		$this->item_list['items'] = array();
	}
	function set_currency($currency) {
		$this->currency = $currency;
		return $this;
	}
	function add_items($items = NULL) {
		if (isset($items)) {
			array_push($this->item_list['items'], $items);
		}
	}
}