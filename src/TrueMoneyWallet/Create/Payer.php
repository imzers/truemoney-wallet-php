<?php
namespace TrueMoneyWallet\Create;
class Payer {
	
	function set_payment_method($payment_method = 'tmn_wallet') { // 'tmn_wallet', 'creditcard', etc...
		$this->payment_method = $payment_method;
		return $this;
	}
	function set_payment_processor($payment_processor) { // 'TMN', 'CYBS-BAY', 'KASIKORN', etc...
		$this->payment_processor = $payment_processor;
		return $this;
	}
	function set_payer_info($payer_id, $email) {
		$payer_data = array();
		$payer_data['payer_id'] = (isset($payer_id) ? $payer_id : 1);
		$payer_data['email'] = (isset($email) ? $email : 'pay@himran.com');
		$this->payer_info = $payer_data;
		return $this;
	}
	
}