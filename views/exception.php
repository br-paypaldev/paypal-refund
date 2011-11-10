<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>Opz</title>
		<link rel="stylesheet" type="text/css" href="example.css" />
	</head>
	<body>
		<div id="app">
			<h1>Alguma coisa deu errado!</h1>
			<ul>
			<?php
				do {
					echo '<li>' , $e->getMessage() , '</li>';

					$e = $e->getPrevious();
				} while ( $e instanceof Exception );
			?>
			</ul>
			<a class="a-btn" href="example.php">Voltar</a>
		</div>
	</body>
</html>