<?php
session_start();

if(!empty($_SESSION['logged'])){
	switch ($_SESSION['clearsec']) {
		case 2:
		case 4:
			header("location: ../notfound.php");
			break;

		case 1:
		case 3:
		case 5:
		case 6:
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>[PYMAQ] ALMACEN</title>

	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/cupertino/jquery-ui.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid-theme.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" />
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,600,400">

	<link href="css/welcome.css" rel="stylesheet">
	<link href="css/popgrid.css" rel="stylesheet">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="http://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.5.3/jsgrid.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

	<script src="js/scrollspy.js"></script>
	<script src="js/jsExitTool.js"></script>
	<script src="js/jsFloorGrid.js"></script>
	<script src="js/sendTool.js"></script>

	<?php
		date_default_timezone_set("America/Monterrey");

		//database connection
    	try{
    		$config = include("db/config.php");
    		$conn = new PDO($config["db"], $config["username"], $config["password"]);
    		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    		$yayconn = '<div class = "alert alert-success text-center" style = "margin-top: 18px;"> <strong> CONN </strong> :: Conexion Establecida </div>';
    	}
    	catch (PDOException $e){
    		$yayconn = "<div class= 'alert alert-danger'><p class='text-center'><strong>[ERROR] </strong> :: <u>".$e->getMessage()."</u> :: (error: JD01)</p></div>";
    	}
    ?>   
</head>

<body data-spy="scroll" data-target="#side-nav" data-offset="180">

	<nav class="navbar navbar-inverse navbar-fixed-top"><!--navbar group-->
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">Consumibles Almacén</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="index.php">Consumibles</a></li>
					<li><a href="tools.php">Herramientas</a></li>
					<?php
						if($_SESSION['clearsec'] == 1){
							echo '<li><a href="#">Reportes</a></li>';
						}
						
						switch ($_SESSION['clearsec']) {
							case 1:
							case 2:
							case 3:
								echo '<li class="dropdown">
              			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Departamentos <span class="caret"></span></a>
              			<ul class="dropdown-menu">
              				<li><a href="../index.php"><u>Inicio</u></a></li>
                			<li><a href="#">Maquinados</a></li>
                			<li><a href="#">Troquelado</a></li>
                			<li><a href="#">Doblado</a></li>
                			<li><a href="#">Pintura</a></li>
                			<li><a href="#">Soldadura</a></li>
                			<li role="separator" class="divider"></li>
                			<li><a href="index.php">Almacen</a></li>
                			<li><a href="#">Embarque</a></li>
              			</ul>
					</li>';
								break;
						}
					?>
					
					<li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Salir</a></li>
				</ul>
			</div><!--navbody-->
		</div><!--container fluid-->
	</nav>


	<div class="container-fluid">
		<div class="row">
			
			<div class="col-sm-3 col-md-2 sidebar affix" id="side-nav"><!--sidebar-->
				<?php echo $yayconn; ?>
				<ul class="nav nav-sidebar">
					<li><a href="#loantool">Préstamo de Herramienta</a></li>
					<li><a href="#toolout">Material en Préstamo</a></li>
					<li><a href="#regtool">Herramientas Nuevas</a></li>
					
				</ul>
			</div><!--end sidebar-->
			

			<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"><!--Main Content-->
					
				<div id="loantool">
					<h1 class="page-header"><b>Herramientas</b></h1>

					<div class="container-fluid">	
						<h2 class="sub-header">Préstamo de Herramientas</h2><br>
					
						<div id="jsExitToolGrid"></div>

						<div id="exitError" title="ERROR"></div>

						<div id="exitSuccess" title="EXITO"></div>

   						<script>exitToolGrid();</script>

					</div><!--Container Fluid-->	
				</div><!--Div with ID | END OF NEW ORDER-->

				<div id="toolout">

					<div class="container-fluid">	
						<h2 class="sub-header">Herramientas en Piso</h2><br>
							
							<div id="jsFloorGrid"></div>


				<div id="endLoanDialog">
    				<form id="endLoanForm">
    					<div class="form-field">
    						<label for="editToolHash">
    						<input type="hidden" name="editToolHash" id="editToolHash" />
        				<div class="form-field">
            				<label for="editToolName">Herramienta:</label>
            				<input id="editToolName" name="editToolName" type="text" />
        				</div>
        				<div class="form-field">
            				<label for="editToolLive">Cantidad Regreso:</label>
            				<input id="editToolLive" name="editToolLive" type="text" />
        				</div>
        				<div class="form-field">
            				<label for="editHasTool">En Uso:</label>
            				<input id="editHasTool" name="editHasTool" type="text" readonly />
        				</div>
        				<div class="form-field">
            				<label for="editToolStock">Total Piezas en Almacén:</label>
            				<input id="editToolStock" name="editToolStock" type="text" readonly />
        				</div>
        				<div class="form-field">
            				<label for="editAreaName">Departamento:</label>
            				<input id="editAreaName" name="editAreaName" type="text" readonly />
        				</div>
        				<div class="form-field">
            				<label for="editToolTime">Hora de Salida:</label>
            				<input id="editToolTime" name="editToolTime" type="text" readonly />
        				</div>
        				<div class="form-field">
            				<button class="btn btn-primary" type="submit" id="save">Guardar</button>
        				</div>
    				</form>
				</div>

				<div id="floorError" title="ERROR"></div>

				<div id="floorSuccess" title="EXITO"></div>

				<script>floorToolsGrid();</script>

							
							
					</div><!--Container Fluid-->

				</div><!--Div with ID | END OF NEW ORDER-->

				<br>
				<div id="regtool">
					<div class="container-fluid">
						<h2 class="sub-header">Registro de Herramientas Nuevas</h2><br>
						<div class="jumbotron jumbotron-fluid">
							<div class="container">
									<h2 class="display-3">Formulario de Registro</h2>
									<p class="lead text-muted"><i>Ingresar Herramienta al Almacén</i></p><br>
								</div>
							<div class="container" id="newTool">
								<form class="form-horizontal" role="form" method="POST" action="php/newTool.php" name="newToolForm" id="newToolForm">

									<div class="form-group row">
										<label for="newTool" class="col-sm-2 col-form-label">Nombre Pieza</label>
										<div class="col-sm-6">
											<input class="form-control" maxlength="20" type="text" placeholder="No más de 20 caracteres" name="newTool" id="newTool" required>
										</div>
									</div>
									<div class="form-group row">
										<label for="newToolStock" class="col-sm-2 col-form-label">Cantidad Recibida</label>
										<div class="col-sm-3">
											<input class="form-control" type="number" placeholder="Cantidad" name="newToolStock" id="newToolStock" required>
										</div>
									</div>
									<div class="form-group row">
										<label for="newToolStock" class="col-sm-2 col-form-label">Numero de Serie</label>
										<div class="col-sm-3">
											<input class="form-control" type="text" placeholder="En caso de existir" name="newToolSerieNum" id="newToolSerieNum">
										</div>
									</div>
									<div class="form-group row">
										<label for="newToolArea" class="col-sm-2 col-form-label">Área de Uso</label>
										<div class="col-sm-6">
											<select class= "form-control selectpicker" data-live-search="true" name="prodExitLog" id="prodExitLog">
												<option value="11">Soldadura</option>
												<option value="13">Pulido</option>
												<option value="8">Pintura</option>
												<option value="6">Maquinados</option>
												<option value="12">Ensamble</option>
												<option value="9">Troquelado</option>
												<option value="10">Doblado</option>
												<option value="14">Limpieza</option>
												<option value="7">Almacen</option>
												<option value="2">Development</option>
											</select>
										</div>
									</div>
									
									<div class="form-group row">
										<div class="col-sm-6">
										<button type="submit" class="btn btn-primary">Aceptar</button>
										</div>
									</div>
								</form>
								<!--End New Order Form-->
								</div>
								<!--End New Order Container-->
							</div>

							<div id="newToolSuccess" title="EXITO"></div>

							<div id="newToolFail" title="ERROR"></div>

					</div><!--container fluid-->
				</div><!--DIV ID | END OF ENTER TOOL-->



			</div><!--Main Content until here-->	
		</div>
	</div>

</body>

</html>
<?php
			break;
	}
}
else{
	header("location: ../notfound.php");
}
?>