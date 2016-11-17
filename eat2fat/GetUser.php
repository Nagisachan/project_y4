<?php
	include("Connect.php");

	$con = mysqli_connect($host, $username, $password, $databaseName);

	if (mysqli_connect_errno())
	{
		echo "false";
	}
	else
	{
		session_start();
		$userID = mysqli_real_escape_string($con, $_SESSION['username']);
		$userPassword = mysqli_real_escape_string($con, $_SESSION['userPassword']);
		$sql = "SELECT COUNT(*) AS count FROM tb_user JOIN tb_user_role ON tb_user.userRoleID = tb_user_role.userRoleID WHERE userID = '" . $userID . "' AND userPassword = '" . $userPassword . "' AND userRoleName = 'Admin'";
		$result = mysqli_query($con, $sql);
		$out = $result->fetch_array(MYSQLI_ASSOC);

		if($out['count'] == "1")
		{
			$sql = "SELECT userID, userPassword, userEmail, userRoleName, registrationDate FROM tb_user u INNER JOIN tb_user_role ur ON u.userRoleID = ur.userRoleID ORDER BY userRoleName";
			$result = mysqli_query($con, $sql);
			$out = "[";
			while($rs = $result->fetch_array(MYSQLI_ASSOC))
			{
			    if ($out != "[")
			    {
			    	$out .= ",";
			    }
			    $out .= '{"userID":"' . $rs["userID"] . '",';
			    $out .= '"userPassword":"' . $rs["userPassword"] . '",';
			    $out .= '"userEmail":"' . $rs["userEmail"] . '",';
			    $out .= '"userRoleName":"'. $rs["userRoleName"] . '",';
			    $out .= '"registrationDate":"'. $rs["registrationDate"] . '"}';
			}
			$out .= "]";
			echo $out;
		}
		else
		{
			echo "false";
		}		
	}
	mysqli_close($con);
?>