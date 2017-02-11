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
	<script src="js/jsExitProd.js"></script>
	<script src="js/jsReqProd.js"></script>
	<script src="js/getData.js"></script>
	<script src="js/prodForm.js"></script>

	<style>
		.jsgrid-grid-header,
		.jsgrid-grid-body{
  			overflow: auto;
		}
	</style>

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
					<li><a href="#exitstock">Salida de Material</a></li>
					<li><a href="#livestock">Material Disponible</a></li>
					<li><a href="#enterstock">Entrada de Material</a></li>
					
				</ul>
			</div><!--end sidebar-->
			

			<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main"><!--Main Content-->
					
				<div id="exitstock">
					<h1 class="page-header"><b>Consumibles</b></h1>

					<div class="container-fluid">	
						<h2 class="sub-header">Entrega de Material</h2><br>
					
						<div id="jsExitProd"></div>

						<div id="errorDialog" title="ERROR"></div>

						<div id="welpDialog" title="ATENCION"></div>

						<div id="yayDialog" title="EXITO"></div>

   						<script>exitProdGrid();</script>

					</div><!--Container Fluid-->	
				</div><!--Div with ID | END OF NEW ORDER-->

				<div id="livestock">

					<div class="container-fluid">	
						<h2 class="sub-header">Material en Almacén</h2><br>
							<div id="showStock">
							</div>

							<div class="btn-group btn-group-justified" role="group" aria-label="...">
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-primary" name="buttonDefault" id="buttonDefault">
										Material en Almacén
									</button>
								</div>
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-danger" name="buttonEmpty" id="buttonEmpty">
										Material Agotado
									</button>
								</div>
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalReqProd" name="buttonReq" id="buttonReq" disabled>
										Requisición de Material
									</button>
								</div>
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalExitLog" name="buttonExitLog" id="buttonExitLog">
										Salida Material*
									</button>
								</div>
								<div class="btn-group" role="group">
									<button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalEnterLog" name="buttonEnterLog" id="buttonEnterLog">
										Entrada Material*
									</button>
								</div>

							</div>


							<div id="modalReqProd" class="modal fade" role="dialog" aria-labelledby="modalReqProd" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title">Requisición de Compras Materiales</h4>
										</div>
										<div class="modal-body">
											<p><b>Seleccionar Información de Productos</b></p>
											<div id="jsReqProd"></div>
											<script>reqProdGrid();</script>
										</div>
									</div>
								</div>
							</div>

							<div id="modalExitLog" class="modal fade" role="dialog" aria-labelledby="modalExitLog" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title">[SALIDAS] Seleccionar Producto y Filtro</h4>
										</div>
										<form id="ExitLogForm" method="POST">
											<div class="modal-body">
												<div class="form-group row">
													<label for="prodExitLog" class="col-sm-4  col-form-label">Producto</label>
													<div class="col-sm-6">
														<select class= "form-control selectpicker" data-live-search="true" name="prodExitLog" id="prodExitLog">
															<option value="0" selected>Todos los Productos</option>
															<?php		
																$query = ("SELECT stockproducts.ID AS id, CommonName AS text
																			FROM stockproducts
																			ORDER BY id");
																$data = $conn->prepare($query);
																$data->execute();

																while($row=$data->fetch(PDO::FETCH_ASSOC)){
																	echo '<option value="'.$row['id'].'">'.$row['text'].'</option>';
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group row">
													<label for="dateExitLog" class="col-sm-4 col-form-label">Fecha Inicial</label>
													<div class="col-sm-6">
														<input class="form-control" type="date" name="dateExitLog" id="dateExitLog">
													</div>
												</div>
												<div class="form-group row">
													<label for="dateExitLogEnd" class="col-sm-4 col-form-label">Fecha Final</label>
													<div class="col-sm-6">
														<input class="form-control" type="date" name="dateExitLogEnd" id="dateExitLogEnd">
													</div>
												</div>
											</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
											<button type="submit" class="btn btn-primary">Aceptar</button>
										</form>
										</div>
									</div>
								</div>
							</div>

							<div id="modalEnterLog" class="modal fade" role="dialog" aria-labelledby="modalEnterLog" aria-hidden="true">
								<div class="modal-dialog" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
											<h4 class="modal-title">[ENTRADA] Seleccionar Producto y Filtro</h4>
										</div>
										<form id="EnterLogForm" method="POST">
											<div class="modal-body">
												<div class="form-group row">
													<label for="prodEnterLog" class="col-sm-4  col-form-label">Producto</label>
													<div class="col-sm-6">
														<select class= "form-control selectpicker" data-live-search="true" name="prodEnterLog" id="prodEnterLog">
															<option value="0" selected>Todos los Productos</option>
															<?php		
																$query = ("SELECT stockproducts.ID AS id, CommonName AS text
																			FROM stockproducts
																			ORDER BY id");
																$data = $conn->prepare($query);
																$data->execute();

																while($row=$data->fetch(PDO::FETCH_ASSOC)){
																	echo '<option value="'.$row['id'].'">'.$row['text'].'</option>';
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group row">
													<label for="dateEnterLog" class="col-sm-4 col-form-label">Fecha Inicial</label>
													<div class="col-sm-6">
														<input class="form-control" type="date" name="dateEnterLog" id="dateEnterLog">
													</div>
												</div>
												<div class="form-group row">
													<label for="dateEnterLogEnd" class="col-sm-4 col-form-label">Fecha Final</label>
													<div class="col-sm-6">
														<input class="form-control" type="date" name="dateEnterLogEnd" id="dateEnterLogEnd">
													</div>
												</div>
											</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
											<button type="submit" class="btn btn-primary">Aceptar</button>
										</form>
										</div>
									</div>
								</div>
							</div>
							
					</div><!--Container Fluid-->

				</div><!--Div with ID | END OF NEW ORDER-->

				<br>
				<div id="enterstock">
					<div class="container-fluid">
						<h2 class="sub-header">Entrada de Material al Almacén</h2><br>
						<div class="jumbotron jumbotron-fluid">
							<div class="container">
									<h2 class="display-3">Formulario de Registro</h2>
									<p class="lead text-muted"><i>Ingresar la información de producto recibido</i></p><br>
								</div>
							<div class="container" id="newConsumeProd">
								<form class="form-horizontal" role="form" method="POST" action="php/moreproduct.php" id="addProdForm" name="addProdForm">

									<div class="form-group row">
										<label for="newConsumePiece" class="col-sm-2 col-form-label">Producto</label>
										<div class="col-sm-6">
											<select class="form-group row prodselect" id="newConsumePiece" name="newConsumePiece">
														<?php		
															$query = ("SELECT stockproducts.ID AS id, CommonName AS text, Description AS des, Unit AS unit, ReqArea AS area
																		FROM stockproducts
																		ORDER BY id");
															$data = $conn->prepare($query);
															$data->execute();
															while($row=$data->fetch(PDO::FETCH_ASSOC)){
																echo '<option value="'.$row['id'].'" data-unit="'.$row['unit'].'" data-area="'.$row['area'].'" data-desc="'.utf8_encode($row['des']).'">'.$row['text'].'</option>';
															}
												
														?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label for="newConsumeDesc" class="col-sm-2 col-form-label">Descripción</label>
										<div class="col-sm-6">
											<input class="form-control" type="text" maxlength="70" placeholder="No Exceder 70 Caracteres" name="newConsumeDesc" id="newConsumeDesc" required>
										</div>
									</div>
									<div class="form-group row">
										<label for="newConsumeQuant" class="col-sm-2 col-form-label">Cantidad Recibida</label>
										<div class="col-sm-3">
											<input class="form-control" type="number" name="newConsumeQuant" id="newConsumeQuant" required>
										</div>
									</div>
									<div class="form-group row">
										<label for="newConsumeUnit" class="col-sm-2 col-form-label">Unidad de Medicion</label>
										<div class="col-sm-3">
											<select class="form-group row selectpicker" data-live-search="true" name="newConsumeUnit" id="newConsumeUnit">
														<?php		
															$query = ("SELECT ID AS id, LongName AS text
																		FROM produnits
																		ORDER BY id");
															$data = $conn->prepare($query);
															$data->execute();
															while($row=$data->fetch(PDO::FETCH_ASSOC)){
																echo '<option value="'.$row['id'].'">'.$row['text'].'</option>';
															}
												
														?>
											</select> 
										</div>
									</div>
									<div class="form-group row">
										<label for="newToolArea" class="col-sm-2 col-form-label">Área de Uso</label>
										<div class="col-sm-6">
											<select class= "form-control selectpicker" data-live-search="true" name="newToolArea" id="newToolArea">
												<option value="11">Soldadura</option>
												<option value="13">Pulido</option>
												<option value="8">Pintura</option>
												<option value="6">Maquinados</option>
												<option value="12">Ensamble</option>
												<option value="9">Troquelado</option>
												<option value="10">Doblado</option>
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

					</div><!--container fluid-->
					<div id="stockError" title="ERROR"></div>

					<div id="stockExito" title="EXITO"></div>
				</div><!--DIV ID | END OF ENTER STOCK-->



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