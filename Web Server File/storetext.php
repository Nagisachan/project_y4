<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
///home/strikermx/upload/
session_start();
$uploaddir = '/var/www/html/upload/';
$storedir = '/var/www/html/text/';
$locale='en_US.UTF-8';
setlocale(LC_ALL,$locale);
putenv('LC_ALL='.$locale);
//echo exec('locale charmap');

$newline = '
';

$data = json_decode($_SESSION['json']);

//echo '<br />The ' . $_POST['submit'] . ' submit button was pressed<br />';

if($_POST['submit'] === 'SaveDraft'){
	$conn = mysqli_connect('localhost','root','password','tagvisor');
	$sql = "update stats set status=1 where userid = (select id from users where username = '".$_SESSION['login']."') AND docid = ".$_SESSION['docid'].";";
	$result = mysqli_query($conn,$sql);
}	
else if($_POST['submit'] === 'Submit'){
	$conn = mysqli_connect('localhost','root','password','tagvisor');
	$sql = "update stats set status=2 where userid = (select id from users where username = '".$_SESSION['login']."') AND docid = ".$_SESSION['docid'].";";
	$result = mysqli_query($conn,$sql);
}

$store_values = '{"paragraph":[';

//echo $store_values;

for( $i = 1 ; $i <= $data->count ; $i++){
	$postdata = "";
	$countdropdown = 0;
	$tags = $_POST['tags'.$i];
	//echo $tags;
	//echo "---";
	$countfreetags = substr_count($tags, ',') + 1; 
	$tags = str_replace(',','","',$tags);
	//echo $tags;
	//echo strcmp($tags,'""');
	
	foreach ($_POST['select'.$i] as $selected_option) {
		$countdropdown++;
		$postdata = $postdata.',"'.$selected_option.'"';
	}
	if ($countdropdown > 0){
		$postdata = substr($postdata, 1);
	}
	
	if($tags === ''){
		$countfreetags = $countfreetags - 1;
	}
	else{
		$tags = '"'.$tags.'"';	
	}
	
	$store_values = $store_values.'{"dropdowncount":'.$countdropdown++.',"freecount":'.$countfreetags.',"dropdowntag":['.$postdata.'],"freetag":['.$tags.']},'.$newline;
	
}

$store_values = substr($store_values, 0, -2);
$store_values = $store_values.']}';

//echo $store_values;

$file = $_SESSION['login'] . "-" .$_SESSION['docname'];
//$file = substr($file, 0, -3);
$file = $file.'.txt';

//echo $file;

$myfile = fopen($storedir.$file, "w") or die("Unable to open file!");
fwrite($myfile, $store_values);
fclose($myfile);
//echo $file;

//$datav = iconv("CP1257","UTF-8", $store);
//$datav = mb_convert_encoding($store,'UTF-8','OLD-ENCODING');

//file_put_contents($file,$store_values
if($_POST['submit'] === 'SaveDraft'){
	$message = "Update ข้อมูลเรียบร้อย";
	echo '<script language="javascript">alert("'.$message.'");document.location="edittag.php";</script>';
}
else if($_POST['submit'] === 'Submit'){
	$message = "จัดเก็บข้อมูลลงระบบเรียบร้อย";
	echo '<script language="javascript">alert("'.$message.'");document.location="expert_ez.php";</script>';
}

//sleep(5);
//header('Location: expert_ez.php');

?>
