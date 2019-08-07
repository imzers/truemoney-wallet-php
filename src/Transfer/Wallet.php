<?php
namespace TrueMoneyWallet\Transfer;
class Wallet {
	protected $error;
	function __construct() {
		$this->error = false;
	}
	function set_merchant_ref($merchant_ref) {
		$this->merchant_ref = $merchant_ref;
		return $this;
	}
	function set_wallet_account($wallet_account) {
		if (!preg_match('/^0([1-9]+){1}[0-9]{4,15}$/', $wallet_account)) {
			$this->error = true;
		}
		$this->wallet_account = $wallet_account;
		return $this;
	}
	function set_amount($amount) {
		if (!is_numeric($amount)) {
			$this->error = true;
		}
		$amount = round($amount, 2, PHP_ROUND_HALF_ODD);
		$this->amount = $amount;
		return $this;
	}
	function is_ok() {
		if (!$this->error) {
			return $this;
		}
		return FALSE;
	}
}