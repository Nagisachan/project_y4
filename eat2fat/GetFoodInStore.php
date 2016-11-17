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
			$sql = "SELECT t1.foodID, f.foodName, f.foodPrice, f.carbohydrate, f.protein, f.fat, f.vitamin, t1.quantity, f.maxQuantity FROM tb_food f RIGHT JOIN (SELECT foodID, quantity FROM tb_player_store WHERE playerID = '" . $userID . "') t1 ON f.foodID = t1.foodID";
			$result = mysqli_query($con, $sql);
			$out = "[";
			while($rs = $result->fetch_array(MYSQLI_ASSOC))
			{
			    if($out != "[")
			    {
			    	$out .= ",";
			    }
				$out .= '{"foodID":"' . $rs["foodID"] . '",';
			    $out .= '"foodName":"' . $rs["foodName"] . '",';
			    $out .= '"foodPrice":"' . $rs["foodPrice"] . '",';
			    $out .= '"carbohydrate":"' . $rs["carbohydrate"] . '",';
			    $out .= '"protein":"'. $rs["protein"] . '",';
				$out .= '"fat":"'. $rs["fat"] . '",';
				$out .= '"vitamin":"'. $rs["vitamin"] . '",';
			    $out .= '"quantity":"'. $rs["quantity"] . '",';
				$out .= '"maxQuantity":"'. $rs["maxQuantity"] . '"}';
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