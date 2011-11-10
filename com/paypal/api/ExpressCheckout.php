<?php
/**
 * Pacote de integração com PayPal
 * @package com.paypal.api
 */
namespace com\paypal\api;

use \InvalidArgumentException;
use \RuntimeException;
use \UnexpectedValueException;
use com\paypal\http\HTTPConnection;
use com\paypal\http\HTTPRequest;

class ExpressCheckout {
	/**
	 * HOST da API do PayPal, utilizada em produção.
	 */
	const API_HOST = 'api-3t.paypal.com';

	/**
	 * Versão da API.
	 */
	const API_VERSION = '72.0';

	/**
	 * HOST da API no Sandbox PayPal, utilizada durante os testes.
	 */
	const SANDBOX_HOST = 'api-3t.sandbox.paypal.com';

	/**
	 * @var	array
	 */
	private $params = array();

	/**
	 * @var	boolean
	 */
	private $sandbox;

	/**
	 * Constroi o objeto de integração com PayPal Express Checkout especificando
	 * a versão da API que será utilizada.
	 * @param	string $user Usuário da API
	 * @param	string $pswd Senha do usuário da API
	 * @param	string $signature Assinatura da API
	 */
	public function __construct( $user , $pswd , $signature ) {
		$this->sandbox( false );
		$this->setParam( 'USER' , $user );
		$this->setParam( 'PWD' , $pswd );
		$this->setParam( 'SIGNATURE' , $signature );
		$this->setParam( 'VERSION' , ExpressCheckout::API_VERSION );
	}

	/**
	 * Operação DoExpressCheckout da API PayPal Express Checkout
	 * @param	string $token
	 * @param	string $payerId
	 * @return	ExpressCheckout
	 */
	public function doExpressCheckout( $token , $payerId ) {
		$this->setParam( 'METHOD' , 'DoExpressCheckoutPayment' );
		$this->setParam( 'TOKEN' , $token );
		$this->setParam( 'PAYERID' , $payerId );

		return $this;
	}

	/**
	 * Executa a operação
	 * @return	array Resposta no formato NVP.
	 * @throws	RuntimeException
	 */
	public function execute() {
		$httpConnection = new HTTPConnection();

		if ( $this->sandbox ) {
			$host = ExpressCheckout::SANDBOX_HOST;
		} else {
			$host = ExpressCheckout::API_HOST;
		}

		$httpConnection->initialize( $host , true );

		foreach ( $this->params as $name => $value ) {
			$httpConnection->setParam( $name , $value );
		}

		$httpResponse = $httpConnection->execute( '/nvp' , HTTPRequest::POST );

		if ( $httpResponse->getStatusCode() == 200 ) {
			$nvp = array();
			$matches = array();

			if ( preg_match_all(
					'/(?<name>[^\=]+)\=(?<value>[^&]+)&?/',
					$httpResponse->getContent(),
					$matches ) ) {

				foreach ( $matches[ 'name' ] as $offset => $name ) {
					$nvp[ $name ] = urldecode(
						$matches[ 'value' ][ $offset ]
					);
				}

				if ( isset( $nvp[ 'ACK' ] ) ) {
					if ( $nvp[ 'ACK' ] == 'Failure' ) {
						$e = new RuntimeException(
							'Falha integração com PayPal'
						);

						foreach ( $nvp as $name => $value ) {
							if ( substr( $name , 0 , 13 ) == 'L_LONGMESSAGE' ) {
								$o = substr( $name , 13 );
								$code = 0;

								if ( isset( $nvp[ 'L_ERRORCODE' . $o ] ) ) {
									$code = $nvp[ 'L_ERRORCODE' . $o ];
								}

								$c = new RuntimeException( $value , $code , $e );
								$e = $c;
							}
						}

						throw $e;
					} else {
						return $nvp;
					}
				}
			}

			throw new RuntimeException( 'Falha na recepção dos dados do PayPal' );
		} else {
			throw new RuntimeException( 'Falha na comunicação com o PayPal' );
		}
	}

	/**
	 * Operação GetExpressCheckoutDetails da API PayPal ExpressCheckout
	 * @param	string $token
	 * @return	ExpressCheckout
	 */
	public function getExpressCheckoutDetails( $token ) {
		$this->setParam( 'METHOD' , 'GetExpressCheckoutDetails' );
		$this->setParam( 'TOKEN' , $token );

		return $this;
	}

	/**
	 * Define se as requisições serão feitas no PayPal Sandbox ou em produção.
	 * @param	boolean $sandbox
	 * @return	com\paypal\api\ExpressCheckout
	 */
	public function sandbox( $sandbox = true ) {
		$this->sandbox = !!$sandbox;

		return $this;
	}

	/**
	 * Operação RefundTransaction da API PayPal
	 * @param	string $transactionId
	 * @param	string $refundType
	 */
	public function refundTransaction( $transactionId , $refundType = 'Full' ) {
		$this->setParam( 'METHOD' , 'RefundTransaction' );
		$this->setParam( 'TRANSACTIONID' , $transactionId );
		$this->setParam( 'REFUNDTYPE' , $transactionId );

		return $this;
	}

	/**
	 * Operação SetExpressCheckout da API PayPal Express Checkout
	 * @param	string $returnURL
	 * @param	string $cancelURL
	 * @throws	InvalidArgumentException
	 */
	public function setExpressCheckout( $returnURL , $cancelURL ) {
		if ( filter_var( $returnURL , FILTER_VALIDATE_URL ) &&
			filter_var( $cancelURL , FILTER_VALIDATE_URL ) ) {

			$this->setParam( 'METHOD' , 'SetExpressCheckout' );
			$this->setParam( 'RETURNURL' , $returnURL );
			$this->setParam( 'CANCELURL' , $cancelURL );

			return $this;
		} else {
			throw new InvalidArgumentException( 'URL inválida' );
		}
	}

	/**
	 * Define um parâmetro que será enviado na requisição
	 * @param	string $name
	 * @param	string $value
	 */
	public function setParam( $name , $value ) {
		$this->params[ $name ] = $value;
	}
}