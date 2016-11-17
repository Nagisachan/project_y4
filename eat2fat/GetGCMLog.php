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
		$sql = "SELECT COUNT(*) AS count FROM tb_user JOIN tb_user_role ON tb_user.userRoleID = tb_user_role.userRoleID WHERE userID = '" . $userID . "' AND userPassword = '" . $userPassword . "' AND (userRoleName = 'Admin' OR userRoleName = 'GCM')";
		$result = mysqli_query($con, $sql);
		$out = $result->fetch_array(MYSQLI_ASSOC);

		if($out['count'] == "1")
		{
			$sql = "SELECT userLogNo, ul.userID, dateTime, actionName, foodName, quantity, ul.description FROM tb_user_log ul LEFT JOIN tb_action_log acl ON ul.actionCode = acl.actionCode LEFT JOIN tb_food f ON ul.foodID = f.foodID LEFT JOIN tb_user u ON ul.userID = u.userID LEFT JOIN tb_user_role ur ON u.userRoleID = ur.userRoleID WHERE ur.userRoleName = 'GCM' ORDER BY userLogNo DESC";
			$result = mysqli_query($con, $sql);
			$out = "[";
			while($rs = $result->fetch_array(MYSQLI_ASSOC))
			{
			    if ($out != "[")
			    {
			    	$out .= ",";
			    }
			    $out .= '{"userLogNo":"' . $rs["userLogNo"] . '",';
			    $out .= '"userID":"' . $rs["userID"] . '",';
			    $out .= '"userLogDateTime":"' . $rs["dateTime"] . '",';
			    $out .= '"actionName":"' . $rs["actionName"] . '",';
			    $out .= '"foodName":"'. $rs["foodName"] . '",';
			    $out .= '"quantity":"'. $rs["quantity"] . '",';
			    $out .= '"description":"'. $rs["description"] . '"}';
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