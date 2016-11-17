<?php
	include("Connect.php");

	$con = mysqli_connect($host, $username, $password, $databaseName);
	if (mysqli_connect_errno())
	{
		echo "false";
	}
	else
	{
		$userID = mysqli_real_escape_string($con, $_GET['username']);
		$userPassword = mysqli_real_escape_string($con, $_GET['password']);
		$userEmail = mysqli_real_escape_string($con, $_GET['email']);
		$sql = "INSERT INTO tb_user (userID, userPassword, userEmail, userRoleID) VALUES('".$userID."', '".$userPassword."', '".$userEmail."', (SELECT userRoleID FROM tb_user_role WHERE userRoleName = 'Player'));";
		if (mysqli_query($con, $sql))
		{
			$sql = "INSERT INTO tb_player_information VALUES('".$userID."', 5, 500)";
			$result = mysqli_query($con, $sql);
			echo "true";
		}
		else
		{
			echo "false";
		}
	}
	mysqli_close($con);
?>