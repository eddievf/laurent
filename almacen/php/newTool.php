<?php
session_start();
if(!empty($_SESSION['logged']))
{
	$config = include("../db/config.php");
	$conn = new PDO($config["db"], $config["username"], $config["password"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	date_default_timezone_set("America/Monterrey");
	$regdate = date("Y-m-d H:i:s");

	$loggives = $_SESSION['user'];

	function productKey($length, $keyspace = 'abcdefghijklmnopqrstuvwxyz234567'){
			$str = '';
			$keysize = strlen($keyspace);
			
			for ($i=0; $i < $length ; $i++) { 
				$str .= $keyspace[\Sodium\randombytes_uniform($keysize)];
			}

			return $str;
		}

	try{

		$stmt = $conn->prepare("INSERT INTO stocktools (ToolHash, ToolName, ToolLive, ToolStock, ToolArea, ToolSerie)
								VALUES (:prodkey, :newName, :newLive, :newStock, :newArea, :newSerie) ");

		$check = $conn->prepare("SELECT ID, ToolArea
								 FROM stocktools
								 WHERE ToolHash = :newKey");

		$log = $conn -> prepare("INSERT INTO stocklogtools (LogTool, ToolRequest, ToolArea, ToolGives, ToolTime, ToolNum, ToolMoveType, RemainStock)
                                  VALUES (:logtool, :logreq, :logarea, :loggives, :logtime, :lognum, :logmove, :logstock) ");

		$ProdKey = base64_encode(productKey(6));	

		

		$ToolName = $_POST['newTool'];
		$ToolStock = $_POST['newToolStock'];
		$ToolLive = $ToolStock;
		$ToolSerieNum = $_POST['newToolSerieNum'];
		$ToolArea = $_POST['prodExitLog'];

		if($ToolStock <= 0){
			exit("Para poder resgistrar la Herramienta, se requiere que <u>al menos una pieza ingrese al almacén.</u>");
		}

		$stmt->bindParam(':prodkey', $ProdKey);
		$stmt->bindParam(':newName', $ToolName);
		$stmt->bindParam(':newLive', $ToolLive);
		$stmt->bindParam(':newStock', $ToolStock);
		$stmt->bindParam(':newArea', $ToolArea);
		$stmt->bindParam(':newSerie', $ToolSerieNum);
		$stmt->execute();

		$check->bindParam(':newKey', $ProdKey);
		$check->execute();

		while($row=$check->fetch(PDO::FETCH_ASSOC)){
			$dbID = $row['ID'];
			$dbArea = $row['ToolArea'];
		}

		$logreq = "NUEVO";
		$logmove = 1;

		$log->bindParam(':logtool', $dbID);
		$log->bindParam(':logreq', $logreq);
		$log->bindParam(':logarea', $dbArea);
		$log->bindParam(':loggives', $loggives);
		$log->bindParam(':logtime', $regdate);
		$log->bindParam(':lognum', $ToolLive);
		$log->bindParam(':logmove', $logmove);
		$log->bindParam(':logstock', $ToolStock);
		$log->execute();

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