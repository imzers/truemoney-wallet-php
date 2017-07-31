<?php

namespace TrueMoneyWallet\Create;

class BillingAddress {
    
	
	function set_forename($forename) {
		$this->forename = $forename;
		return $this;
	}
	function set_surname($surname) {
		$this->surname = $surname;
		return $this;
	}
	function set_email($email) {
		$this->email = $email;
		return $this;
	}
	function set_phone($phone) {
		$this->phone = $phone;
		return $this;
	}
	function set_line1($line1) {
		$this->line1 = $line1;
		return $this;
	}
	function set_line2($line2) {
		$this->line2 = $line2;
		return $this;
	}
	function set_city_district($city_district) {
		$this->city_district = $city_district;
		return $this;
	}
	function set_state_province($state_province) {
		$this->state_province = $state_province;
		return $this;
	}
	function set_country($country) {
		$this->country = $country;
		return $this;
	}
	function set_postal_code($postal_code) {
		$this->postal_code = $postal_code;
		return $this;
	}
}