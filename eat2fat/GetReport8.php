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
			$playerID = mysqli_real_escape_string($con, $_GET['playerID']);

			$sql = "SELECT t.foodID, t.foodName, SUM(t.quantity) as sumQuantity FROM (SELECT ul.foodID, f.foodName, ul.quantity FROM tb_user_log ul LEFT JOIN tb_action_log al ON ul.actionCode = al.actionCode INNER JOIN tb_food f ON ul.foodID = f.foodID WHERE actionName = 'Eat' AND ul.userID = '" . $playerID . "') t GROUP BY t.foodID ORDER BY sumQuantity DESC";
			$result = mysqli_query($con, $sql);
			$out = "[";
			while($rs = $result->fetch_array(MYSQLI_ASSOC))
			{
			    if ($out != "[")
			    {
			    	$out .= ",";
			    }
			    $out .= '{"foodID":"' . $rs["foodID"] . '",';
			    $out .= '"foodName":"' . $rs["foodName"] . '",';
			    $out .= '"quantity":"'. $rs["sumQuantity"] . '"}';
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