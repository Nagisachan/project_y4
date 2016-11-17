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
			$foodID = mysqli_real_escape_string($con, $_GET['foodID']);
			$quantity = mysqli_real_escape_string($con, $_GET['quantity']);

			$sql = "UPDATE tb_player_store SET quantity = quantity - " . $quantity . " WHERE playerID = '" . $userID . "' AND foodID = '" . $foodID . "'";
			$result = mysqli_query($con, $sql);

			$sql = "DELETE FROM tb_player_store WHERE playerID = '" . $userID . "' AND foodID = '" . $foodID . "' AND quantity = 0";
			$result = mysqli_query($con, $sql);

			$sql = "INSERT INTO tb_user_log (userLogNo, userID, actionCode, foodID, quantity) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(ul.userLogNo) END + 1 FROM tb_user_log ul), '" . $userID . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Eat'), '" . $foodID . "', " . $quantity . ")";
			$result = mysqli_query($con, $sql);

			$sql = "UPDATE tb_player_information SET playerWeight = playerWeight + (" . $quantity . " * (0.35 * (SELECT carbohydrate FROM tb_food WHERE foodID = '" . $foodID . "') + 0.15 * (SELECT protein FROM tb_food WHERE foodID = '" . $foodID . "') + 0.45 * (SELECT fat FROM tb_food WHERE foodID='" . $foodID . "') + 0.05 * (SELECT vitamin FROM tb_food WHERE foodID = '" . $foodID . "'))) WHERE playerID = '" . $userID . "'";
			$result = mysqli_query($con, $sql);
		}
		else
		{
			echo "false";
		}
	}
	mysqli_close($con);
?>

