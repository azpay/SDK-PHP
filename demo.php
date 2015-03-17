<?php 
date_default_timezone_set('America/Sao_Paulo');

include 'azpay.php';

$azpay = new AZPay('1', 'd41d8cd98f00b204e9800998ecf8427e');

$azpay->config_order = array(
	'reference' => '123456789',
	'totalAmount' => 'R$ 10,00'
);

$azpay->config_billing = array(
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

$azpay->config_card_payments = array(
	'acquirer' => Config::$CARD_OPERATORS['cielo']['modes']['store']['code'],
	'method' => '1',
	'amount' => '1000',
	'currency' => Config::$CURRENCIES['BRL'],
	'country' => 'BRA',
	'numberOfPayments' => '1',
	'groupNumber' => '0',
	'flag' => 'mastercard',
	'cardHolder' => 'Jose da Silva',
	'cardNumber' => '5453010000066167',
	'cardSecurityCode' => '123',
	'cardExpirationDate' => '2018-05',
	'saveCreditCard' => 'true',
	'generateToken' => 'false',
	'departureTax' => '0',
	'softDescriptor' => ''
);

$azpay->config_boleto = array(
	'acquirer' => Config::$BOLETO_OPERATORS['bradesco']['code'],
	'expire' => date('Y-m-d', strtotime('today + 10 day')),
	'nrDocument' => '123456789',
	'amount' => 'R$ 10,00',
	'currency' => Config::$CURRENCIES['BRL'],
	'country' => 'BRA',
	'instructions' => 'Não aceitar pagamento em cheques. \n Percentual Juros Dia: 1%. Percentual Multa: 1%.'
);

$azpay->config_options = array(
	'urlReturn' => 'http://loja.exemplo.com.br',
	'fraud' => 'false',
	'customField' => ''
);

/**
 * Generates Boleto
 */
//$azpay->boleto();

/**
 * Request authorization to transaction
 */
//$azpay->authorize();

/**
 * Execute capture of transaction
 */
//$azpay->capture();

/**
 * Execute authorization and capture (direct sale), to transaction
 */
//$azpay->sale();

/**
 * Cancel a transaction
 */
//$azpay->cancel($tid);

/**
 * Check a transaction status by TID
 */
$azpay->report('BCEF86DB-841F-079B-5505-6F97E14BA284');

/**
 * Obtain response
 */
$xml = $azpay->response();
print_r($xml);

?>