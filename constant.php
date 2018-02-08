<?php
class ConstantConfig {
	static protected $_root = null;
	static public $THIS_SERVER_MODE = 'sandbox'; // 'sandbox' || 'live'
	static public function root() {
		if (is_null(self::$_root)) {
			self::$_root = dirname(__FILE__);
		}
		return self::$_root;
	}
	public function get_baseurl(&$base_url) {
		$base_url = '';
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
			if ( $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
				$_SERVER['HTTPS']       = 'on';
				$_SERVER['SERVER_PORT'] = 443;
			}
		}
		if (isset($_SERVER['HTTPS'])) {
			$base_url = (($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http');
		} else {
			$base_url = (isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : 'http');
			$base_url = ((strtolower(substr($base_url, 0, 5)) =='https') ? 'https://': 'http://');
		}
		if (isset($_SERVER['HTTP_HOST'])) {
			$base_url .= $_SERVER['HTTP_HOST'];
		} else {
			$base_url .= (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
		}
		if (isset($_SERVER['SCRIPT_NAME'])) {
			$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
		}
		return $base_url;
	}
	const PUBLIC_URL_PROTOCOL			= 'https';
	const PUBLIC_URL_ADDRESS			= 'payment.your-domain.tld'; // Change domain of live or sandbox
	const PUBLIC_URL_PATH				= '/payment/##method##/##transaction##';
	## Another constant you can put below:
	
	
}




