<?php
session_start();
if(!empty($_SESSION['logged']))
{
	$config = include("../db/config.php");
	$conn = new PDO($config["db"], $config["username"], $config["password"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	try{

		$query = "SELECT ToolHash, ToolName, ToolLive, HasTool, ToolStock, AreaName, stocklogtools.ToolTime
			FROM stocktools, stocklogtools, departamentos
			WHERE HasTool <> ''
			AND stocklogtools.ToolArea = departamentos.ID
			AND stocklogtools.ToolTime = (SELECT MAX(ToolTime)
								  FROM stocklogtools
								  WHERE stocklogtools.LogTool = stocktools.ID)";

    	$stmt = $conn->prepare($query);
    	$stmt->execute();

    	$results = $stmt->fetchALL(PDO::FETCH_ASSOC);


    	$json = json_encode($results);

    	echo $json;
	}
	catch (PDOException $e){
		echo "
		<div class= 'alert alert-danger'><strong>[ERROR]</strong> :: ".$e->getMessage()."</div>";
	}
}
else{
    header("location: ../../notfound.php");
}
