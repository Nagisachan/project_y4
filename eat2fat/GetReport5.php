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
			$date = $_GET['date'];

			$sql = "SELECT t.period, COUNT(t.period) AS numberOfPlayer FROM (SELECT CASE WHEN TIME(ul.dateTime) >= '00:00:00' AND TIME(ul.dateTime) <= '03:59:59' THEN '12:00 AM - 03:59 AM' WHEN TIME(ul.dateTime) >= '04:00:00' AND TIME(ul.dateTime) <= '07:59:59' THEN '04:00 AM - 07:59 AM'WHEN TIME(ul.dateTime) >= '08:00:00' AND TIME(ul.dateTime) <= '11:59:59' THEN '08:00 AM - 11:59 AM' WHEN TIME(ul.dateTime) >= '12:00:00' AND TIME(ul.dateTime) <= '15:59:59' THEN '12:00 PM - 03:59 PM' WHEN TIME(ul.dateTime) >= '16:00:00' AND TIME(ul.dateTime) <= '19:59:59' THEN '04:00 PM - 07:59 PM'WHEN TIME(ul.dateTime) >= '20:00:00' AND TIME(ul.dateTime) <= '23:59:59' THEN '08:00 PM - 11:59 PM' end AS period FROM tb_user_log ul LEFT JOIN tb_action_log al ON ul.actionCode = al.actionCode LEFT JOIN tb_user u ON ul.userID = u.userID LEFT JOIN tb_user_role ur ON u.userRoleID = ur.userRoleID WHERE actionName = 'Login' AND userRoleName = 'Player' AND DATE(ul.dateTime) LIKE '" . $date . "%') t GROUP BY t.period ORDER BY numberOfPlayer DESC";
			$result = mysqli_query($con, $sql);
			$totalNumberOfPlayer = 0;
			$out = "[";
			while($rs = $result->fetch_array(MYSQLI_ASSOC))
			{
			    if ($out != "[")
			    {
			    	$out .= ",";
			    }
			    $out .= '{"period":"' . $rs["period"] . '",';
			    $out .= '"numberOfPlayer":"'. $rs["numberOfPlayer"] . '"}';
			    $totalNumberOfPlayer += intval($rs["numberOfPlayer"]);
			}
			if($totalNumberOfPlayer != 0)
			{
				$out .= ',{"period":"Total number of player",';
				$out .= '"numberOfPlayer":"'. $totalNumberOfPlayer . '"}';
			}
			else
			{
				$out .= '{"period":"Total number of player",';
				$out .= '"numberOfPlayer":"'. $totalNumberOfPlayer . '"}';
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