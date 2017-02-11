<?php
  session_start();
  if(!empty($_SESSION['logged'])){
    switch ($_SESSION['clearsec']) {
        case 6:
        case 5:
        case 4:
        header("location: almacen/index.php");
        break;
        
        case 1:
        case 2:
        case 3:
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="">
    <meta name="author" content="">

    <title>[PYMAQ] Bienvenido </title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="css/home.css" rel="stylesheet">

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
<!-- NAVBAR
================================================== -->
<body>
    <div class="navbar-wrapper">
        <div class="container">

            <nav class="navbar navbar-inverse navbar-static-top">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#"><span style="color: white;">PYMAQ - Producción</span></a>
                        <p class="navbar-text">Bienvenido, <?php echo $_SESSION['user'] ?></p>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="index.php">Consumibles</a></li>
                            <li><a href="tools.php">Herramientas</a></li>
                            <li><a href="#">Reportes</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Departamentos <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Maquinados</a></li>
                                    <li><a href="#">Troquelado</a></li>
                                    <li><a href="#">Doblado</a></li>
                                    <li><a href="#">Pintura</a></li>
                                    <li><a href="#">Soldadura</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="almacen">Almacen</a></li>
                                    <li><a href="#">Embarque</a></li>
                                </ul>
                            </li>
                            <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Salir</a></li>
                        </ul>
                    </div><!--navbody-->
                </div>
            </nav>
        </div>
    </div>


    <!-- Carousel
    ================================================== -->
    <div id="dataCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#dataCarousel" data-slide-to="0" class="active"></li>
            <li data-target="#dataCarousel" data-slide-to="1"></li>
            <li data-target="#dataCarousel" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner" role="listbox">
            <div class="item active">
                <img class="second-slide" src="data:image/gif;base64,R0lGODlhAQABAPAAAABtzP///yH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Second slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1><u>Almacén</u></h1>
                        <p>
                        <?php
                            $alm = $conn->prepare("SELECT CommonName, RestockLevel, Abbrev, Description, inStock, AreaName
                                                    FROM stockproducts, departamentos,produnits
                                                    WHERE (stockproducts.ReqArea = departamentos.ID)
                                                    AND (stockproducts.RestockLevel = 1)
                                                    AND (stockproducts.Unit = produnits.ID)
                                                    ORDER BY inStock, RAND() LIMIT 5");
                            $alm->execute();

                            echo "<div class='table-responsive'><table class='table table-hover table-bordered table-condensed'>
                            <thead>
                            <tr>
                                <th class = 'col-md-5'>PRODUCTO</th>
                                <th class = 'col-md-3'>CANTIDAD EN ALMACEN</th>
                                <th class = 'col-md-4'>DEPARTAMENTO</th>
                            </tr>
                            </thead>
                            <tbody>";

                            while($row=$alm->fetch(PDO::FETCH_ASSOC)){
                                $restock = $row["RestockLevel"];
                                $stock = $row["inStock"];
                                if($restock == 1) { 
                                    echo "<tr>";

                                    if($row['inStock'] == 0){
                                        echo "<td>$row[CommonName] <span class='label label-danger'><span class='glyphicon glyphicon-alert'></span> AGOTADO</span></td>";
                                    }
                                    else{
                                        echo "<td>$row[CommonName] <span class='label label-warning'><span class='glyphicon glyphicon-alert'></span> RESTOCK</span></td>";
                                    }
                                }
                                else{
                                    echo "<tr><td>$row[CommonName] </td>";
                                }

                            echo "<td><strong>$row[inStock] $row[Abbrev]</strong></td>
                            <td> $row[AreaName]</td>
                            </tr>";
                        }

                        echo "</tbody></table></div>";
                        ?>
                        </p>

                        <p><a class="btn btn-lg btn-primary" href="almacen/index.php" role="button">Ir a Almacén</a></p>
                    
                    </div>
                </div> 
            </div>
            <div class="item">
                <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAPAAAN0vKv///yH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1><u>Maquinados</u></h1>
                        <p>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th class = "col-md-0">O. T</th>
                                <th class = "col-md-0">O. C</th>
                                <th class = "col-md-1">Cliente</th>
                                <th class = "col-md-0">Partida</th>
                                <th class = "col-md-5">Pieza</th>
                                <th class = "col-md-1">Cantidad</th>
                                <th class = "col-md-2">Solicitud</th>
                                <th class = "col-md-4">Estado</th>
                                <th class = "col-md-1">Avance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php               

                                try{
                                    $stmt = $conn->prepare("SELECT OrdenTrabajo, OrdenCompra, maqorders.Cliente, Partida, Validate, Prioridad, ProdKey, Descripcion, Cantidad,
                                    FechaSolicitud, Avance, Progress
                                    FROM maqorders, maqpiezas
                                    WHERE (maqpiezas.id)=(maqorders.pieza)
                                    AND (Avance < 1 OR Validate <> 1)
                                    ORDER BY Validate DESC, Prioridad DESC, RAND(), FechaSolicitud, OrdenTrabajo, Partida
                                    LIMIT 3");
                                    $stmt->execute();
        
                                    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

                                        echo "<tr>
                                        <td>$row[OrdenTrabajo] ";

                                        if($row['Prioridad'] == 1){
                                            echo "<span class='label label-warning'><u>Urgente</u></span> ";
                                        }
                                        else{
                                            echo '';
                                        }

                                        echo "</td>
                                        <td>$row[OrdenCompra]</td>
                                        <td>$row[Cliente] </td>
                                        <td>$row[Partida]</td>
                                        <td>";
                                        

                                        if($row['Validate'] != 1){
                                                echo "$row[Descripcion] <span class='label label-warning'><span class='glyphicon glyphicon-alert'></span> VAL</span></td>";
                                        }
                                        else{
                                            echo "$row[Descripcion]</td>";
                                        }

                                        echo "<td>$row[Cantidad]</td>
                                        <td>$row[FechaSolicitud]</td>
                                        <td>$row[Progress]</td>
                                        <td>$row[Avance]%</td>
                                        </tr>";
                                    }
                                }
                                catch (PDOException $e){
                                    echo "
                                    <div class= 'alert alert-danger'><strong>[ERROR]</strong> :: ".$e->getMessage()."</div>";
                                }
                        
                            ?>
                        </tbody>
                    </table>
                </div>

                        </p>
                        <p><a class="btn btn-lg btn-danger" href="#" role="button">Ir a Maquinados</a></p>
                    </div>
                </div>
            </div>
            <div class="item">
                <img class="third-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Third slide">
                <div class="container">
                    <div class="carousel-caption">
                        <h1><u>Troquelado</u></h1>
              <p>Notificaciones del Departamento de Troquelado</p>
              <p><a class="btn btn-lg btn-warning" href="#" role="button">Ir a Troquelado</a></p>
            </div>
          </div>
        </div>
      </div>
      <a class="left carousel-control" href="#dataCarousel" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="right carousel-control" href="#dataCarousel" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div><!-- /.carousel -->


    <!-- Marketing messaging and featurettes
    ================================================== -->
    <!-- Wrap the rest of the page in another container to center all the content. -->

    <div class="container marketing">

    &raquo;

      <!-- Three columns of text below the carousel -->
      <div class="row">
        <div class="col-lg-4">
          <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Etiam porta sem malesuada magna mollis euismod. Nullam id dolor id nibh ultricies vehicula ut id elit. Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Praesent commodo cursus magna.</p>
          <p><a class="btn btn-default" href="#" role="button">View details ;</a></p>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
          <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
          <h2>Heading</h2>
          <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Cras mattis consectetur purus sit amet fermentum. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh.</p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
        <div class="col-lg-4">
          <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>
        </div><!-- /.col-lg-4 -->
      </div><!-- /.row -->


      <!-- START THE FEATURETTES -->

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">First featurette heading. <span class="text-muted">It'll blow your mind.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-5">
          <img class="featurette-image img-responsive center-block" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
        </div>
      </div>

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7 col-md-push-5">
          <h2 class="featurette-heading">Oh yeah, it's that good. <span class="text-muted">See for yourself.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-5 col-md-pull-7">
          <img class="featurette-image img-responsive center-block" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
        </div>
      </div>

      <hr class="featurette-divider">

      <div class="row featurette">
        <div class="col-md-7">
          <h2 class="featurette-heading">And lastly, this one. <span class="text-muted">Checkmate.</span></h2>
          <p class="lead">Donec ullamcorper nulla non metus auctor fringilla. Vestibulum id ligula porta felis euismod semper. Praesent commodo cursus magna, vel scelerisque nisl consectetur. Fusce dapibus, tellus ac cursus commodo.</p>
        </div>
        <div class="col-md-5">
          <img class="featurette-image img-responsive center-block" data-src="holder.js/500x500/auto" alt="Generic placeholder image">
        </div>
      </div>

      <hr class="featurette-divider">

      <!-- /END THE FEATURETTES -->


      <!-- FOOTER -->
      <footer>
        <p class="pull-right"><a href="#">Back to top</a></p>
        <p>&copy; 2017. Prensas y Maquinados SA de CV &middot; @eddievf
      </footer>

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </body>
</html>
<?php
            break;
    }
}
else{
    header("location: login.html");
}
?>