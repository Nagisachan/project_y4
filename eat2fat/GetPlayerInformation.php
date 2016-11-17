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
		$sql = "SELECT COUNT(*) AS count FROM tb_user JOIN tb_user_role ON tb_user.userRoleID = tb_user_role.userRoleID WHERE userID = '" . $userID . "' AND userPassword = '" . $userPassword . "' AND userRoleName = 'Player'";
		$result = mysqli_query($con, $sql);
		$out = $result->fetch_array(MYSQLI_ASSOC);

		if($out['count'] == "1")
		{
			$sql = "SELECT playerID, playerWeight, playerMoney FROM tb_player_information JOIN tb_user ON playerID = userID WHERE playerID = '" . $userID . "'";
			$result = mysqli_query($con, $sql);
			$rs = $result->fetch_array(MYSQLI_ASSOC);
			$out = "[";  
			$out .= '{"playerID":"' . $rs["playerID"] . '",';
			$out .= '"playerWeight":"' . $rs["playerWeight"] . '",';
			$out .= '"playerMoney":"' . $rs["playerMoney"]. '"}';
			$out .="]";
			echo $out;
		}
		else
		{
			echo "false";
		}
	}
	mysqli_close($con);
?>