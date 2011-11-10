<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Estorno</title>
		<link rel="stylesheet" type="text/css" href="example.css" />
	</head>
	<body>
		<div id="app">
			<h1>Estorno feito</h1>
			<p>
			O estorno de <?php echo $nvp[ 'CURRENCYCODE' ] . ' ' . $nvp[ 'TOTALREFUNDEDAMOUNT' ]; ?> foi feito.
			<a class="a-btn" href="example.php">Voltar</a>
		</div>
	</body>
</html>