<?php
	$school_id = $_POST['school_id'];
	$school_content = array();
	$jsonData = array();
	
	include_once('../db.php');
	$query=mysqli_query($con,"SELECT * FROM school_all where id = '".$school_id."'");
    $row=mysqli_fetch_assoc($query);
		
	$school_content['id'] = $row['id'];
	$school_content['name'] = $row['name'];
	$school_content['subdistrict'] = $row['subdistrict'];
	$school_content['district'] = $row['district'];
	$school_content['province'] = $row['province'];
	$school_content['postcode'] = $row['postcode'];
	$school_content['type'] = $row['type'];
	$school_content['department'] = $row['department'];
	$school_content['telephone'] = $row['telephone'];
	$school_content['fax'] = $row['fax'];
	$school_content['website'] = $row['website'];
	$school_content['email'] = $row['email'];
	$school_content['latitude'] = $row['latitude'];
	$school_content['longtitude'] = $row['longtitude'];
	$jsonData[] = $school_content;
		
	$encoded = json_encode($jsonData);
	$unescaped = preg_replace_callback('/\\\u(\w{4})/', function ($matches) {
		return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
	}, $encoded);
	echo $unescaped;
?>