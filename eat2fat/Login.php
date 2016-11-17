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
		$sql = "SELECT COUNT(*) AS count FROM tb_user WHERE userID = '" . $userID . "' AND userPassword = '" . $userPassword . "'";
		$result = mysqli_query($con, $sql);
		$out = $result->fetch_array(MYSQLI_ASSOC);
		if($out['count'] == "1")
		{
			$sql = "SELECT userRoleName FROM tb_user JOIN tb_user_role ON tb_user.userRoleID = tb_user_role.userRoleID WHERE userID = '" . $userID . "' AND userPassword = '" . $userPassword . "'";
			$result = mysqli_query($con, $sql);
			$out = $result->fetch_array(MYSQLI_ASSOC);
			echo $out['userRoleName'];
			session_start();
			$_SESSION['username'] = $userID;
			$_SESSION['userPassword'] = $userPassword;

			if($out['userRoleName'] == "Admin")
			{
				$adminID = $userID;
				$sql = "INSERT INTO tb_admin_log (adminLogNo, adminID, actionCode) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(al.adminLogNo) END + 1 FROM tb_admin_log al), '" . $adminID . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Login'))";
				$result = mysqli_query($con, $sql);
			}
			else
			{
				$sql = "INSERT INTO tb_user_log (userLogNo, userID, actionCode) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(ul.userLogNo) END + 1 FROM tb_user_log ul), '" . $userID . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Login'))";
				$result = mysqli_query($con, $sql);
			}
		}
		else
		{
			echo "false";
		}
	}
	mysqli_close($con);
?>