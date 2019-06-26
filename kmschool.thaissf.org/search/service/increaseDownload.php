<?php
	#$file_id = 454;
	$file_name = $_POST['file_name'];
	
	include_once("../db.php");
	$query=mysqli_query($con,"SELECT download_time FROM file where file_name = '".$file_name."'");
    $row=mysqli_fetch_assoc($query);
	$download_time = $row['download_time'];
	$download_time = $download_time + 1;
	$updatequery = "UPDATE file SET download_time=".$download_time." WHERE file_name = '".$file_name."'";
	echo $updatequery;
	
	$query = mysqli_query($con,$updatequery);
	if($query) {
		echo "Record update successfully";
	}
	else{
		echo "nono";
	}
	echo $download_time;
?>