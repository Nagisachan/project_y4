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
		$sql = "SELECT COUNT(userID) AS count FROM tb_user WHERE userID = '".$userID."';";
		$result = mysqli_query($con, $sql);
		$out = $result->fetch_array(MYSQLI_ASSOC);
		if($out['count'] == "0")
		{
			echo "true";
		}
		else
		{
			echo "false";
		}
	}
	mysqli_close($con);
?>