<?php
/**
 * XML Requests class
 * 
 */

class XML_Requests {

	private $xml_writer;

	function __construct() {

		$this->xml_writer = new XMLWriter(); 
		$this->xml_writer->openMemory(); 
		$this->xml_writer->setIndent(true);

	}


	/**
	 * Generate XML Header
	 * @return [type] [description]
	 */
	private function header() {
		
		$this->xml_writer->startDocument('1.0', 'UTF-8');		
		$this->xml_writer->startElement("transaction-request");

	}


	/**
	 * Return XML
	 * @return [type] [description]
	 */
	public function output() {

		$this->xml_writer->endElement();
		
		return $this->xml_writer->outputMemory();

	}

	
	/**
	 * Generate XML Verification
	 * @return [type] [description]
	 */
	private function verificationNode($merchantId, $merchantKey) {
		
		$this->xml_writer->writeElement("version", Config::$AZPAY_VERSION);

		$this->xml_writer->startElement("verification");
			$this->xml_writer->writeElement('merchantId', $merchantId);
			$this->xml_writer->writeElement('merchantKey', $merchantKey);
		$this->xml_writer->endElement();

	}


	/**
	 * Generate XML Order
	 * @param  [type] $order [description]
	 * @return [type]        [description]
	 */
	private function orderNode($order) {		
		
		$this->xml_writer->startElement('order');

			$this->xml_writer->writeElement('reference', $order['reference']);
			$this->xml_writer->writeElement('totalAmount', Utils::formatNumber($order['totalAmount']));

		$this->xml_writer->endElement();

	}


	/**
	 * Generate Payments XML nodes
	 * @param  [type] $payments [description]
	 * @return [type]           [description]
	 */
	private function paymentsNode($payments) {

		if (isset($payments[0]) && is_array($payments[0])) {

			foreach ($payments as $key => $payment) {
		
				$this->xml_writer->startElement('payment');

					if (isset($payment['tokenCard']) && !empty($payment['tokenCard'])) {
						$this->tokenCardXML($payment['tokenCard']);
					}

					$this->xml_writer->writeElement('acquirer', $payment['acquirer']);
					$this->xml_writer->writeElement('method', $payment['method']);
					$this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
					$this->xml_writer->writeElement('currency', Config::$CURRENCIES['BRL']);
					$this->xml_writer->writeElement('country', $payment['country']);
					$this->xml_writer->writeElement('numberOfPayments', $payment['numberOfPayments']);
					$this->xml_writer->writeElement('groupNumber', $payment['groupNumber']);

					if (!isset($payment['tokenCard']) || empty($payment['tokenCard'])) {
						$this->cardXML($payment);
					}

				$this->xml_writer->endElement();
			}
			
		} else {

			$this->xml_writer->startElement('payment');

				if (isset($payments['tokenCard']) && !empty($payments['tokenCard'])) {
					$this->tokenCardXML($payments['tokenCard']);
				}

				$this->xml_writer->writeElement('acquirer', $payments['acquirer']);
				$this->xml_writer->writeElement('method', $payments['method']);
				$this->xml_writer->writeElement('amount', Utils::formatNumber($payments['amount']));
				$this->xml_writer->writeElement('currency', Config::$CURRENCIES['BRL']);
				$this->xml_writer->writeElement('country', $payments['country']);
				$this->xml_writer->writeElement('numberOfPayments', $payments['numberOfPayments']);
				$this->xml_writer->writeElement('groupNumber', $payments['groupNumber']);

				if (!isset($payments['tokenCard']) || empty($payments['tokenCard'])) {
					$this->cardXML($payments);
				}

			$this->xml_writer->endElement();
		}

	}


	/**
	 * Generate Boleto Payment XML node
	 * @param  [type] $payment [description]
	 * @return [type]          [description]
	 */
	private function paymentBoletoNode($payment) {

		$this->xml_writer->startElement('payment');

			$this->xml_writer->writeElement('acquirer', $payment['acquirer']);
			$this->xml_writer->writeElement('expire', $payment['expire']);
			$this->xml_writer->writeElement('nrDocument', $payment['nrDocument']);
			$this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
			$this->xml_writer->writeElement('currency', $payment['currency']);
			$this->xml_writer->writeElement('country', $payment['country']);
			$this->xml_writer->writeElement('instructions', $payment['instructions']);

		$this->xml_writer->endElement();

	}


	/**
	 * Generate Billing XML node
	 * @param  [type] $billing [description]
	 * @return [type]          [description]
	 */
	private function billingNode($billing) {

		$this->xml_writer->startElement('billing');

			$this->xml_writer->writeElement('customerIdentity', $billing['customerIdentity']);
			$this->xml_writer->writeElement('name', $billing['name']);
			$this->xml_writer->writeElement('address', $billing['address']);
			$this->xml_writer->writeElement('address2', $billing['address2']);
			$this->xml_writer->writeElement('city', $billing['city']);
			$this->xml_writer->writeElement('state', $billing['state']);
			$this->xml_writer->writeElement('postalCode', Utils::formatNumber($billing['postalCode']));
			$this->xml_writer->writeElement('country', $billing['country']);
			$this->xml_writer->writeElement('phone', Utils::formatNumber($billing['phone']));
			$this->xml_writer->writeElement('email', $billing['email']);

		$this->xml_writer->endElement();

	}


	/**
	 * Generate the XML node to request the authorization to the transaction
	 * @param  [type] $merchant   [description]
	 * @param  [type] $order      [description]
	 * @param  [type] $payments   [description]
	 * @param  [type] $billing    [description]
	 * @param  [type] $url_return [description]
	 * @return [type]             [description]
	 */
	public function authorizeXml($merchant, $order, $payments, $billing, $options) {

		$this->header();

		$this->verificationNode($merchant['id'], $merchant['key']);

		$this->xml_writer->startElement('authorize');

			$this->orderNode($order);

			$this->paymentsNode($payments);							

			$this->billingNode($billing);

			$this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
			$this->xml_writer->writeElement('fraud', $options['fraud']);
			$this->xml_writer->writeElement('customField', $options['customField']);

		$this->xml_writer->endElement();

	}


	/**
	 * Generate the XML node to execute a capture of a transaction
	 * @param  [type] $merchant_id   [description]
	 * @param  [type] $merchant_key  [description]
	 * @param  [type] $transactionId [description]
	 * @return [type]                [description]
	 */
	public function captureXml($merchant_id, $merchant_key, $transactionId) {

		$this->header();

		$this->verificationNode($merchant_id, $merchant_key);

		$this->xml_writer->startElement('capture');

			$this->xml_writer->writeElement('transactionId', $transactionId);

		$this->xml_writer->endElement();

	}


	/**
	 * Generate the XML node to request a direct sale (authorization and capture)
	 * @param  [type] $merchant [description]
	 * @param  [type] $order    [description]
	 * @param  [type] $payments [description]
	 * @param  [type] $billing  [description]
	 * @param  [type] $options  [description]
	 * @return [type]           [description]
	 */
	public function saleXml($merchant, $order, $payments, $billing, $options) {

		$this->header();

		$this->verificationNode($merchant['id'], $merchant['key']);

		$this->xml_writer->startElement('sale');

			$this->orderNode($order);
			
			$this->paymentsNode($payments);

			$this->billingNode($billing);

			$this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
			$this->xml_writer->writeElement('fraud', $options['fraud']);
			$this->xml_writer->writeElement('customField', $options['customField']);

		$this->xml_writer->endElement();
		
	}


	/**
	 * Generate XML node of the Report request
	 * @param  [type] $merchant [description]
	 * @param  [type] $tid      [description]
	 * @return [type]           [description]
	 */
	public function reportXml($merchant, $tid) {

		$this->header();

		$this->verificationNode($merchant['id'], $merchant['key']);

		$this->xml_writer->startElement('report');

			$this->xml_writer->writeElement('transactionId', $tid);

		$this->xml_writer->endElement();

	}


	/**
	 * Generate XML node of the Cancel request
	 * @param  [type] $merchant [description]
	 * @param  [type] $tid      [description]
	 * @return [type]           [description]
	 */
	public function cancelXml($merchant, $tid) {

		$this->header();

		$this->verificationNode($merchant['id'], $merchant['key']);

		$this->xml_writer->startElement('cancel');

			$this->xml_writer->writeElement('transactionId', $tid);

		$this->xml_writer->endElement();

	}


	/**
	 * Generate XML node of the Boleto request
	 * @return [type] [description]
	 */
	public function boletoXml($merchant, $order, $payment, $billing, $options) {

		$this->header();

		$this->verificationNode($merchant['id'], $merchant['key']);

		$this->xml_writer->startElement('boleto');

			$this->orderNode($order);

			$this->paymentBoletoNode($payment);

			$this->billingNode($billing);

			$this->xml_writer->writeElement('customField', $options['customField']);

		$this->xml_writer->endElement();

	}


	/**
	 * Mount the XML of the Card
	 * @param  [type] $card [description]
	 * @return [type]       [description]
	 */
	public function cardXML($card) {

		$this->xml_writer->writeElement('flag', Utils::formatSlug($card['flag']));
		$this->xml_writer->writeElement('cardHolder', $card['cardHolder']);
		$this->xml_writer->writeElement('cardNumber', Utils::formatNumber($card['cardNumber']));
		$this->xml_writer->writeElement('cardSecurityCode', Utils::formatNumber($card['cardSecurityCode']));
		$this->xml_writer->writeElement('cardExpirationDate', Utils::formatNumber($card['cardExpirationDate']));					
		$this->xml_writer->writeElement('saveCreditCard', $card['saveCreditCard']);
		$this->xml_writer->writeElement('generateToken', $card['generateToken']);
		$this->xml_writer->writeElement('departureTax', $card['departureTax']);
		$this->xml_writer->writeElement('softDescriptor', $card['softDescriptor']);

	}


	/**
	 * Return the Token Card node
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 */
	public function tokenCardXML($token) {

		$this->xml_writer->writeElement('tokenCard', $token);

	}

}

?>