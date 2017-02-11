<?php
session_start();
if(!empty($_SESSION['logged']))
{
	$config = include("../db/config.php");
	$conn = new PDO($config["db"], $config["username"], $config["password"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$stmt = $conn->prepare("SELECT CommonName, RestockLevel, Abbrev, Description, inStock, AreaName
							FROM stockproducts, departamentos,produnits
							WHERE (stockproducts.ReqArea = departamentos.ID)
							AND (stockproducts.Unit = produnits.ID)
							ORDER BY stockproducts.ID");
	$stmt->execute();

	echo "

    ";


    echo '
        <form  target="_blank" method="POST" id="pdfRequestForm" name="pdfRequestForm">
            <button style="margin-top: 5px; margin-bottom: 15px;" type="submit" value="email" class="btn btn-warning" name="StockPDFEmail" id="StockPDFEmail" onclick="document.pdfRequestForm.action='; echo "'pdf/emailStock.php'"; echo '">
                <span class="glyphicon glyphicon-envelope"></span> Enviar Reporte
            </button>
            <button style="margin-top: 5px; margin-bottom: 15px;" type="submit" value="download" class="btn btn-primary" name="ReqPDF" id="ReqPDF"  onclick="document.pdfRequestForm.action='; echo "'pdf/pdfInStock.php'"; echo '">
                <span class="glyphicon glyphicon-save-file"></span> Descargar Reporte
            </button>

    ';

    ob_start();

    echo "<div class='table-responsive'>
     <table class='table table-striped table-hover table-bordered table-condensed'>
     <thead>
     <tr>
     <th class = 'col-md-5'>PRODUCTO</th>
     <th class = 'col-md-3'>CANTIDAD EN ALMACEN</th>
     <th class = 'col-md-4'>DEPARTAMENTO</th>
     </tr>
     </thead>
     <tbody>";

    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
    	$restock = $row["RestockLevel"];
    	$stock = $row["inStock"];

        if($restock == 1) { 
            echo "<tr class='warning'>";
            if($row['inStock'] == 0){
          		echo "<td>$row[CommonName] <span class='label label-danger'><span class='glyphicon glyphicon-alert'></span> AGOTADO</span></td>";
          	}
          	else{
          		echo "<td>$row[CommonName] <span class='label label-warning'><span class='glyphicon glyphicon-alert'></span> RESTOCK</span></td>";
          	}
        }
        else{
        	echo "<tr>
        	<td>$row[CommonName] </td>";

        }

        echo "<td><strong>$row[inStock] $row[Abbrev]</strong></td>
        <td> $row[AreaName]</td>
        </tr>";
        

    }

    echo "</tbody></table></div>";

    
    $html = ob_get_contents();

    echo '<input type="hidden" name = "object" value = "'.$html.'" id = "object"> </form>';	
}
else{
    header("location: ../../notfound.php");
}
?>