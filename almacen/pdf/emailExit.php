<?php
session_start();
if(!empty($_SESSION['logged']))
{	
	//emailSalidas
	//autoload
	require_once __DIR__ . '/lib/mpdf/vendor/autoload.php';
	require_once __DIR__ . '/lib/swiftmailer/lib/swift_required.php';

	ini_set('max_execution_time', 300); //300 seconds = 5 minutes

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
	$fechaA = $_POST['DateA'];
	$fechaB = $_POST['DateB'];
	ob_start();
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ENVIADO - REPORTE DE ALMACEN</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
	<table width="100%" style="margin-bottom: 20px; border-bottom: 1px solid #0E2F44; vertical-align: bottom; font-family: serif; font-size: 9pt; color: #000000;">
		<tr>
			<td width="33%"><img src="logo.jpg" width="160px" /></td>
			<td width="33%" align="center"><span style="font-family: Arial; color: #000000; font-weight: bold; font-variant: small-caps; font-size:16pt;">ALMACEN<br>Reporte de Almacen 
				<?php
					if($fechaA == $fechaB){
						echo "<br>Fecha: ".$fechaA;
					}
					else{
						echo $fechaA." al ".$fechaB;
					}
				?>

			 </span></td>
			<td width="33%" style="text-align: right; font-family: Arial; font-size: 14pt; font-variant: small-caps;">Hora y Fecha de Actualizacion<br> <span style="font-weight: bold; font-size:14pt;"><?php echo $echofun; ?></span></td>
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
	<strong>INFO</strong> :: <span style="font-variant: small-caps;">  Reporte generado el <strong><u> <?php echo $echofun; ?> </u></strong></span>
  </div>
</div>



</body>
</html>

<?php
	$table = ob_get_contents();
	ob_end_clean();

	$filename = $atwhat.'_almacen.pdf';

	$mpdf->WriteHTML(utf8_encode($table));
	#optional protection -># $mpdf->setProtection(array());

	$content = $mpdf->Output('', 'S');

	$attachment = new Swift_Attachment($content, $filename, 'application/pdf');

	$msgbody = "<p><h2>Reporte de Almacén</h2>
    <h3><i>Departamento de Almacén e Inventario</i></h3>
    <i>Responsable: Daniel Eduardo</i><br></p>
    <hr />
    <p>Reporte de Consumibles distribuidos en Almacén, que abarca del <u> ".$fechaA." al ".$fechaB."</u></p>
    <p>Generado el día: ".$echofun."</p>
    <p><i>Mensaje enviado desde Sistema de Producción, Departamento de Almacén e Inventario.</i></p>";

	$message = Swift_Message::newInstance()
		->setSubject('REPORTE - Almacén')
		->setFrom(array('ingenieria@pymaq.com.mx' => 'Departamento Almacén'))
		->setTo(array('eddievf@hotmail.com'))
		->setBody($msgbody, 'text/html')
		->attach($attachment);

	$transport = Swift_SmtpTransport::newInstance('mail.pymaq.com.mx', 26);

	$mailer = Swift_Mailer::newInstance($transport);

	if ($mailer->send($message))
	{
  		$mpdf->Output($filename, 'I');
	}
	else
	{
  		echo "Error\n";
	}

}
else{
	header("location: ../../notfound.php");
}
?>