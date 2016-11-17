<?php
	include("Connect.php");

	$con = mysqli_connect($host, $username, $password, $databaseName);
	if (mysqli_connect_errno())
	{
		echo "false";
	}
	else
	{
		$userEmail = mysqli_real_escape_string($con, $_GET['email']);
		$sql = "SELECT COUNT(userEmail) AS count FROM tb_user WHERE userEmail = '".$userEmail."';";
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