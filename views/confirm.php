<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Confirmação</title>
		<link rel="stylesheet" type="text/css" href="example.css" />
	</head>
	<body>
		<div id="app">
			<h1>Pagamento feito</h1>
			<p>
			<em><?php echo $nvp[ 'FIRSTNAME' ] . ' ' , $nvp[ 'LASTNAME' ];?></em> fez o
			pagamento no valor de <strong><?php echo $nvp[ 'CURRENCYCODE' ] . ' ' . $nvp[ 'AMT' ];?></strong>
			</p>
			<a class="a-btn" href="example.php?action=refund&tid=<?php echo $nvp[ 'PAYMENTINFO_0_TRANSACTIONID' ]?>">Estornar</a>
		</div>
	</body>
</html>