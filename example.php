<?php
require_once 'com.paypal.http.phar';
require_once 'com/paypal/api/ExpressCheckout.php';

use com\paypal\http\HTTPConnection;
use com\paypal\api\ExpressCheckout;

if ( isset( $_GET[ 'action' ] ) ) {
	try {
		$ec = new ExpressCheckout(
			'usuario-da-api',
			'senha',
			'assinatura'
		);

		switch ( $_GET[ 'action' ] ) {
			case 'set':
				$ec->setExpressCheckout(
					'http://127.0.0.3/paypal-ec/example.php?action=do',
					'http://127.0.0.3/paypal-ec/example.php?action=cancel'
				);
				$ec->setParam( 'L_PAYMENTREQUEST_0_AMT0' , 100.00 );
				$ec->setParam( 'L_PAYMENTREQUEST_0_NAME0' , 'Produto de Teste' );
				$ec->setParam( 'L_PAYMENTREQUEST_0_QTY0' , 1 );

				$ec->setParam( 'L_PAYMENTREQUEST_0_AMT1' , 100.00 );
				$ec->setParam( 'L_PAYMENTREQUEST_0_NAME1' , 'Outro teste' );
				$ec->setParam( 'L_PAYMENTREQUEST_0_QTY1' , 1 );

				$ec->setParam( 'PAYMENTREQUEST_0_AMT' , 200.00 );
				$ec->setParam( 'PAYMENTREQUEST_0_ITEMAMT' , 200.00 );
				$ec->setParam( 'PAYMENTREQUEST_0_CURRENCYCODE' , 'BRL' );
				$ec->setParam( 'PAYMENTREQUEST_0_PAYMENTACTION' , 'Sale' );
				$ec->setParam( 'LOCALECODE' , 'pt_BR' );

				$nvp = $ec->sandbox()->execute();

				header( 'Location: https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' . $nvp[ 'TOKEN' ] );
				break;
			case 'do':
				$ec->getExpressCheckoutDetails( $_GET[ 'token' ] );

				$nvp = $ec->sandbox()->execute();

				$ec->doExpressCheckout( $_GET[ 'token' ] , $_GET[ 'PayerID' ] );

				$ec->setParam( 'PAYMENTREQUEST_0_AMT' , 200.00 );
				$ec->setParam( 'PAYMENTREQUEST_0_CURRENCYCODE' , 'BRL' );
				$ec->setParam( 'PAYMENTREQUEST_0_PAYMENTACTION' , 'Sale' );

				$nvp = array_merge( $ec->sandbox()->execute() , $nvp );

				require 'views/confirm.php';
				break;
			case 'refund':
				$ec->refundTransaction( $_GET[ 'tid' ] );

				$nvp = $ec->sandbox()->execute();

				require 'views/refund.php';
				break;

		}
	} catch ( Exception $e ) {
		require 'views/exception.php';
	}
} else {
	require 'views/cart.html';
}