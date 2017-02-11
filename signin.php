<?php
	session_start();
	error_reporting(0);

	$salt = '$argon2i$v=19$m=32768,t=4,p=1';

	$config = include("db/config.php");
    $conn = new PDO($config["db"], $config["username"], $config["password"]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	

	$auth = $conn->prepare('SELECT userID,username,userpass, clearsec
						  	   FROM sysusers
							   WHERE username = :user');

	$username = $_POST['login_username'];
	$username = strtolower($username);
	$userpass = $_POST['login_password'];

	$auth->bindParam(':user', $username);
	$auth->execute();

	while($row=$auth->fetch(PDO::FETCH_ASSOC)){
		$authuser = $row['username'];
		$authhash = $row['userpass'];
		$authsec = $row['clearsec'];
	}

	$username = strtolower($username);
	$hash_str = $salt.$authhash;

	if(\Sodium\crypto_pwhash_str_verify($hash_str, $userpass)){
		\Sodium\memzero($userpass);

		$_SESSION['logged'] = true;
		$_SESSION['user'] = $username;
		$_SESSION['clearsec'] = $authsec;

		echo 1;

	}

	else{
		\Sodium\memzero($userpass);
		echo 0;
	}

?>