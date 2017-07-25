<?php
namespace TrueMoneyWallet\Utils;
use \DateTime;
use \DateTimeZone;
class Datezone {
	protected $DateObject;
	
	function __construct($timezone) {
		// $timezone : Asia/Bangkok
		$microtime = microtime(true);
		$micro = sprintf("%06d",($microtime - floor($microtime)) * 1000000);
		$this->DateObject = new DateTime(date('Y-m-d H:i:s.'.$micro, $microtime));
		$this->DateObject->setTimezone(new DateTimeZone($timezone));
	}
	function create_datetime_format($format) {
		// $format : YmdHisu
		return $this->DateObject->format($format);
	}
}
