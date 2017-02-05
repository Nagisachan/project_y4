<?php
session_start();
$reg_username =  $_POST['reg_username'];
echo $reg_username;
$reg_password = $_POST['reg_password'];
echo $reg_password;
$reg_email = $_POST['reg_email'];
echo $reg_email;
$reg_fullname = $_POST['reg_fullname'];
echo $reg_fullname;


#$dsn = "DSN=Sample Cloudera Impala DSN 64;host=10.8.0.6;port=21050;database=autotag";
#$conn = odbc_connect($dsn, '', '');
#print_r($conn);
$conn = mysqli_connect('localhost','root','password','tagvisor');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

$sql = "SELECT username FROM users WHERE username = '".$reg_username."';";
$result = mysqli_query($conn,$sql);
$rs = $result->fetch_array(MYSQLI_ASSOC);

if($rs["username"] == $reg_username){
	header('Location: regis.php');
}
else{
	$sql2 = "INSERT INTO users (username,password,email,fullname) values ('".$reg_username."','".$reg_password."','".$reg_email."','".$reg_fullname."');";
	if (!mysqli_query($conn,$sql2))
	{
		die('Error: '.mysqli_error($conn));
	}
	$_SESSION['login']=$reg_username;
	header('Location: expert.php');
}
/*
$result = odbc_exec($conn, "insert roletype from test_login2 WHERE id = '" . $lg_username . "' AND password = '" . $lg_password . "';");
echo "command : insert roletype from test_login2 WHERE id = '" . $lg_username . "' AND password = '" . $lg_password . "';";

#echo "--result--";

while($row = odbc_fetch_array($result))
#print_r($row);
*/


?>





