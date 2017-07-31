<?php
namespace TrueMoneyWallet\Create;
class Items {
	function set_item_id($item_id) {
		$this->item_id = $item_id;
		return $this;
    }
    function set_service($service) {
		$this->service = $service;
		return $this;
    }
    function set_product_id($product_id) {
		$this->product_id = $product_id;
		return $this;
    }
    function set_price($price) {
		$price = sprintf("%.2f", floatval($price));
		$this->price = $price;
		return $this;
    }
	function set_shop_code($shop_code) {
		$this->shop_code = $shop_code;
		return $this;
	}
    function set_details($details) {
		$this->details = $details;
		return $this;
    }
	function set_detail($detail) {
		$this->detail = $detail;
		return $this;
    }
    function set_reference($reference = array()) {
		$ref = array();
		$ref['ref1'] = (isset($reference['ref1']) ? $reference['ref1'] : '');
		$ref['ref2'] = (isset($reference['ref2']) ? $reference['ref2'] : '');
		$ref['ref3'] = (isset($reference['ref3']) ? $reference['ref3'] : '');
		$this->reference = $ref;
		return $this;
    }
	function set_ref($refs) {
		if (is_array($refs) && (count($refs) > 0)) {
			foreach ($refs as $ke => $val) {
				$this->$ke = $val;
			}
		}
		unset($this->reference);
		return $this;
	}
}