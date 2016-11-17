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
		$sql = "SELECT COUNT(*) AS count FROM tb_user JOIN tb_user_role ON tb_user.userRoleID = tb_user_role.userRoleID WHERE userID = '" . $userID . "' AND userPassword = '" . $userPassword . "' AND userRoleName = 'GCM'";
		$result = mysqli_query($con, $sql);
		$out = $result->fetch_array(MYSQLI_ASSOC);

		if($out['count'] == "1")
		{
			$methodID = mysqli_real_escape_string($con, $_GET['methodID']);
			$changeTo = mysqli_real_escape_string($con, $_GET['changeTo']);
			$sql = "UPDATE tb_method SET methodName = '".$changeTo."' WHERE methodID = '".$methodID."'";
			if (mysqli_query($con, $sql))
			{
				$GCMID = mysqli_real_escape_string($con, $_SESSION['username']);
				$sql = "INSERT INTO tb_user_log (userLogNo, userID, actionCode, description) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(ul.userLogNo) END + 1 FROM tb_user_log ul), '" . $GCMID . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Edit'), '".$methodID." Name = " . $changeTo . "')";
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