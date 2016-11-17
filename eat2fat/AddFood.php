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
			$foodID1 = $con->query("SELECT MAX(foodID) AS fd FROM tb_food");
			$foodID2 = "F";
			while($rs = $foodID1->fetch_array(MYSQLI_ASSOC))
			{
				$methodID2 = intval(substr($rs["fd"], 1)) + 1;
				$methodID3 = (string)$methodID2;
				
				$size = strlen ($methodID3);	
				if( $size == 1 )
				{
					$foodID2 = $foodID2 . "000" . $methodID3;	
				}
				else if($size == 2)
				{
					$foodID2 = $foodID2 . "00" . $methodID3;	
				}
				else if($size == 3)
				{
					$foodID2 = $foodID2 . "0" . $methodID3;	
				}
				else
				{
					$foodID2 = $foodID2 . $methodID3;
				}
			}

			$food_name 				= mysqli_real_escape_string($con, $_GET['foodName']); 
			$description			= mysqli_real_escape_string($con, $_GET['foodDescription']);
			$levelIngredientName	= mysqli_real_escape_string($con, $_GET['levelIngredient']);
			$method_name			= mysqli_real_escape_string($con, $_GET['method']);
			$food_price				= mysqli_real_escape_string($con, $_GET['foodPrice']);
			$carbohydrate			= mysqli_real_escape_string($con, $_GET['carbohydrate']);
			$protein				= mysqli_real_escape_string($con, $_GET['protein']);
			$fat					= mysqli_real_escape_string($con, $_GET['fat']);
			$vitamin				= mysqli_real_escape_string($con, $_GET['vitamin']);
			$coolDown				= mysqli_real_escape_string($con, $_GET['coolDown']);
			$weightForUnlock		= mysqli_real_escape_string($con, $_GET['weightForUnlock']);
			$maxQuantity			= mysqli_real_escape_string($con, $_GET['maxQuantity']);
			$list					= json_decode($_GET['list']);

			if(!(count($list) == 0 && $levelIngredientName != "Root"))
			{
				$levelIngredient2 = $con->query("SELECT levelIngredientID FROM tb_level_ingredient WHERE levelIngredientName ='" . $levelIngredientName . "'");
				while($li = $levelIngredient2->fetch_array(MYSQLI_ASSOC))
				{
					$liID = $li["levelIngredientID"];
				}

				$method_sql = $con->query("SELECT methodID FROM tb_method WHERE methodName ='" . $method_name . "'");
				while($mt = $method_sql->fetch_array(MYSQLI_ASSOC))
				{
					$methodIDADD = $mt["methodID"];
				}
				
				if($levelIngredientName == "Root")
				{
					$sql = "INSERT INTO tb_food(foodID, foodName, foodDescription, levelIngredientID, foodPrice, carbohydrate, protein, fat, vitamin, coolDown, weightForUnlock, maxQuantity) VALUES('".$foodID2."','".$food_name."','".$description."','".$liID."','".$food_price."','".$carbohydrate."','".$protein."','".$fat."','".$vitamin."','".$coolDown."','".$weightForUnlock."','".$maxQuantity."')";
				}
				else
				{
					$sql = "INSERT INTO tb_food VALUES('".$foodID2."','".$food_name."','".$description."','".$liID."','".$methodIDADD."','".$food_price."','".$carbohydrate."','".$protein."','".$fat."','".$vitamin."','".$coolDown."','".$weightForUnlock."','".$maxQuantity."')";
				}

				if (mysqli_query($con, $sql))
				{
					if($levelIngredientName != "Root")
					{
						for($i=0 ; $i<count($list) ; $i++)
						{ 
							$sql = "INSERT INTO tb_ingredient VALUES('" . $foodID2 . "','" . $list[$i] . "')";
							$result = mysqli_query($con, $sql);
						}
						
					}

					$GCMID = mysqli_real_escape_string($con, $_SESSION['username']);
					$sql = "INSERT INTO tb_user_log (userLogNo, userID, actionCode, foodID) VALUES((SELECT CASE COUNT(*) WHEN 0 THEN 0 ELSE MAX(ul.userLogNo) END + 1 FROM tb_user_log ul), '" . $GCMID . "', (SELECT actionCode FROM tb_action_log WHERE actionName = 'Add'), '" . $foodID2 . "')";
					$result = mysqli_query($con, $sql);

					echo "true";
				}
				else
				{
					echo "false";
				}
			}
		}
		else
		{
			echo "false";
		}		
	}
	mysqli_close($con);








	
	



?>