<?php
/**
 * AZPay SDK
 * 
 * Version: 1.0.0
 * Author: Gabriel Guerreiro
 * Copyright AZClick
 * 
 */

include 'config.php';
include 'utils.php';
include 'xml_requests.php';

class AZPay {

	/**
	 * VERSION
	 */
	const VERSION = '1.0.0';

	/**
	 * Client Key and Client ID
	 * @var array
	 */
	public $merchant = array(
		'id' => '',
		'key' => ''
	);

	/**
	 * Order Details
	 * @var array
	 */
	public $config_order = array(
		'reference' => '',
		'totalAmount' => '0000'
	);

	/**
	 * Billing Details
	 * @var array
	 */
	public $config_billing = array(
		'customerIdentity' => '',
		'name' => '',
		'address' => '',
		'address2' => '',
		'city' => '',
		'state' => '',
		'postalCode' => '',
		'country' => 'BR',
		'phone' => '',
		'email' => ''
	);

	/**
	 * Creditcard data
	 * @var array
	 */
	public $config_card_payments = array(
		'acquirer' => '',
		'method' => '',
		'amount' => '0000',
		'currency' => '986',
		'country' => 'BRA',
		'numberOfPayments' => '1',
		'groupNumber' => '0',
		'flag' => '',
		'cardHolder' => '',
		'cardNumber' => '',
		'cardSecurityCode' => '',
		'cardExpirationDate' => '',
		'saveCreditCard' => 'true',
		'generateToken' => 'false',
		'departureTax' => '0',
		'softDescriptor' => '',
		'tokenCard' => ''
	);

	/**
	 * Boleto configuration
	 * @var array
	 */
	public $config_boleto = array(
		'acquirer' => '10',
		'expire' => '',
		'nrDocument' => '',
		'amount' => '000',
		'currency' => '986',
		'country' => 'BRA',
		'instructions' => ''
	);

	/**
	 * Options extra
	 * @var array
	 */
	public $config_options = array(
		'urlReturn' => '',
		'fraud' => 'false',
		'customField' => ''
	);


	/**
	 * Reponse
	 * @var [type]
	 */
	public $curl_response;

	/**
	 * Meta data
	 * @var [type]
	 */
	public $curl_response_meta;

	/**
	 * Errors
	 * @var [type]
	 */
	public $curl_error;	

	/**
	 * Errors Code
	 * @var [type]
	 */
	public $curl_error_code;

	/**
	 * Set timeout to cURL
	 * default = 5s
	 * @var [type]
	 */
	public $curl_timeout = 5;

	/**
	 * Error flag
	 * @var boolean
	 */
	public $error = false;

	/**
	 * Flag to execute Exceptions
	 * @var boolean
	 */
	public $throw_exceptions = true;



	/**
	 * Construct class
	 * 
	 * @param [type] $merchant_id  [Client ID]
	 * @param [type] $merchant_key [Client Key]
	 */
	function __construct($merchant_id, $merchant_key) {

		if (!function_exists('curl_init'))
            throw new Exception('CURL module not available! Pest requires CURL. See http://php.net/manual/en/book.curl.php');        

		$this->merchant['id'] = $merchant_id;
		$this->merchant['key'] = $merchant_key;
	}


	/**
	 * Execute request
	 * 
	 * @return [type] [execute cUrl request]
	 */
	public function execute($xml) {		

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, Config::$RECIVER_URL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);

		$this->curl_response = curl_exec($ch);
		$this->curl_response_meta = curl_getinfo($ch);

		if ($this->curl_response === false || $this->curl_response_meta === false) {
			$this->error = true;
			$this->curl_error = curl_error($ch);
			$this->curl_error_code = curl_errno($ch);
		}

		if ($this->curl_response === false && $this->throw_exceptions)			
			throw new AZPay_Curl_Exec_Exception(curl_error($ch));		

		if ($this->curl_response_meta === false && $this->throw_exceptions)
			throw new AZPay_Curl_Meta_Exception(curl_error($ch));

		curl_close($ch);

		$this->checkErrors();
	}


	/**
	 * Return the cUrl response 
	 * 
	 * @return [type] [description]
	 */
	public function response() {

		// If no errors, return XML parsed
		if ($this->error == false) {
			
			$xml = simplexml_load_string($this->curl_response);

			return $xml;

		} else {

			return $this->curl_error;

		}

	}


	/**
	 * Request authorization to complete the transaction
	 * 	 
	 * @return [type]          [description]
	 */
	public function authorize() {

		$requests = new XML_Requests();
		
		$requests->authorizeXml($this->merchant, $this->config_order, $this->config_card_payments, $this->config_billing, $this->config_options);
		$xml = $requests->output();

		$this->execute($xml);

	}

	/**
	 * Request the capture of the transaction
	 * after the authorization request, to payment validation
	 * 
	 * @return [type] [description]
	 */
	public function capture($transactionId) {

		$requests = new XML_Requests();
		
		$requests->captureXml($this->merchant['id'], $this->merchant['key'], $transactionId);
		$xml = $requests->output();

		$this->execute($xml);

	}


	/**
	 * Request Sale
	 * without a pre authorization
	 * 
	 * @return [String] [description]
	 */
	public function sale() {

		$requests = new XML_Requests();
		
		$requests->saleXml($this->merchant, $this->config_order, $this->config_card_payments, $this->config_billing, $this->config_options);
		$xml = $requests->output();

		$this->execute($xml);

	}


	/**
	 * Request Report
	 * to check the transaction status
	 * 
	 * @param  [type] $tid [TID]
	 * @return [type]      [description]
	 */
	public function report($tid) {

		$requests = new XML_Requests();
		
		$requests->reportXml($this->merchant, $tid);
		$xml = $requests->output();

		$this->execute($xml);

	}


	/**
	 * Request Cancel
	 * 
	 * @param  [type] $tid [description]
	 * @return [type]      [description]
	 */
	public function cancel($tid) {

		$requests = new XML_Requests();
		
		$requests->cancelXml($this->merchant, $tid);
		$xml = $requests->output();

		$this->execute($xml);

	}


	/**
	 * Request Boleto
	 * 
	 * @return [type] [description]
	 */
	public function boleto() {

		$requests = new XML_Requests();
		
		$requests->boletoXml($this->merchant, $this->config_order, $this->config_boleto, $this->config_billing, $this->config_options);
		$xml = $requests->output();

		$this->execute($xml);

	}

	/**
	 * Check error exceptions
	 * 
	 * @return [void] [description]
	 */
	private function checkErrors() {

		if (!$this->throw_exceptions)
            return;

        $meta = $this->curl_response_meta;
        $response = $this->curl_response;

        if ($meta === false)
            return;

        switch ($meta['http_code']) {
            case 400:
                throw new AZPay_BadRequest($response);
                break;
            case 401:
                throw new AZPay_Unauthorized($response);
                break;
            case 403:
                throw new AZPay_Forbidden($response);
                break;
            case 404:
                throw new AZPay_NotFound($response);
                break;
            case 405:
                throw new AZPay_MethodNotAllowed($response);
                break;
            case 409:
                throw new AZPay_Conflict($response);
                break;
            case 410:
                throw new AZPay_Gone($response);
                break;
            case 422:
                throw new AZPay_InvalidRecord($response);
                break;
            default:
                if ($meta['http_code'] >= 400 && $meta['http_code'] <= 499) {
                    throw new AZPay_ClientError($response);
                } elseif ($meta['http_code'] >= 500 && $meta['http_code'] <= 599) {
                	throw new AZPay_ServerError($response);
                } elseif (!isset($meta['http_code']) || $meta['http_code'] >= 600) {
                    throw new AZPay_UnknownResponse($response);
                }
        }

	}

}


/**
 * General Exceptions
 */
class AZPay_Exception extends Exception {}
class AZPay_Unknown_Exception extends AZPay_Exception {}

/**
 * Client Exception
 */
class AZPay_Client_Exception extends AZPay_Exception {}

/**
 * HTTP Exceptions
 */
class AZPay_BadRequest extends AZPay_Client_Exception {}
class AZPay_Unauthorized extends AZPay_Client_Exception {}
class AZPay_Forbidden extends AZPay_Client_Exception {}
class AZPay_NotFound extends AZPay_Client_Exception {}
class AZPay_MethodNotAllowed extends AZPay_Client_Exception {}
class AZPay_Conflict extends AZPay_Client_Exception {}
class AZPay_Gone extends AZPay_Client_Exception {}
class AZPay_InvalidRecord extends AZPay_Client_Exception {}
class AZPay_ServerError extends AZPay_Client_Exception {}
class AZPay_UnknownResponse extends AZPay_Client_Exception {}

/**
 * cUrl Exceptions
 */
class AZPay_Curl_Init_Exception extends AZPay_Exception {}
class AZPay_Curl_Meta_Exception extends AZPay_Exception {}
class AZPay_Curl_Exec_Exception extends AZPay_Exception {}

?>