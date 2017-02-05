<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
///home/strikermx/upload/
session_start();
$uploaddir = '/var/www/html/dataset/txt/';
$conn = mysqli_connect('localhost','root','password','tagvisor');
$user=$_SESSION['login'];
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
$sql = "SELECT docname,docpath FROM doc WHERE docid = ".$_GET['docid'].";";
$result = mysqli_query($conn,$sql);
$_SESSION['docid']=$_GET['docid'];
$rs = $result->fetch_array(MYSQLI_ASSOC);
$_SESSION['docname']= $rs["docname"];
$_SESSION['docpath']=$rs['docpath'];
$uploadfile = $uploaddir . $rs["docname"] . ".txt";
//echo $uploadfile;
$exec = 'ls';
#$exec_pdfbox = 'java -jar /var/www/html/upload/pdfbox-app-2.0.3.jar ExtractText '.$uploadfile;
$exec_getjson = 'java -jar /var/www/html/upload/ParagraphSplitterDocx.jar '.$uploadfile;

	
	//echo "Connected successfully";
	

#$_SESSION['filename']=basename($_FILES['fileToUpload']['name']);
#echo $_SESSION['filename'];

/*echo '<pre>';
if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Can't upload!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);


echo "Can't upload!\n";

print "</pre>";
print_r($uploadfile);
print_r($txt);*/

//echo "\n-----------------\n";

$locale='en_US.UTF-8';
setlocale(LC_ALL,$locale);
putenv('LC_ALL='.$locale);
echo exec('locale charmap');

$old_path = getcwd();
//echo $exec_getjson;
//chdir('/my/path/');
#$output_pdfbox = shell_exec($exec_pdfbox);
//print_r($exec_pdfbox);
//echo "<pre>$output_pdfbox</pre>";
$output_json = exec($exec_getjson);
//print_r($exec_getjson);
//echo '<pre>'.$output_json.'</pre>';
//echo 'ภาษาไทย';
chdir($old_path);

$_SESSION['json']=$output_json;
#echo $_SESSION['json'];
$sql = "SELECT status FROM stats INNER JOIN users ON users.id = stats.userid WHERE users.username = '".$user."' AND stats.docid = ".$_GET["docid"].";";
$result = mysqli_query($conn,$sql);
$rs = $result->fetch_array(MYSQLI_ASSOC);
if($rs["status"]==0){
	header('Location: addtag2.php');
}
else 
{
	header('Location: edittag.php');
}

?>
