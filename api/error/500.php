<!DOCTYPE HTML>
<html>
	<head>
		<title>Page error</title>
		<link rel="stylesheet" href="/css/site.css" type="text/css" media="screen" />
	</head>

	<body>
		<header>
			<div class="logo">
				<!--<img border="0" src="/img/xxxx.png">-->
			</div>
			<h1>Page error</h1>
		</header>
		<div class="wrapper">
			<p>
			The page you are accessing has the following errors:
			</p>
			<p>
			<?php global $ERROR_MESSAGE; echo $ERROR_MESSAGE; ?>
			</p>
			<br>
			<br>
		</div>
		<footer>
			<div class="footer">(C) 2013 Tui Innovation</div>
		</footer>
	</body>
</html> 

