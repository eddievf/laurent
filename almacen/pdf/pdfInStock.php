<?php
session_start();
if(!empty($_SESSION['logged']))
{	
	//pdfinstock
	//composer autoload (mpdf)
	require_once __DIR__ . '/lib/mpdf/vendor/autoload.php';

	$mpdf = new mPDF('Letter');
	
	$mpdf->AddPage('L');
	$mpdf->SetHTMLHeader('<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; color: #000000; font-weight: bold; font-style: italic;"><tr>

						<td width="33%"><span style="font-weight: bold; font-style: italic;">{DATE j-m-Y}</span></td>

						<td width="33%" align="center" style="font-weight: bold; font-style: italic;">{PAGENO}/{nbpg}</td>

						<td width="33%" style="text-align: right; ">ALM - Inventario en Almacen</td>

						</tr></table>');
	

	date_default_timezone_set("America/Monterrey");
	$echofun = date("d-m-Y | H:i:s");
	$atwhat = date("Ymd");
	$htmlobject = $_POST['object'];
	ob_start();
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>[PYMAQ] PDF - Material en Almacén</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
	<table width="100%" style="margin-bottom: 20px; border-bottom: 1px solid #0E2F44; vertical-align: bottom; font-family: serif; font-size: 9pt; color: #000000;">
		<tr>
			<td width="33%"><img src="logo.jpg" width="160px" /></td>
			<td width="33%" align="center"><span style="font-family: Arial; color: #000000; font-weight: bold; font-size:16pt;">ALMACEN<br>Stock en Almacén: </span></td>
			<td width="33%" style="text-align: right; font-family: Arial; font-size: 14pt;">Hora y Fecha de Actualización<br> <span style="font-weight: bold; font-size:14pt;"><?php echo $echofun; ?></span></td>
		</tr>
		<br>
		<tr>
			<td><br></td>
		</tr>
	</table>

	<?php
		echo $htmlobject;
	?>

  <div class = "alert">
	<strong>INFO</strong> ::  Reporte generado el <strong><u> <?php echo $echofun; ?> </u></strong>
  </div>
</div>



</body>
</html>

<?php
	$table = ob_get_contents();
	ob_end_clean();

	$mpdf->WriteHTML($table);
	#optional protection -># $mpdf->setProtection(array());

	$mpdf->Output($atwhat.'_consumibles.pdf', 'I');

}
else{
	header("location: ../../notfound.php");
}
?>