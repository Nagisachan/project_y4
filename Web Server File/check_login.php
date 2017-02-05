<?php
session_start();
$lg_username =  $_POST['lg_username'];
echo $lg_username;
$lg_password = $_POST['lg_password'];
echo $lg_password;

#echo "session!";
#echo $_SESSION['login'];

//$dsn = "DSN=Sample Cloudera Impala DSN 64;host=10.8.0.6;port=21050;database=autotag";
//$dsn = "DSN=Sample Cloudera Impala DSN 64;host=13.76.160.188;port=21050;database=tagvisor";

//$conn = odbc_connect($dsn, '', '');
//print_r($conn);
/*

$result = odbc_exec($conn, "select username from users WHERE username = '" . $lg_username . "' AND password = '" . $lg_password . "';");
echo "command : select username from users WHERE username = '" . $lg_username . "' AND password = '" . $lg_password . "';";

echo "--result--";

while($row = odbc_fetch_array($result)){
	print_r($row);
	
}

*/

$conn = mysqli_connect('localhost','root','password','tagvisor');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

$sql = "SELECT password FROM users WHERE username = '".$lg_username."';";
$result = mysqli_query($conn,$sql);
$rs = $result->fetch_array(MYSQLI_ASSOC);

if($rs["password"] == $lg_password){
	$_SESSION['login']=$lg_username;
	header('Location: expert_ez.php');
}
else{
	header('Location: login.php');
}


/*if($_SESSION['login'] == 'nxz')
{
	header('Location: expert.php');
}
else
{
	header('Location: login.php');
}*/

?>





