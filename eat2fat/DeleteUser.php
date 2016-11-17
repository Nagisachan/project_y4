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
			$userID = mysqli_real_escape_string($con, $_GET['userID']);

			$sql = "DELETE FROM tb_user_log WHERE userID = '".$userID."'; ";
			$result = mysqli_query($con, $sql);
			$sql = "DELETE FROM tb_admin_log WHERE adminID = '".$userID."'; ";
			$result = mysqli_query($con, $sql);
			$sql = "DELETE FROM tb_player_store WHERE playerID = '".$userID."'; ";
			$result = mysqli_query($con, $sql);
			$sql = "DELETE FROM tb_player_information WHERE playerID = '".$userID."'; ";
			$result = mysqli_query($con, $sql);
			$sql = "DELETE FROM tb_user WHERE userID = '".$userID."'; ";
			if (mysqli_query($con, $sql))
			{
				$adminID = mysqli_real_escape_string($con, $_SESSION['username']);
				$sql = "INSERT INTO tb_admin_log (adminLogNo, adminID, actionCode, description) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(al.adminLogNo) END + 1 FROM tb_admin_log al), '" . $adminID . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Delete'), '" . $userID . "')";
				$result = mysqli_query($con, $sql);
				echo "true";
			}
			else
			{
				echo "false";
			}
		}
		else
		{
			echo "false";
		}		
	}
	mysqli_close($con);
?>