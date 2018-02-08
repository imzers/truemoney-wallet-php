# TrueMoney Wallet Payment #

## Description ##

Truemoney Wallet is an online payment gateway. We strive to make payments simple for both the merchant and customers. 
With this plugin you can accept online payment on your app or website using TrueMoney Wallet payment gateway.
This Module have library you can use to:
- Create Payment
- Transfer Balance
- Void Payment (soon)
- Check Payment Status

### Minimum Requirements ###

- PHP version 5.4.x or greater

## How to use sample ##

For using sample on this plugin, first you need client_id, client_secret, client_token, client_shopcode, and client_appname that was provided by Truemoney Wallet, please visit <a href="http://www.truemoney.com/partner/">http://www.truemoney.com/partner/</a> or contact [partner@truemoney.com](partner@truemoney.com)

### Set API Credential

Please check file config.php starting at line 35 and put all of your API Credential on it
```php
$ClientConfig = array(
	'sandbox'		=> array(
			'client_id' 		=> 'TMN_CLIENT_ID', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_secret' 	=> 'TMN_CLIENT_SECRET', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_token' 		=> 'TMN_CLIENT_TOKEN', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_shopcode'	=> 'TMN_CLIENT_SHOPCODE', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_appname'	=> 'TMN_CLIENT_APPNAME', // You will get this from TMN-Wallet, Please contact TrueMoney
	),
	'live'			=> array(
			'client_id' 		=> 'TMN_LIVE_CLIENT_ID', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_secret' 	=> 'TMN_LIVE_CLIENT_SECRET', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_token' 		=> 'TMN_LIVE_CLIENT_TOKEN', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_shopcode'	=> 'TMN_LIVE_CLIENT_SHOPCODE', // You will get this from TMN-Wallet, Please contact TrueMoney
			'client_appname'	=> 'TMN_LIVE_CLIENT_APPNAME', // You will get this from TMN-Wallet, Please contact TrueMoney
	),
);
```
Also check file constant.php starting at line 4
```php
	static public $THIS_SERVER_MODE = 'sandbox'; // 'sandbox' || 'live'
```
Put your desired environment (Sandbox or Live)

### Installation

1. Open a Terminal/Console window.
2. Change directory to the server root and path of this script (i.e. `cd /var/www/truemoney-wallet-php-master` if your local server root is at /var/www).
3. Install dependencies (`composer install`).
4. Visit plugin sample payment in a browser (probably at http://localhost/truemoney-wallet-php-master/sample/01-create-payment.php) and submit payment.


#### NOTES ####
* Please note: To test payment in sandbox mode (Test Server Environment), you should do it on Bangkok Office Hour: Monday to Friday on (09.00 AM to 17.00 PM) because Truemoney Wallet turning down test-server service, including on weekend and holiday

Further information about TrueMoney Wallet, please check on  [TrueMoney Website](https://www.truemoney.com/wallet)
### Demo ###
- Chek our [Demo](http://market.myarenaonline.com)
