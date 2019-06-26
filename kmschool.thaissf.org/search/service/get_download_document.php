<?php
	chdir('../dataset/raw');
	$filename = $_GET['filename'];
	foreach (glob($filename . "*") as $fullfilename) {
		header('Location: http://kmschool.thaissf.org/search/dataset/raw/'.$fullfilename);
	}
?>