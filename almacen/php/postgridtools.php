<?php
session_start();
if(!empty($_SESSION['logged']))
{
    try{
        date_default_timezone_set("America/Monterrey");
        $datetime = date("Y-m-d H:i:s");
        $username = $_SESSION['user'];
        $config = include("../db/config.php");
        $conn = new PDO($config["db"], $config["username"], $config["password"]);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $check = $conn -> prepare ("SELECT ID, ToolHash, ToolLive, ToolStock, HasTool, ToolArea
                                    FROM stocktools
                                    WHERE ToolHash = :hash");

        $notlast = $conn -> prepare("SELECT ToolHash, ToolRequest, ToolTime
                                     FROM stocktools, stocklogtools, departamentos
                                     WHERE ToolHash = :notlastHash
                                     AND stocklogtools.ToolRequest <> :userTool
                                     AND stocktools.ToolArea = departamentos.ID
                                     AND stocklogtools.ToolTime = (SELECT MAX(ToolTime)
                                                                   FROM stocklogtools
                                                                   WHERE stocklogtools.LogTool = stocktools.ID
                                                                   AND stocklogtools.ToolMoveType = 0
                                                                   AND ToolRequest <> :userTool)");

        $live = $conn -> prepare("UPDATE stocktools
                                  SET ToolLive = :newLive, HasTool = :newHost 
                                  WHERE ToolHash = :newHash");

      	$log = $conn -> prepare("INSERT INTO stocklogtools (LogTool, ToolRequest, ToolArea, ToolGives, ToolTime, ToolNum, ToolMoveType, RemainStock)
                                  VALUES (:logtool, :logreq, :logarea, :loggives, :logtime, :lognum, :logmove, :logstock) ");

      	if(isset($_POST['toolback'])){
            $json = $_POST['toolback'];
            $data = json_decode($json, true);
            $result = count($data);
            $movetype = 1;

            if($result == 0){
      			exit("Se intentó ingresar un registro vacio.");
      		}

                $ToolHash = $data['ToolHash'];
                $ToolName = $data['ToolName'];
                $ToolLive = $data['ToolLive'];
                $HasTool = $data['HasTool'];
                $ToolStock = $data['ToolStock'];
                $AreaName = $data['AreaName'];
                $ToolTime = $data['ToolTime'];

      		    $check->bindParam(':hash', $ToolHash);
      		    $check->execute();

      			while($row=$check->fetch(PDO::FETCH_ASSOC)){
                    $dbID = $row['ID'];
      				$dbHash = $row['ToolHash'];
                    $dbLive = $row['ToolLive'];
      				$dbStock = $row['ToolStock'];
                    $dbHasTool = $row['HasTool'];
                    $dbArea = $row['ToolArea'];
      			}

                $verifull = $dbLive + $ToolLive;

      			if(isset($dbHash)){
      				if($ToolLive > $dbStock){
      					$error_str = "¿Intentas ingresar herramientas de más? Verifica la información antes de continuar.";
      					exit($error_str);
      				}


                    if($ToolLive == $dbLive){
                        exit('2');
                    }

                    $verifull = $dbLive + $ToolLive;

                    if($verifull > $dbStock){
                        $error_str = "¿Intentas ingresar herramientas de más? Verifica la información antes de continuar.";
                        exit($error_str);
                    }

                    
                    if($verifull < $dbStock){
                        $notlast->bindParam(':notlastHash', $ToolHash);
                        $notlast->bindParam(':userTool', $HasTool);
                        $notlast->execute();

                        while($row=$check->fetch(PDO::FETCH_ASSOC)){
                            $stillUsing = $row['ToolRequest'];
                        }
                    }

                    if($verifull == $dbStock){
                        $stillusing = '';
                    }

                    $receives = 7;

                    $live->bindParam(':newLive', $verifull);
                    $live->bindParam(':newHost', $stillusing);
                    $live->bindParam(':newHash', $ToolHash);
                    $live->execute();

                    

                    $log->bindParam(':logtool', $dbID);
                    $log->bindParam(':logreq', $HasTool);
                    $log->bindParam(':logarea', $receives);
                    $log->bindParam(':loggives', $username);
                    $log->bindParam(':logtime', $datetime);
                    $log->bindParam(':lognum', $ToolLive);
                    $log->bindParam(':logmove', $movetype);
                    $log->bindParam(':logstock', $verifull);

                    $log->execute();


      			}



      		echo 1;
      		
        }
        else{
      		echo "Error, data not set";
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
