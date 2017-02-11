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

		$add = $conn->prepare("INSERT INTO stockproducts (ConsumeHash, CommonName, Description, Unit, inStock, SafeStock, RestockLevel, ReqArea)
								VALUES (:prodkey, :prodName, :consumeDesc, :prodUnit, :prodStock, :prodSafe, :prodRestock, :prodArea) ");

		$update = $conn->prepare("UPDATE stockproducts
									SET inStock = :updateStock, RestockLevel = :updateLevel
									WHERE ID = :updateKey");

		$check = $conn->prepare("SELECT ID, ConsumeHash, inStock, SafeStock, RestockLevel 
								 FROM stockproducts
								 WHERE ID = :checkKey");

		$chash = $conn->prepare("SELECT ID, ConsumeHash, inStock, SafeStock, RestockLevel 
								 FROM stockproducts
								 WHERE ConsumeHash = :checkHash");

		$log = $conn -> prepare("INSERT INTO stocklog (LogProduct, WhoRequest, WhoArea, WhoGives, LogTime, NumMoved, LogUnit, MoveType, RemainStock)
                                  VALUES (:logprod, :logreq, :logarea, :loggives, :logtime, :lognum, :logunit, :logmove, :logstock) ");

		$addForPiece = $_POST['newConsumePiece'];
		$addDesc = $_POST['newConsumeDesc'];
		$addQuant = $_POST['newConsumeQuant'];
		$addUnit = $_POST['newConsumeUnit'];
		$addArea = $_POST['newToolArea'];
		$MoveType = 1;
		$logreq = "ENTRADA";

		if($addQuant == 0){
			exit("Se debe ingresar al menos 1 unidad de Producto para actualizar el registro");
		}

		$checkNew = is_numeric($addForPiece);

		if($checkNew){
			$check->bindParam(':checkKey', $addForPiece);
			$check->execute();

			while($row=$check->fetch(PDO::FETCH_ASSOC)){
				$dbID = $row['ID'];
				$dbStock = $row['inStock'];
				$dbHash = $row['ConsumeHash'];
				$dbSafe = $row['SafeStock'];
				$dbLevel = $row['RestockLevel'];
			}

			if(isset($dbID)){

				$newInStock = $addQuant + $dbStock;
			

				if($newInStock > $dbSafe){
					$newStockLevel = 0;
				}
				else{
					$newStockLevel = 1;
				}

				$update->bindParam(':updateStock', $newInStock);
				$update->bindParam(':updateLevel', $newStockLevel);
				$update->bindParam(':updateKey', $addForPiece);
				$update->execute();

				$log->bindParam(':logprod', $addForPiece);
				$log->bindParam(':logreq', $logreq);
				$log->bindParam(':logarea', $addArea);
				$log->bindParam(':loggives', $loggives);
				$log->bindParam(':logtime', $regdate);
				$log->bindParam(':lognum', $addQuant);
				$log->bindParam(':logunit', $addUnit);
				$log->bindParam(':logmove', $MoveType);
				$log->bindParam(':logstock', $newInStock);
				$log->execute();

				echo 1;
			}
			else{
				exit("Ha ocurrido un error al verificar el registro en la Base de Datos, revisar información");
			}

		} //end if Product Exists
		else{

			$defaultLevel = 0;

			$ProdKey = base64_encode(productKey(6));
			$add->bindParam(':prodkey', $ProdKey);
			$add->bindParam(':prodName', $addForPiece);
			$add->bindParam(':consumeDesc', $addDesc);
			$add->bindParam(':prodUnit', $addUnit);
			$add->bindParam(':prodStock', $addQuant);
			$add->bindParam(':prodSafe', $addQuant);
			$add->bindParam(':prodRestock', $defaultLevel);
			$add->bindParam(':prodArea', $addArea);
			$add->execute();


			$chash->bindParam(':checkHash', $ProdKey);
			$chash->execute();


			while($row=$chash->fetch(PDO::FETCH_ASSOC)){
				$dbID = $row['ID'];
				$dbStock = $row['inStock'];
				$dbHash = $row['ConsumeHash'];
				$dbSafe = $row['SafeStock'];
				$dbLevel = $row['RestockLevel'];
			}

			$log->bindParam(':logprod', $dbID);
			$log->bindParam(':logreq', $logreq);
			$log->bindParam(':logarea', $addArea);
			$log->bindParam(':loggives', $loggives);
			$log->bindParam(':logtime', $regdate);
			$log->bindParam(':lognum', $addQuant);
			$log->bindParam(':logunit', $addUnit);
			$log->bindParam(':logmove', $MoveType);
			$log->bindParam(':logstock', $dbStock);
			$log->execute();

			echo 1;
		}


	}
	catch(PDOException $e){
		$caught = $e->getMessage();
		echo $caught;
	}
}
else{
    header("location: ../../notfound.php");
}