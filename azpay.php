<?php
/**
 * AZPay SDK
 * 
 */

include 'config.php';
include 'utils.php';
include 'xml_requests.php';

class AZPay {

	# Key and ID of the client
	public $merchant = array(
		'id' => '',
		'key' => ''
	);

	# Order
	public $config_order = array(
		'reference' => '',
		'totalAmount' => ''
	);

	# Billing
	public $config_billing = array(
		'customerIdentity' => '1',
		'name' => 'Fulano de Tal',
		'address' => 'Av. Federativa, 230',
		'address2' => '10 Andar',
		'city' => 'Mogi das Cruzes',
		'state' => 'SP',
		'postalCode' => '20031-170',
		'country' => 'BR',
		'phone' => '21 4009-9400',
		'email' => 'fulanodetal@email.com'
	);

	# Card Payment
	public $config_card_payments = array(
		'acquirer' => '1',
		'method' => '1',
		'amount' => 'R$ 0,00',
		'currency' => 986,
		'country' => 'BRA',
		'numberOfPayments' => '1',
		'groupNumber' => '0',
		'flag' => 'mastercard',
		'cardHolder' => '',
		'cardNumber' => '',
		'cardSecurityCode' => '123',
		'cardExpirationDate' => '2018-05',
		'saveCreditCard' => 'true',
		'generateToken' => 'false',
		'departureTax' => '0',
		'softDescriptor' => ''
	);

	# Boleto
	public $config_boleto = array(
		'acquirer' => '10',
		'expire' => '',
		'nrDocument' => '',
		'amount' => '000',
		'currency' => 986,
		'country' => 'BRA',
		'instructions' => ''
	);

	# Options
	public $config_options = array(
		'urlReturn' => '',
		'fraud' => 'false',
		'customField' => ''
	);


	public $curl_response;
	public $curl_response_error;
	public $times = 0;
	public $error = false;


	/**
	 * Construct class
	 * @param [type] $merchant_id  [description]
	 * @param [type] $merchant_key [description]
	 */
	function __construct($merchant_id, $merchant_key) {

		$this->merchant['id'] = $merchant_id;
		$this->merchant['key'] = $merchant_key;

	}


	/**
	 * Execute request
	 * @return [type] [description]
	 */
	public function execute($xml) {		

		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, Config::$RECIVER_URL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if (curl_errno($ch)) {
			
			$this->curl_response_error = curl_errno($ch) . ' - ' . curl_error($ch);
			$this->error = true;

		} else {

			$this->curl_response = curl_exec($ch);
			curl_close($ch);

		}

	}


	/**
	 * [responseXml description]
	 * @return [type] [description]
	 */
	public function response() {

		if ($this->error == false) {
			
			$xml = simplexml_load_string($this->curl_response);

			return $xml;

		} else {

			return $this->response_error;

		}

	}


	/**
	 * Request Authorize
	 * with authorization
	 * @param  [type] $order   [description]
	 * @param  [type] $payment [description]
	 * @param  [type] $billing [description]
	 * @return [type]          [description]
	 */
	public function authorize() {

		$requests = new XML_Requests();
		
		$requests->authorizeXml($this->merchant, $this->config_order, $this->config_card_payments, $this->config_billing, $this->config_options);
		$xml = $requests->output();

		$this->execute($xml);

	}


	/**
	 * Request Sale
	 * without authorization
	 * @param  [type] $order    [description]
	 * @param  [type] $payments [description]
	 * @param  [type] $billing  [description]
	 * @param  [type] $options  [description]
	 * @return [type]           [description]
	 */
	public function sale() {

		$requests = new XML_Requests();
		
		$requests->saleXml($this->merchant, $this->config_order, $this->config_card_payments, $this->config_billing, $this->config_options);
		$xml = $requests->output();

		$this->execute($xml);

	}


	/**
	 * Request Report
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
	 * @return [type] [description]
	 */
	public function boleto() {

		$requests = new XML_Requests();
		
		$requests->boletoXml($this->merchant, $this->config_order, $this->config_boleto, $this->config_billing, $this->config_options);
		$xml = $requests->output();

		$this->execute($xml);

	}

}
?>