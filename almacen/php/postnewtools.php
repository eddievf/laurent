<?php
session_start();
if(!empty($_SESSION['logged']))
{
	$config = include("../db/config.php");
	$conn = new PDO($config["db"], $config["username"], $config["password"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	date_default_timezone_set("America/Monterrey");
	$regdate = date("Y-m-d H:i:s");
	$username = $_SESSION['user'];

	try{
		$check = $conn -> prepare("SELECT ID, ToolHash, ToolName, ToolLive, ToolStock, HasTool, ToolArea
                                    FROM stocktools
                                    WHERE ID = :toolID");

		$live = $conn -> prepare("UPDATE stocktools
                                  	SET ToolLive = :newLive, HasTool = :newHost 
                                  	WHERE (stocktools.ID = :newID)");

		$log = $conn ->prepare("INSERT INTO stocklogtools (LogTool, ToolRequest, ToolArea, ToolGives, ToolTime, ToolNum, ToolMoveType, RemainStock)
                                  VALUES (:logtool, :logreq, :logarea, :loggives, :logtime, :lognum, :logmove, :logstock) ");

		if(isset($_POST['toolexit'])){
			$json = $_POST['toolexit'];
			$data = json_decode($json, true);
			$result = count($data);
			$movetype = 0;
			
			if($result == 0){
				exit("Se ha intentando enviar una solicitud vacía. Por favor, <u>Ingresar Datos para Continuar</u>");
			}

			foreach($data as $key){
				$LogTool = $key['LogTool'];
				$ToolNum = $key['ToolNum'];
				$ToolRequest = $key['ToolRequest'];
				$ToolArea = $key['ToolArea'];

				$check->bindParam(':toolID', $LogTool);
				$check->execute();

				while($row=$check->fetch(PDO::FETCH_ASSOC)){
					$dbID = $row['ID'];
					$dbHash = $row['ToolHash'];
					$dbName = $row['ToolName'];
					$dbLive = $row['ToolLive'];
					$dbStock = $row['ToolStock'];
					$dbHasTool = $row['HasTool'];
					$dbArea = $row['ToolArea'];
				}


				if($dbLive == 0){
					exit("La Herramienta <u>".$dbName."</u> se encuentra registrada como <u>Agotada</u> en Almacén. <br><br>Revisar 'Herramientas en Piso' y registros para confirmar esta información");
				}

				$newStock = $dbStock - $ToolNum;

				if($newStock < 0){
					exit("La solicitud de <u>".$dbName."</u> no pudo ser completada. <br><br>El almacén <u>no registra existencia suficiente</u> para el préstamo de las herramientas");
				}
				else{

					$log->bindParam(':logtool', $LogTool);
                    $log->bindParam(':logreq', $ToolRequest);
                    $log->bindParam(':logarea', $ToolArea);
                    $log->bindParam(':loggives', $username);
                    $log->bindParam(':logtime', $regdate);
                    $log->bindParam(':lognum', $ToolNum);
                    $log->bindParam(':logmove', $movetype);
                    $log->bindParam(':logstock', $newStock);
                    $log->execute();

					$live->bindParam(':newLive', $newStock);
                    $live->bindParam(':newHost', $ToolRequest);
                    $live->bindParam(':newID', $LogTool);
                    $live->execute();

					}
				}
			}

			echo 1;
		

	}
	catch(PDOException $e){
		$caught = $e->getMessage();
		echo $caught;
	}

}
else{
    header("location: ../../notfound.php");
}