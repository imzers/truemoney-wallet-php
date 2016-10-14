<?php
	/**
     * Decryption, recall encryption
     *
     * @param string $pwd Key to decrypt with (can be binary of hex)
     * @param string $data Content to be decrypted
     * @param bool $ispwdHex Key passed is in hexadecimal or not
     * @access public
     * @return string
     */ 
class rc4crypt {
	function encrypt($pwd, $data, $ispwdHex = 0) {
        if ($ispwdHex) {
            $pwd = @pack('H*', $pwd); // valid input, please!
			}
        $key[] = '';
        $box[] = '';
        $cipher = '';
        $pwd_length = strlen($pwd);
        $data_length = strlen($data);
        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
			}
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box [$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
			}
        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box [$a] = $box [$j];
            $box[$j] = $tmp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
			}
        return $cipher;
	}
    function decrypt($pwd, $data, $ispwdHex = 0) {
        return rc4crypt::encrypt($pwd, $data, $ispwdHex);
	}
    function bEncryptRC4($data, $key) {
        $rn = rand(2, 7);
        $sf_temp[1] = "";
        $sf_temp[2] = "";
        for ($fn = 0; $fn <= $rn; $fn++) {
            if (rand(1, 2) == 1) {
                $sf_temp[1] = $sf_temp[1] . chr(96 + rand(1, 26));
                $sf_temp[2] = $sf_temp [2] . (int) (rand(1, 9));
            } else {
                $sf_temp[1] = $sf_temp[1] . (int) (rand(1, 9));
                $sf_temp [2] = $sf_temp[2] . chr(96 + rand(1, 26));
            }
        }
        $dataTemp = $sf_temp[1] . $data . substr($sf_temp[2], 0, $rn);
        $result = $this->fCharCode($rn) . strtoupper((bin2hex(rc4crypt::encrypt($key, $dataTemp))));
        return $result;
	}
    function fCharCode($num) {
        $sf_temp = "ECHDXRFKNQLUGAJZLMSIVTWPYB";
        return substr($sf_temp, $num, 1);
	}
    function fCodeNum($char) {
        $sf_temp = "ECHDXRFKNQLUGAJZLMSIVTWPYB";
        return strpos($sf_temp, $char);
	}
    function bDecryptRC4($Data, $Key) {
        try {
            $bn = intval(rc4crypt:: fCodeNum(substr($Data, 0, 1)) + 1);
            $Data = substr($Data, 1);
            $DecryptText = rc4crypt::encrypt($Key, pack("H*", $Data));
            $DecryptText = substr($DecryptText, $bn);
            return substr($DecryptText, 0, strlen($DecryptText) - ($bn - 1));
		} catch (Exception $e) {
            return "0";
		}
	}
    function DecryptRC4($Key, $Data) {
        return rc4crypt::encrypt($Key, pack("H*", $Data));
	}
    function EncryptRC4($Key, $Data) {
        return bin2hex(rc4crypt::encrypt($Key, $Data));
	}

}
?>