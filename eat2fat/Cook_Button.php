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
		$maybeFoodID = array();

		if($out['count'] == "1")
		{
			$list = json_decode($_GET['list']);
			$method = mysqli_real_escape_string($con, $_GET['method']);
			$correctFoodID = "";

			$sql = "SELECT t.foodID FROM (SELECT foodID FROM tb_ingredient GROUP BY foodID HAVING COUNT(foodID) = ".count($list).") t LEFT JOIN tb_food f ON t.foodID = f.foodID LEFT JOIN tb_method m ON f.methodID = m.methodID WHERE methodName = '".$method."'";
			$result = mysqli_query($con, $sql);
			$i = 0;
			while($rs = $result->fetch_array(MYSQLI_ASSOC))
			{
				$maybeFoodID[$i] = $rs["foodID"];
				$i++;
			}

			$found = false;
			for($i=0 ; $i<count($maybeFoodID) && !($found) ; $i++)
			{
				$sql = "SELECT ingredientID FROM tb_ingredient WHERE foodID = '".$maybeFoodID[$i]."'";
				$result = mysqli_query($con, $sql);
				$maybeIngredient = array();
				$j = 0;
				while($rs = $result->fetch_array(MYSQLI_ASSOC)) 
				{
					$maybeIngredient[$j] = $rs["ingredientID"];
					$j++;
				}

				$correct = 0;
				$check = true;
				for($j=0 ; $j<count($list) && $check ; $j++)
				{
					$check = false;
					for($k=0 ; $k<count($list) && !($check) ; $k++)
					{
						if($maybeIngredient[$j] == $list[$k])
						{
							$correct += 1;
							$check = true;
						}
					}
				}

				if($correct == count($list))
				{
					$found = true;
					$correctFoodID = $maybeFoodID[$i];
				}
			}

			// Cook Successfully
			$cookSuccessfully = "Failed";
			if($found)
			{
				$sql = "SELECT COUNT(*) AS count FROM tb_player_store WHERE playerID = '" . $userID . "' AND foodID = '" . $correctFoodID . "'";
				$result = mysqli_query($con, $sql);
				$out = $result->fetch_array(MYSQLI_ASSOC);
				if($out['count'] == "1")
				{
					$sql = "UPDATE tb_player_store SET quantity = quantity + 1 WHERE playerID = '" . $userID . "' AND foodID = '" . $correctFoodID . "'";
					$result = mysqli_query($con, $sql);
				}
				else
				{
					$sql = "INSERT INTO tb_player_store VALUES ('" . $userID . "','" . $correctFoodID . "', 1)";
					$result = mysqli_query($con, $sql);
				}
				$cookSuccessfully = "Successed";
			}

			$sql = "INSERT INTO tb_user_log (userLogNo, userID, actionCode, foodID, quantity, description) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(ul.userLogNo) END + 1 FROM tb_user_log ul), '" . $userID . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Cook'), '" . $correctFoodID . "', 1, '" . $cookSuccessfully . "')";
			$result = mysqli_query($con, $sql);

			for($i=0 ; $i<count($list) ; $i++)
			{
				$sql = "UPDATE tb_player_store SET quantity = quantity - 1 WHERE playerID = '" . $userID . "' AND foodID = '" . $list[$i] . "'";
				$result = mysqli_query($con, $sql);

				$sql = "SELECT quantity FROM tb_player_store WHERE playerID = '" . $userID . "' AND foodID = '" . $list[$i] . "'";
				$result = mysqli_query($con, $sql);
				$out = $result->fetch_array(MYSQLI_ASSOC);
				if($out['quantity'] == "0")
				{
					$sql = "DELETE FROM tb_player_store WHERE playerID = '" . $userID . "' AND foodID = '" . $list[$i] . "'";
					$result = mysqli_query($con, $sql);
				}
			}
			
			if($cookSuccessfully == "Successed")
			{
				echo "successed";
			}
			else
			{
				echo "true";
			}
		}
		else
		{
			echo "false";
		}
	}
	mysqli_close($con);
?>

