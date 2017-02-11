<?php
	session_start();
	session_destroy();

	header("refresh: 3; url= index.php");

	echo '
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Sesión Terminada</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Salir</a>
			</div>
			
		</div><!--container fluid-->
	</nav>

	<div class="jumbotron jumbotron-fluid">
		<div class="container">
			<h1><u>Sesión Terminada</u></h1>
			<p>La sesión actual ha sido terminada.<br>Puede volver a ingresar con sus mismas credenciales desde el portal de acceso.<br>A continuación, será redirigido al sitio de inicio de sesión.</p><br>
			<p><small>Dar click <u><a href="login.html">aquí</a></u> si no se redirige automaticamente.</small></p>
		</div>
	</div>

	<div class="container">
		<p class="text-muted"><small>2017 Prensas y Maquinados SA de CV.<br>Contact webmaster at info@eddievf.com</small></p>
	</div>
</body>
</html>
';
