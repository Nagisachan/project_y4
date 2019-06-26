<?php
	include_once("../db.php");

    $page = $_POST['pageNo'];
	//$page = 1;
	$tag_receive = $_POST['tag_box'];
	//$tag_receive = array("33-1","33-2");
	$school_receive = $_POST['school_list'];
	//$school_receive = array("บ้านขอวิทยา");
	$free_search_receive = $_POST['free_search'];
	//$free_search_receive = "ทำงานด้วยเหตุผล";
	$order = $_POST['orderby'];
	//echo $order;
	$tag_search = "where ";
	$school_search = "where ";
	$free_search = "";
	
	$free_search = $free_search_receive;
	//var_dump ($school_receive);
	$text_split = array();
	$cmd = "echo ".$free_search." | nc 206.189.148.201 7002"; //FIXME:
	exec($cmd,$text_split);

	$word = explode(",", $text_split[0]);
	$countsplit = substr_count($text_split[0], ',') + 1;
	$splittext = array();
	$free_tag_array = array();
	
	for($i=0;$i<$countsplit;$i++){
		//echo $word[$i];
		$splittext[$i] = str_replace('"',"",$word[$i]);
		//echo $splittext[$i];
		$id = 0;
		$query=mysqli_query($con,"SELECT distinct tag_category from Map_Text where InputText LIKE '".$splittext[$i]."'");
		while($row=mysqli_fetch_assoc($query))
		{
			array_push($free_tag_array,$row['tag_category']);
		}
	}
	//echo count($free_tag_array);
	//echo $tag_receive;
	//echo $free_tag_array[0];
	if($tag_receive != NULL){
		for($i=0;$i<count($free_tag_array);$i++){
			if (!in_array($free_tag_array[$i], $tag_receive))
				array_push($tag_receive,$free_tag_array[$i]);
		}
	}
	else{
		$tag_receive = array();
		for($i=0;$i<count($free_tag_array);$i++){
			if (!in_array($free_tag_array[$i], $tag_receive))
				array_push($tag_receive,$free_tag_array[$i]);
		}
	}
	if(count($tag_receive)>0){ 
		for($i=0;$i < count($tag_receive);$i++){
			$tag_search = $tag_search."tag = '".$tag_receive[$i]."' or ";
		}
		$tag_search = substr($tag_search, 0, -3);
	}
	else{
		$tag_search = "";
	}
	if(count($school_receive)>0){ 
		for($i=0;$i < count($school_receive);$i++){
			$school_search = $school_search."id = '".$school_receive[$i]."' or ";
		}
		$school_search = substr($school_search, 0, -3);
	}
	else{
		$school_search = "";
	}
	
	$query = "select * from (select tag.file_id,tag.paragraph_id,GROUP_CONCAT(distinct tag_category_item.name), file.file_name, school, file_uploaded_date,file.download_time from tag inner join (select * from tag ".$tag_search.") as tag_result on tag.file_id = tag_result.file_id and tag.paragraph_id = tag_result.paragraph_id inner join tag_category_item on  CONCAT(tag_category_item.category_id, '-', tag_category_item.item) = tag.tag inner join file on tag.file_id = file.file_id group by file.download_time, tag.file_id, tag.paragraph_id,file_name,school,file_uploaded_date) as ab left join content on ab.file_id = content.file_id and ab.paragraph_id = content.paragraph_id left join school_all on ab.school = school_all.id ".$school_search." ".$order;
	
	//echo $query;
	
	$result=mysqli_query($con,$query);
	
	$rows_count = mysqli_num_rows($result);
	$rows_count_max = ceil($rows_count/10);
	$paragraph_content = array();
	$paragraph_list = array();
	$start_row = $page*10 - 10;
	$limit = 10;
	if($start_row/10 < $rows_count_max){
		for($i=0;$i<$start_row;$i++){
			$row = mysqli_fetch_assoc($result);
		}
		for($i=$start_row;$i<$start_row+$limit;$i++)
		{
			if(!$row = mysqli_fetch_assoc($result)){
				break;
			}
			$paragraph_content['content_id'] = $row['content_id'];
			$paragraph_content['file_name'] = $row['file_name'];
			$paragraph_content['paragraph_id'] = $row['paragraph_id'];
			$paragraph_content['tag'] = $row['GROUP_CONCAT(distinct tag_category_item.name)'];
			$paragraph_content['file_uploaded_date'] = $row['file_uploaded_date'];
			$paragraph_content['school'] = $row['school'];
			$paragraph_content['content'] = $row['content'];
			$paragraph_content['school_name'] = $row['name'];
			$paragraph_content['latitude'] = $row['latitude'];
			$paragraph_content['longitude'] = $row['longitude'];
			$paragraph_content['department'] = $row['department'];
			$paragraph_content['download_time'] = $row['download_time'];
			$paragraph_content['rows_count'] = $rows_count;
			// $tag_content['item'] = $row[6];
			//$tag_content['item_name'] = $row[7];
			//$tag_content['item_status'] = $row[8];
			//$category_name = $row[1];
			//$jsonData['category_name'][] = $category_name;
			$paragraph_list[] = $paragraph_content;
			
			//echo $id;
		
		}
		$encoded = json_encode($paragraph_list);
		$unescaped = preg_replace_callback('/\\\u(\w{4})/', function ($matches) {
			return html_entity_decode('&#x' . $matches[1] . ';', ENT_COMPAT, 'UTF-8');
		}, $encoded);
		echo $unescaped;
		//echo count($subCategory["ครูผู้สอน - พัฒนาครู"]);
		//echo $paragraph_list;
		//echo $query;
	}
	else{
		echo "Exceed Page";
	}
		
?>