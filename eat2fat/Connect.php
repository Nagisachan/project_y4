<?php
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF8");

	$host = "localhost";
	$username = "root";
	$password = "";
	$databaseName = "eat2fat";

	/*
	$host = "mysql.hostinger.co.uk";
	$username = "u570291279_e2f";
	$password = "cpe332kmutt";
	$databaseName = "u570291279_e2f";

	mysql_db_query($databaseName,"SET NAMES UTF8");

	$con = mysqli_connect($host, $username, $password, $databaseName);

	if (mysqli_connect_errno())
	{
		echo "false";
	}
	else
	{
		echo "true";
	}
	*/
?>