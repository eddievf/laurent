<?php
session_start();
if(!empty($_SESSION['logged']))
{
	$config = include("../db/config.php");
	$conn = new PDO($config["db"], $config["username"], $config["password"]);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


	try{
    	$stmt = $conn->prepare("SELECT stockproducts.ID AS id, CommonName AS text
							FROM stockproducts
							ORDER BY ID");
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
?>