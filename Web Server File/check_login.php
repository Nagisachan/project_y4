<?php
$lg_username =  $_GET['lg_username'];
#echo $lg_username;
$lg_password = $_GET['lg_password'];
#echo $lg_password;

$dsn = "DSN=Sample Cloudera Impala DSN 64;host=10.8.0.6;port=21050;database=autotag";

$conn = odbc_connect($dsn, '', '');
#print_r($conn);

$result = odbc_exec($conn, "select roletype from test_login2 WHERE id = '" . $lg_username . "' AND password = '" . $lg_password . "'");
echo "command : select roletype from test_login2 WHERE id = '" . $lg_username . "' AND password = '" . $lg_password . "'";

#echo "--result--";

while($row = odbc_fetch_array($result))
#print_r($row);

echo $row['roletype'];

if($lg_username == 'nxz')
{
	header('Location: http://52.163.225.136/expert.html');
	exit;
}
else
{
	header('Location: http://52.163.225.136/login.html');
	exit;
}

?>




