<?php
session_start();
if(!empty($_SESSION['logged']))
{

	$config = include("../db/config.php");
	$conn = new PDO($config["db"], $config["username"], $config["password"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $prod = $_POST['prodEnterLog'];
    $fechaInicial = $_POST['dateEnterLog'];
    $fechaFinal = $_POST['dateEnterLogEnd'];

    $query_string = "SELECT stockproducts.CommonName, WhoRequest, departamentos.AreaName, WhoGives, LogTime, NumMoved, Abbrev, MoveType, RemainStock FROM stocklog, stockproducts, departamentos, produnits WHERE (stocklog.LogProduct = stockproducts.ID) AND (stocklog.WhoArea = departamentos.ID) AND (stocklog.LogUnit = produnits.ID) AND MoveType = 1 ";



    if($fechaFinal >= $fechaInicial){
        $query_string .= "AND (stocklog.LogTime BETWEEN '".$fechaInicial." 00:00:00' AND '".$fechaFinal." 23:59:59') ";
    }
    else{
        echo "
        <div class= 'alert alert-danger'><p class='text-center'><strong>[ERROR] </strong> :: <u>Fecha Final es Posterior a Fecha Inicio</u> :: (error: JD08)</p></div>";
    }

    if($prod != 0){
        $query_string .= "AND LogProduct = ".$prod;
    }

    $query_string .= " ORDER BY LogTime ASC";

	$stmt = $conn->prepare($query_string);
	$stmt->execute();

	echo '
		<form target="_blank" method="POST" id="pdfRequestForm" name="pdfRequestForm">
            <button style="margin-top: 5px; margin-bottom: 15px;" type="submit" value="email" class="btn btn-warning" name="EnterPDFEmail" id="EnterPDFEmail" onclick="document.pdfRequestForm.action='; echo "'pdf/emailEnter.php'"; echo '">
                <span class="glyphicon glyphicon-envelope"></span> Enviar Reporte
            </button>
            <button style="margin-top: 5px; margin-bottom: 15px;" type="submit" value="download" class="btn btn-primary" name="ReqPDF" id="ReqPDF"  onclick="document.pdfRequestForm.action='; echo "'pdf/pdfEnterLog.php'"; echo '">
                <span class="glyphicon glyphicon-save-file"></span> Descargar Reporte
            </button>           

    ';

    ob_start();

    echo "<div class='table-responsive'>
     <table class='table table-striped table-hover table-bordered table-condensed'>
     <thead>
     <tr>
     <th class = 'col-md-2'>PRODUCTO</th>
     <th class = 'col-md-1'>CANT.</th>
     <th class = 'col-md-2'>SOLICITANTE</th>
     <th class = 'col-md-2'>AREA</th>
     <th class = 'col-md-3'>HORA</th>
     <th class = 'col-md-3'>ENCARGADO</th>
     <th class = 'col-md-1'>RESTANTE</th>
     </tr>
     </thead>
     <tbody>";

    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
    	$remainStock = $row['RemainStock'];
        

        if($remainStock == 0) { 
            echo "<tr class='warning'>
                    <td>$row[CommonName]</td>";
        }
        else{
        	echo "<tr>
        	<td>$row[CommonName] </td>";

        }

        echo "<td><strong>$row[NumMoved] $row[Abbrev]</strong></td>
        <td> $row[WhoRequest]</td>
        <td> $row[AreaName]</td>
        <td> $row[LogTime]</td>
        <td> $row[WhoGives]</td>";

        if($remainStock == 0) { 
            echo "<td> $row[RemainStock] <span class='label label-danger'><span class='glyphicon glyphicon-alert'></span> </span></td>";
        }
        else{
            echo "<td> $row[RemainStock]</td>";

        }

        
        echo "</tr>";
        

    }

    echo "</tbody></table></div>";

    
    $html = ob_get_contents();

    echo '<input type="hidden" name = "object" value = "'.$html.'" id = "object">';
    echo '<input type="hidden" name = "DateA" value = '.$fechaInicial.' id="DateA">';
    echo '<input type="hidden" name = "DateB" value = '.$fechaFinal.' id="DateB"></form>';	
}
else{
    header("location: ../../notfound.php");
}
?>