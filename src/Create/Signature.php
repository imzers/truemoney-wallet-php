<?php
namespace TrueMoneyWallet\Create;
class Signature {
	public $signature;
	
	function create_signature($appid, $request_id, $params) {
		$signText = "";
		$signText .= "{$appid}{$request_id}";
		if (isset($params)) {
			if (count($params) > 0) {
				foreach ($params as $val) {
					$signText .= (isset($val->shop_code) ? $val->shop_code : "");
					$signText .= (isset($val->price) ? $val->price : "");
				}
			}
		}
		$this->signature = $signText;
	}
	function get_signature() {
		return $this->signature;
	}
	function check_signature($params) {
		$signText = "";
		if (count($params) > 0) {
			foreach ($params as $val) {
				$signText .= (isset($val->shop_code) ? $val->shop_code : "");
				$signText .= (isset($val->price) ? $val->price : "");
			}
		}
		return $signText;
	}
}