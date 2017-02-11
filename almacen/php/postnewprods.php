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
		$check = $conn -> prepare("SELECT CommonName, inStock, SafeStock, RestockLevel, Unit
									FROM stockproducts
									WHERE ID = :IDprod");

		$upLive = $conn -> prepare("UPDATE stockproducts
									SET inStock = :newStock, RestockLevel = :newLevel
									WHERE ID = :IDprod");

		$upLog = $conn ->prepare("INSERT INTO stocklog(LogProduct, WhoRequest, WhoArea, WhoGives, LogTime, NumMoved, LogUnit, MoveType, RemainStock)
									VALUES (:product, :whorequest, :whoarea, :whogives, :logtime, :num, :unit, :movetype, :remain)");

		if(isset($_POST['exitprod'])){
			$json = $_POST['exitprod'];
			$data = json_decode($json, true);
			$result = count($data);
			$movetype = 0;
			$somethingHappened = 0;

			if($result == 0){
				exit("Se ha intentando enviar una solicitud vacía. Por favor, <u>Ingresar Datos para Continuar</u>");
			}

			foreach($data as $key){
				$product = $key["LogProduct"];
				$quantity = $key["NumMoved"];
				$whorequest = $key["WhoRequest"];
				$whoarea = $key["WhoArea"];
				$unit = $key["Unit"];

				$check->bindParam(':IDprod', $product);
				$check->execute();

				while($row=$check->fetch(PDO::FETCH_ASSOC)){
					$prodName = $row['CommonName'];
					$prodStock = $row['inStock'];
					$prodSafe = $row['SafeStock'];
					$prodLevel = $row['RestockLevel'];
					$prodUnit = $row['Unit'];
				}

				if($unit != $prodUnit){
					exit("Revisar Unidad de Medicion para el Producto seleccionado. No coincide con el registrado en la base de datos");
				}

				if($prodStock == 0){
					exit("El producto <u>".$prodName."</u> se encuentra registrado como <u>Agotado</u>. <br><br>Revisar almacén y entradas de material para confirmar esta información");
				}
				else{

					$newStock = $prodStock - $quantity;

					if($newStock < 0){
						exit("La solicitud de ".$prodName." no pudo ser completada, ya que el almacén no registra existencia suficiente para cumplir con todas las piezas");
					}
					else{

						if($newStock <= $prodSafe){
							$newLevel = 1;
						}
						else{
							$newLevel = 0;
						}

						$upLog->bindParam(':product', $product);
						$upLog->bindParam(':whorequest', $whorequest);
						$upLog->bindParam(':whoarea', $whoarea);
						$upLog->bindParam(':whogives', $username);
						$upLog->bindParam(':logtime', $regdate);
						$upLog->bindParam(':num', $quantity);
						$upLog->bindParam(':unit', $unit);
						$upLog->bindParam(':movetype', $movetype);
						$upLog->bindParam(':remain', $newStock);
						$upLog->execute();

						$upLive->bindParam(':newStock', $newStock);
						$upLive->bindParam(':newLevel', $newLevel);
						$upLive->bindParam(':IDprod', $product);
						$upLive->execute();

						if($newLevel == 1){
							$somethingHappened = 1;
						}

					}
				}
			}

			if($somethingHappened == 1){
				echo 2;
			}
			else{
				echo 1;
			}
		
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