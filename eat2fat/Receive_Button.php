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
			// Find now time
			$sql = "SELECT NOW() AS nowTime";
			$result = mysqli_query($con, $sql);
			$out = $result->fetch_array(MYSQLI_ASSOC);
			$nowTime = $out['nowTime'];
			// echo $nowTime."\n";

			// Find last receive time
			$sql = "SELECT MAX(dateTime) AS lastReceive FROM tb_user_log ul LEFT JOIN tb_action_log al ON ul.actionCode = al.actionCode WHERE userID = '".$userID."' AND actionName = 'Receive'";
			$result = mysqli_query($con, $sql);
			$out = $result->fetch_array(MYSQLI_ASSOC);
			if($out['lastReceive'] != NULL)
			{
				$lastReceive = $out['lastReceive'];
			}
			else
			{
				$sql = "SELECT registrationDate FROM tb_user WHERE userID = '".$userID."'";
				$result = mysqli_query($con, $sql);
				$out = $result->fetch_array(MYSQLI_ASSOC);
				$lastReceive = $out['registrationDate'];
			}
			// echo $lastReceive."\n";			

			// Update lastReceive in user log
			$sql = "INSERT INTO tb_user_log (userLogNo, userID, dateTime, actionCode) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(ul.userLogNo) END + 1 FROM tb_user_log ul), '" . $userID . "', '" . $nowTime . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Receive'))";
			$result = mysqli_query($con, $sql);

			// Find root foodID 
			$sql = "SELECT f.foodID, ROUND((TIMESTAMPDIFF(SECOND, '".$lastReceive."', '".$nowTime."'))/f.coolDown) AS receiveQuantity FROM tb_food f INNER JOIN tb_level_ingredient li ON f.levelIngredientID = li.levelIngredientID WHERE levelIngredientName = 'Root'";
			$result = mysqli_query($con, $sql);
			while($rs = $result->fetch_array(MYSQLI_ASSOC))
			{
				$sql = "SELECT COUNT(*) AS count FROM tb_player_store WHERE playerID = '" . $userID . "' AND foodID = '" . $rs["foodID"] . "'";
				$checkFoodID = mysqli_query($con, $sql);
				$out = $checkFoodID->fetch_array(MYSQLI_ASSOC);

				if($out['count'] == "1")
				{
					$sql = "UPDATE tb_player_store SET quantity = (CASE WHEN quantity + " . $rs["receiveQuantity"] . " > (SELECT maxQuantity FROM tb_food WHERE foodID = '".$rs["foodID"]."') THEN (SELECT maxQuantity FROM tb_food WHERE foodID = '".$rs["foodID"]."') ELSE quantity + " . $rs["receiveQuantity"] . " END) WHERE playerID = '" . $userID . "' AND foodID = '" . $rs["foodID"] . "'";
					$resultReceive = mysqli_query($con, $sql);
				}
				else
				{
					$sql = "INSERT INTO tb_player_store VALUES('" . $userID . "', '" . $rs["foodID"] . "', (CASE WHEN " . $rs["receiveQuantity"] . " > (SELECT maxQuantity FROM tb_food WHERE foodID = '".$rs["foodID"]."') THEN (SELECT maxQuantity FROM tb_food WHERE foodID = '".$rs["foodID"]."') ELSE " . $rs["receiveQuantity"] . " END))";
					$resultReceive = mysqli_query($con, $sql);
				}

				$sql = "SELECT foodName FROM tb_food WHERE foodID = '" . $rs["foodID"] . "'";
				$foodName = mysqli_query($con, $sql);
				$out = $foodName->fetch_array(MYSQLI_ASSOC);
				echo $out["foodName"]." = ".$rs["receiveQuantity"]."\n";
			}
			//echo "true";
		}
		else
		{
			echo "false";
		}
	}
	mysqli_close($con);
?>

