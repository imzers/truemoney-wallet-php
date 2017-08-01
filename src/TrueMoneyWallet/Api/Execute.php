<?php
namespace TrueMoneyWallet\Api;
class Execute {
	public $headers;
	public $response;
	
	function __construct($post_method, $post_url, $post_header, $post_field, $post_timeout) {
		$this->post_method = $post_method;
		$this->post_url = $post_url;
		$this->post_field = $post_field;
		$this->post_timeout = $post_timeout;
		$this->post_header = $post_header;
		$this->headers = $this->createHeaders($this->post_header);
	}
	function make_curl() {
		$this->response = $this->create_curl_request($this->post_method, $this->post_url, 'TDP/Api.Context', $this->headers, $this->post_field, $this->post_timeout);
		return $this->response;
	}
	public function collect_headers($headers = array()) {
		$out = array();
		if (count($headers) > 0) {
			foreach ($headers as $keval) {
				if (is_string($keval)) {
					if (0 === strpos($keval, 'application/json')) {
						if (is_array($this->post_field)) {
							$this->post_field = json_encode($this->post_field, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
						}
					}
				}
			}
		}
		return $out;
	}
	
	
	## $this->create_curl_request('POST', 'http://server.tld/query', 'TDP/Api.Context', $headers = array(), $params = array(), $timeout = 30)
	function create_curl_request($action, $url, $UA, $headers = null, $params = array(), $timeout = 30) {
		$cookie_file = (dirname(__FILE__).'/cookies.txt');
		$url = str_replace( "&amp;", "&", urldecode(trim($url)) );
		$ch = curl_init();
		switch (strtolower($action)) {
			case 'get':
				if ((is_array($params)) && (count($params) > 0)) {
					$Querystring = http_build_query($params);
					$url .= "?";
					$url .= $Querystring;
				}
			break;
			case 'post':
			default:
				$url .= "";
			break;
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		if ($headers != null) {
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, false);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $UA);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_COOKIE, $cookie_file);
		//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		$post_fields = NULL;
		switch (strtolower($action)) {
			case 'get':
				curl_setopt($ch, CURLOPT_POST, false);
				curl_setopt($ch, CURLOPT_POSTFIELDS, null);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
			break;
			case 'post':
			default:
				if ((is_array($params)) && (count($params) > 0) && (is_array($headers) && count($headers) > 0)) {
					foreach ($headers as $heval) {
						$getContentType = explode(":", $heval);
						if (strtolower($getContentType[0]) !== 'content-type') {
							continue;
						}
						switch (strtolower(trim($getContentType[0]))) {
							case 'content-type':
								if (isset($getContentType[1])) {
									switch (strtolower(trim($getContentType[1]))) {
										case 'application/json':
											$post_fields = json_encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
										break;
										case 'application/x-www-form-urlencoded':
											$post_fields = http_build_query($params);
										break;
										default:
											$post_fields = http_build_query($params);
										break;
									}
								}
							break;
							default:
								$post_fields = http_build_query($params);
							break;
						}
					}
				} else if ((!empty($params)) || ($params != '')) {
					$post_fields = $params;
				}
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
			break;
		}
		// Get Response
		$response = curl_exec($ch);
		$mixed_info = curl_getinfo($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header_string = substr($response, 0, $header_size);
		$header_content = $this->get_headers_from_curl_response($header_string);
		$header_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if (count($header_content) > 1) {
			$header_content = end($header_content);
		}
		$body = substr($response, $header_size);
		curl_close ($ch);
		$return = array(
			'request'		=> array(
				'method'			=> $action,
				'host'				=> $url,
				'header'			=> $headers,
				'body'				=> $post_fields,
			),
			'response'		=> array(),
		);
		if (!empty($response) || $response != '') {
			$return['response']['code'] = (int)$header_code;
			$return['response']['header'] = array(
				'size' => $header_size, 
				'string' => $header_string,
				'content' => $header_content,
			);
			$return['response']['body'] = $body;
			return $return;
		}
		return false;
	}
	
	
	##################
	private static function get_headers_from_curl_response($headerContent) {
		$headers = array();
		// Split the string on every "double" new line.
		$arrRequests = explode("\r\n\r\n", $headerContent);
		// Loop of response headers. The "count($arrRequests) - 1" is to 
		// avoid an empty row for the extra line break before the body of the response.
		for ($index = 0; $index < (count($arrRequests) - 1); $index++) {
			foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
				if ($i === 0) {
					$headers[$index]['http_code'] = $line;
				} else {
					list ($key, $value) = explode(': ', $line);
					$headers[$index][$key] = $value;
				}
			}
		}
		return $headers;
	}
	private function createHeaders($headers = array()) {
		$curlheaders = array();
		foreach ($headers as $ke => $val) {
			$curlheaders[] = "{$ke}: {$val}";
		}
		return $curlheaders;
	}
}
