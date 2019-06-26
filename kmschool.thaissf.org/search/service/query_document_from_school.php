

<?php
	$school_id = $_POST['school_id'];
	//$school_id = 1091560018;
	$file_content = array();
	$jsonData = array();
	
	include_once("../db.php");
	$query=mysqli_query($con,"select file.file_id,GROUP_CONCAT(distinct tag_category_item.name) as alltag, file.file_name FROM file INNER JOIN tag ON file.file_id = tag.file_id INNER JOIN tag_category_item ON CONCAT(tag_category_item.category_id, '-', tag_category_item.item) = tag.tag WHERE file.school =  '".$school_id."' group by file.file_id");
	
	#while($row = pg_fetch_row($result))
	#while($row=mysql_fetch_assoc($query))
	while($row=mysqli_fetch_assoc($query))
		{
			//echo "12";
			$file_content['school_id'] = $school_id;
			$file_content['file_id'] = $row['file_id'];
			$file_content['tag'] = $row['alltag'];
			$file_content['file_name'] = $row['file_name'];
			$jsonData[] = $file_content;
			//echo $row['alltag'];
		}
		
	//echo json_encode($jsonData);
	$encoded = json_encode($jsonData);
	$unescaped = preg_replace_callback('/\\\u(\w{4})/', function ($matches) {
		return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
	}, $encoded);
	echo $unescaped;
?>