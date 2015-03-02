<?php 
/**
 * Config
 *
 * Classe de constantes e configuracões
 * 
 */

class Config {

	# AZPay reciver url
	public static $RECIVER_URL = 'https://api.azpay.com.br/v1/receiver/';

	# Version
  public static $AZPAY_VERSION = '1.0.0';

  # Flags
  public static $FLAGS = array(
    'visa' => 'Visa',
    'mastercard' => 'MasterCard',
    'amex' => 'Amex',
    'elo' => 'Elo',
    'dinners' => 'Dinners',
    'discover' => 'Discover',
    'jcb' => 'JCB',
    'aura' => 'Aura'
  );

  # AZPay Cards Operators
  public static $CARD_OPERATORS = array(
    'cielo' => array(
      'name' => 'Cielo',
      'modes' => array(
      	'store' => array('code' => '1', 'name' => 'Buy Store'),
      	'cielo' => array('code' => '2', 'name' => 'Buy Cielo')
      )
      ,
      'flags' => array(
        'visa',
        'mastercard',
        'amex',
        'elo',
        'dinners',
        'discover',
        'jcb',
        'aura'
      )
    ),
    'redecard' => array(
      'name' => 'RedeCard',
      'modes' => array(
        array('code' => '3', 'name' => 'WebService'),
        array('code' => '4', 'name' => 'Integrated')
      ),
      'flags' => array(
        'visa',
        'mastercard'
      )
    ),
    'elavon' => array(
      'code' => '6',
      'name' => 'Elavon',
      'flags' => array(
        'visa',
        'mastercard'
      )
    )
  );

  # AZPay boleto operators
  public static $BOLETO_OPERATORS = array(
    'bradesco' => array(
      'code' => '10',
      'name' => 'Bradesco'
    ),
    'bradesco_eletro' => array(
      'code' => '18',
      'name' => 'Bradesco'
    ),
    'itau' => array(
      'code' => '11',
      'name' => 'Itaú'
    ),
    'banco_do_brasil' => array(
      'code' => '12',
      'name' => 'Banco do Brasil'
    ),
    'santander' => array(
      'code' => '13',
      'name' => 'Santander'
    ),
    'caixa_sem_registro' => array(
      'code' => '14',
      'name' => 'Caixa - Sem Registro'
    ),
    'caixa_sinco' => array(
      'code' => '15',
      'name' => 'Caixa - Sinco'
    ),
    'caixa_sigcb' => array(
      'code' => '16',
      'name' => 'Caixa - SIGCB'
    ),
    'hsbc' => array(
      'code' => '17',
      'name' => 'HSBC'
    )
  );

  # Curency
  public static $CURRENCIES = array(
    'BRL' => 986
  );

  # Operations Methods
  public static $OPERATION_METHODS = array(
    '1' => array(
      'name' => 'Crédito a vista'
    ),
    '2' => array(
      'name' => 'Parcelado loja'
    ),
    '3' => array(
      'name' => 'Parcelado administradora'
    ),
    '4' => array(
      'name' => 'Débito'
    )
  );

  # Operation Types
  public static $ORDER_STATUS = array(
    '0' => 'Criada / Em andamento',
    '1' => 'Autenticada',
    '2' => 'Não Autenticada',
    '3' => 'Autorizada pela operadora',
    '4' => 'Não autorizada pela operadora',
    '5' => 'Em Cancelamento',
    '6' => 'Cancelado',
    '7' => 'Em Captura',
    '8' => 'Capturada / Finalizada',
    '9' => 'Não capturada',
    '10' => 'Pagamento Recorrente - Agendado',
    '12' => 'Boleto Gerado'
  );


  /**
   * Code responses by flag
   * @var array
   */
  public static $RESPONSE = array(
    'CREATED'         => 0,
    'AUTHENTICATED'   => 1,
    'UNAUTHENTICATED' => 2,
    'AUTHORIZED'      => 3,
    'UNAUTHORIZED'    => 4,
    'CANCELLING'      => 5,
    'CANCELLED'       => 6,
    'CAPTURING'       => 7,
    'APPROVED'        => 8,
    'UNAPPROVED'      => 9,
    'SCHEDULED'       => 10,
    'GENERATED'       => 12
  );

  /**
   * Code operations by flag
   * @var array
   */
  public static $OPERATION = array(
    'AUTHORIZE' => 1,
    'CAPTURE'   => 2,
    'SALE'      => 3,
    'CANCEL'    => 5,
    'REPORT'    => 6,
    'REBILL'    => 7,
    'BOLETO'    => 8,
    'AUTH'      => 9,
    'PAGSEGURO' => 10,
    'PAYPAL'    => 11,
    'TRANSFER'  => 12,
  );

}
?>