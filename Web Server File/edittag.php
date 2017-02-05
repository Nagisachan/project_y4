<?php
// Start the session
session_start();
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<title><?php echo $_SESSION['docname']; ?></title>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="http://getbootstrap.com/dist/js/bootstrap.min.js"></script>
<script src="jquery.tagsinput.js"></script>
<link rel="stylesheet" type="text/css" href="jquery.tagsinput.css" />
<link rel="stylesheet" href="bootstrap-tagsinput.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rainbow/1.2.0/themes/github.css">
<link rel="stylesheet" href="app.css">



<style>
body,h1 {font-family: "Raleway", Arial, sans-serif}
h1 {letter-spacing:7px}
.menu {position: absolute;top: 25px;right: 25px;}

#save {
    position: fixed;
	top: 45%;
	left: 85%;
}
#submit {
    position: fixed;
	top: 50%;
	left: 85%;
}


#custom-search-input{
    padding: 3px;
    border: solid 1px #E4E4E4;
    border-radius: 6px;
    background-color: #fff;
    position: absolute;
    top: 50%;
    left: 50%;
    right: -60%;
}

#custom-search-input input{
    border: 0;
    box-shadow: none;
}

#custom-search-input button{
    margin: 2px 0 0 0;
    background: none;
    box-shadow: none;
    border: 0;
    color: #666666;
    padding: 0 8px 0 10px;
    border-left: solid 1px #ccc;
}

#custom-search-input button:hover{
    border: 0;
    box-shadow: none;
    border-left: solid 1px #ccc;
}

#custom-search-input .glyphicon-search{
    font-size: 23px;
}
.panel-table .panel-body{
  padding:0;
}

.panel-table .panel-body .table-bordered{
  border-style: none;
  margin:0;
}

.panel-table .panel-body .table-bordered > thead > tr > th:first-of-type {
    text-align:center;
    width: 100px;
}

.panel-table .panel-body .table-bordered > thead > tr > th:last-of-type,
.panel-table .panel-body .table-bordered > tbody > tr > td:last-of-type {
  border-right: 0px;
}

.panel-table .panel-body .table-bordered > thead > tr > th:first-of-type,
.panel-table .panel-body .table-bordered > tbody > tr > td:first-of-type {
  border-left: 0px;
}

.panel-table .panel-body .table-bordered > tbody > tr:first-of-type > td{
  border-bottom: 0px;
}

.panel-table .panel-body .table-bordered > thead > tr:first-of-type > th{
  border-top: 0px;
}

.panel-table .panel-footer .pagination{
  margin:0; 
}

/*
used to vertically center elements, may need modification if you're not using default sizes.
*/
.panel-table .panel-footer .col{
 line-height: 34px;
 height: 34px;
}

.panel-table .panel-heading .col h3{
 line-height: 30px;
 height: 30px;
}

.panel-table .panel-body .table-bordered > tbody > tr > td{
  line-height: 34px;
}
</style>

<body >

<!-- !PAGE CONTENT! -->
<div class="w3-content" style="max-width:1500px">


<!-- Header -->
<header class="w3-panel w3-padding-128 w3-center w3-opacity">
  <h1>Tagvisor</h1>
</header>

 <div class="menu">
    <div class="w3-btn-bar w3-border w3-show-inline-block">
      <a href="http://www.thaiautotag.win/expert_ez.php" class="w3-btn">Home</a>
	  <a href="http://thaiautotag.win/profile.php" class="w3-btn">Profile: 	 
	  <?php
		echo $_SESSION['login'];
	  ?></a>
	  <!--a href="http://thaiautotag.win/uploadpage.php" class="w3-btn">Upload</a-->
	  <a href="http://thaiautotag.win/mainpage.php" class="w3-btn">Log out</a>
    </div>
 </div>

<div class="container">

	<div class="row">
        <div class="col-md-12">
				<?php
			echo '<a href = "http://thaiautotag.win/' . $_SESSION['docpath'] . '/';
			if (strcmp($_SESSION['docpath'],'dataset/docx')==0){
				echo $_SESSION['docname'] . '.docx"';
			}
			else if(strcmp($_SESSION['docpath'],'dataset/doc')==0){
				echo $_SESSION['docname'] . '.doc"';
			}
			else echo $_SESSION['docname'] . '.pdf"';
			echo ' align = "right">Download full document [ '.$_SESSION['docname'].' ]</a>';
			?>
        <h4>Select Paragraph</h4>
			<div class="table-responsive" style="text-align: center;">   
				<table id="mytable" class="table table-bordred table-striped">
					   
					<thead>
						<th>Paragraph Number</th>
						<th>Text</th>
						<th>Edit</th>
						<!--<th>Delete</th>-->
					</thead>
					
					<tbody>
						<?php
							$data = json_decode($_SESSION['json']);
							for( $i = 1 ; $i <= $data->count ; $i++){
								//print $data->content[$i] . PHP_EOL;
								echo '<tr>
						<td>'.$i.'</td>
						<td style="text-align: Left;">' . $data->content[$i-1] . '
						</td>
						<td><p data-placement="top" data-toggle="tooltip" title="Edit"><button class="btn btn-primary btn-xs" data-title="Edit" data-toggle="modal" data-target="#edit'.$i.'" ><span class="glyphicon glyphicon-pencil"></span></button></p></td>
						<!--<<td><p data-placement="top" data-toggle="tooltip" title="Delete"><button class="btn btn-danger btn-xs" data-title="Delete" data-toggle="modal" data-target="#delete" ><span class="glyphicon glyphicon-trash"></span></button></p></td>
						--></tr>';
							}
						?>
					</tbody>			
				</table>

				
				
			</div>
		</div>
	</div>
<form id="form1" name="form1" method="post" action="storetext.php">
	<?php
		$myfile = fopen("text/".$_SESSION['login'].'-'.$_SESSION['docname'].".txt", "r");
		$json = fread($myfile,filesize("text/".$_SESSION['login'].'-'.$_SESSION['docname'].".txt"));
		fclose($myfile);
		//echo $json;
		$oldtag = json_decode($json);
		//echo $oldtag->paragraph[2]->dropdowntag[1];
		$data = json_decode($_SESSION['json']);
		for( $i = 1 ; $i <= $data->count ; $i++){
			//print $data->content[$i] . PHP_EOL;
			echo 
			'<div class="modal fade" id="edit'.$i.'" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
							<h4 class="modal-title custom_align" id="Heading">Edit Paragraph</h4>
						</div>
						
						<div class="container">
							<div class="bs-docs-example">
							<h3 style="padding-left: 10px;" font-family: "san serif">ครูผู้สอน:</h3>
							<table >
								  <tr >
									<td align="center">
										<h3 style="padding-left: 10px;" font-family: "san serif">พัฒนาครู</h3>
									</td>
									<td align="center">
										<h3 style="padding-left: 10px;" >จัดกระบวนการเรียนรู้เชิงบูรณาการ</h3>
									</td>
								  </tr>
								  
								  <tr>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
											<optgroup label="Hard Side">
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("Professional Learning Community",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>Professional Learning Community </option>
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("Knowledge Management",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>Knowledge Management</option>
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("อบรม/สัมมนา",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>อบรม/สัมมนา</option>
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("พัฒนาครูให้เป็นโค้ชการเรียนรู้",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>พัฒนาครูให้เป็นโค้ชการเรียนรู้</option>
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("Lesson Study",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>Lesson Study</option>
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("ความเชี่ยวชาญ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>ความเชี่ยวชาญ</option>
											</optgroup>
											<optgroup label="Soft Side">
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("จิตวิญญาณ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>จิตวิญญาณ</option>
											  <option';
											  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												  if(strcmp("Reflection",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													  echo ' selected = "selected" ';
												  }
											  }
											  echo '>Reflection</option>
											</optgroup>
										</select>
									</td>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("โลก/Globalisation",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>โลก/Globalisation</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("การเงิน",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>การเงิน</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("เศรษฐกิจ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>เศรษฐกิจ</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("ธุรกิจ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>ธุรกิจ</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("การเป็นผู้ประกอบการ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>การเป็นผู้ประกอบการ</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("สิทธิพลเมือง",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>สิทธิพลเมือง</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("สุขภาพ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>สุขภาพ</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("สิ่งแวดล้อม",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>สิ่งแวดล้อม</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("เศรษฐกิจพอเพียง",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>เศรษฐกิจพอเพียง</option>
										</select>
									</td>
								  </tr>
								  <tr >
									<td align="center">
										<h3 style="padding-left: 10px;" >สาระวิชา</h3>
									</td>
									<td align="center">
										<h3 style="padding-left: 10px;" >การจัดการเรียนรู้</h3>
									</td>
								  </tr>
								  
								  <tr>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("ศิลปะ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>ศิลปะ</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("การงานอาชีพ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>การงานอาชีพ</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("พละศึกษา",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>พละศึกษา</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("ภาษาต่างประเทศ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>ภาษาต่างประเทศ</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("ภาษาไทย",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>ภาษาไทย</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("สังคม",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>สังคม</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("วิทยาศาสตร์",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>วิทยาศาสตร์</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("คณิตศาสตร์",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>คณิตศาสตร์</option>
										</select>
									</td>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("RBL",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>RBL</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("PBL",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>PBL</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("STEM",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>STEM</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("BBL",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>BBL</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("Open approach",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>Open approach</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("Active Learning",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>Active Learning</option>
										</select>
									</td>
								  </tr>
								  <tr >
									<td align="center">
										<h3 style="padding-left: 10px;" >บทบาท</h3>
									</td>
									<td align="center">
										<h3 style="padding-left: 10px;" >การจัดการเรียนการสอน</h3>
									</td>
								  </tr>
								  
								  <tr>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("ครูเป็นโค้ชการเรียนรู้",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>ครูเป็นโค้ชการเรียนรู้</option>
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("FACILITATOR",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>FACILITATOR</option>
										</select>
									</td>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <option';
										  for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
												if(strcmp("เตรียมพื้นที่",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
													echo ' selected = "selected" ';
												}
										  }
										  echo '>เตรียมพื้นที่</option>
											<optgroup label="เตรียมเด็ก">
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("จิตศึกษา/จิตตปัญญา",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>จิตศึกษา/จิตตปัญญา</option>
											</optgroup>
											<optgroup label="Class management ">
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("ปรับพฤติกรรมเด็ก",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>ปรับพฤติกรรมเด็ก</option>
											</optgroup>
										</select>
									</td>
								  </tr>
								  <tr >
									<td align="center">
										<h3 style="padding-left: 10px;" >ประเมินเพื่อพัฒนา</h3>
									</td>
									
								  </tr>
								  
								  <tr>
									
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <optgroup label="เด็กปกติ">
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กปกติ - Multiple Intelligence",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กปกติ - Multiple Intelligence</option>
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กปกติ - Formative Assessment",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กปกติ - Formative Assessment</option>
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กปกติ - Summative Assessment",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กปกติ - Summative Assessment</option>
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กปกติ - Authentic",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กปกติ - Authentic</option>
											</optgroup>
											<optgroup label="เด็กที่ต้องการความช่วยเหลือในการเรียนรู้">
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Multiple intelligence",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Multiple intelligence</option>
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Formative intelligence",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Formative Assessment</option>
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Summative intelligence",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Summative Assessment</option>
											  <option';
												for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
													if(strcmp("เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Authentic",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
														echo ' selected = "selected" ';
													}
												}
												echo '>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Authentic</option>
											</optgroup>
											
										</select>
									</td>
								  </tr>
							
							   </table> 
							</div>
						</div>
						
						<div class="container">
							<div class="bs-docs-example">
							<h3 style="padding-left: 10px;" font-family: "Raleway">ผลลัพธ์-เด็ก:</h3>
							  <select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
								<optgroup label="ความสามารถทางวิชาการ">
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("อ่าน",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>อ่าน</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("เขียน",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>เขียน</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("คำนวณ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>คำนวณ</option>
								</optgroup>
								<optgroup label="ทักษะศตวรรษที่ 21">
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะเรียนรู้ - คิดเชิงวิพากษ์",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะเรียนรู้ - คิดเชิงวิพากษ์</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะเรียนรู้ - สื่อสาร",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะเรียนรู้ - สื่อสาร</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะเรียนรู้ - ทำงานเป็นทีม",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะเรียนรู้ - ทำงานเป็นทีม</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะเรียนรู้ - นวัตกรรม",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะเรียนรู้ - นวัตกรรม</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("การเรียนรู้ข้ามวัฒนธรรม",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>การเรียนรู้ข้ามวัฒนธรรม</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะชีวิต - มีเป้าหมาย",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะชีวิต - มีเป้าหมาย</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะชีวิต - วางแผน/ค้นหาทางเลือก",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะชีวิต - วางแผน/ค้นหาทางเลือก</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะชีวิต - ตัดสินใจ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะชีวิต - ตัดสินใจ</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะชีวิต - รับผิดรับชอบ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะชีวิต - รับผิดรับชอบ</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะชีวิต - ยืดหยุ่น",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะชีวิต - ยืดหยุ่น</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะ IT - รู้จักเสพข้อมูล",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะ IT - รู้จักเสพข้อมูล</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะ IT - วิเคราะห์",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะ IT - วิเคราะห์</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทักษะ IT - ใช้ IT ให้เป็นประโยชน์ต่อการดำรงชีวิต",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทักษะ IT - ใช้ IT ให้เป็นประโยชน์ต่อการดำรงชีวิต</option>
								</optgroup>
							  </select>
							</div>
						</div>
						
						<div class="container">
							<div class="bs-docs-example">
							<h3 style="padding-left: 10px;" font-family: "Raleway">อื่นๆ:</h3>
							  <select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="padding-left: 20px;" width="auto" >
								<optgroup label="กิจกรรมพัฒนาผู้เรียน">
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("กิจกรรมสภาการศึกษา",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>กิจกรรมสภาการศึกษา</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("กิจกรรมชมรม/ชุมนุม",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>กิจกรรมชมรม/ชุมนุม</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("กิจกรรมวิชาการ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>กิจกรรมวิชาการ</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("กิจกรรมบำเพ็ญประโยชน์",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>กิจกรรมบำเพ็ญประโยชน์</option>
								</optgroup>
								<optgroup label="ระบบดูแลช่วยเหลือนักเรียน">
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ทุนการศึกษา",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ทุนการศึกษา</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("เยี่ยมบ้าน",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>เยี่ยมบ้าน</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("ให้คำปรึกษาแนะนำ",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>ให้คำปรึกษาแนะนำ</option>
								</optgroup>
								<optgroup label="Transformative Learning ">
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("Transformative Learning - ครู",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>Transformative Learning - ครู</option>
								  <option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("Transformative Learning - นักเรียน",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>Transformative Learning - นักเรียน</option>
								</optgroup>
								<option';
									for ($j = 0; $j < $oldtag->paragraph[$i-1]->dropdowncount; $j++){
										if(strcmp("โครงสร้างหลักสูตร",$oldtag->paragraph[$i-1]->dropdowntag[$j])==0){
											echo ' selected = "selected" ';
										}
									}
									echo '>โครงสร้างหลักสูตร</option>
							  </select>
							</div>
						</div>
						
						</br>
						
						<div class="bs-example">
							<input id="tags'.$i.'" name="tags'.$i.'" type="text" class="tags" value="';
							if($oldtag->paragraph[$i-1]->freecount >= 1){
								echo $oldtag->paragraph[$i-1]->freetag[0];
								for($j = 1 ; $j < $oldtag->paragraph[$i-1]->freecount ; $j++){
									echo ',' . $oldtag->paragraph[$i-1]->freetag[$j];
								}
							}
							echo '" />
						</div>
						
						
						<div class="modal-footer ">
							<button type="button" class="btn btn-warning btn-lg" data-dismiss="modal" aria-hidden="true" style="width: 100%;"><span class="glyphicon glyphicon-ok-sign" "></span> Update</button>
						</div>
					</div>
					<!-- /.modal-content --> 
				</div>
				<!-- /.modal-dialog --> 
			</div>';
		}
	?>

	<input id="save" name="submit" type="submit" value="SaveDraft" onclick="position()">
    <input id="submit" name="submit" type="submit" value="Submit" >
</form>



</a>
</div>
</div>

	<script type="text/javascript">
		
	</script>
	
	<script type="text/javascript">
		function onAddTag(tag) {
			alert("Added a tag: " + tag);
		}
		function onRemoveTag(tag) {
			alert("Removed a tag: " + tag);
		}
		function onChangeTag(input,tag) {
			alert("Changed a tag: " + tag);
		}
		$(function() {
			var i;
			var count = "<?php 
			$data=json_decode($_SESSION['json']);
			echo $data->count;
			?>";
			for( i = 1 ; i <= count ; i++){
				var str1 = "#tags";
				var str2 = i;
				var tag = str1.concat(str2);
				$(tag).tagsInput({width:'auto'});
			}
			
		});
	</script>

	<script type="text/javascript">/*
	var testvariable = 'ครูอ้อยจัดการเรียนรู้โดยใช้กระบวนการวิจัย 10 ขั้นเนียนอย่างเป็นธรรมชาติ   ขั้นต้นเป็นขั้นค้นหาโจทย์การเรียนรู้เป็น  ซึ่งต้องเป็นเรื่องที่นักเรียนสนใจอยากเรียน  เป็นเรื่องใกล้ตัวที่เชื่อมโยงไปถึงชุมชน  เด็กๆ จะได้เรียนรู้ว่า  มนุษย์ไม่สามารถมีชีวิตอยู่คนเดียวได้  และการอยู่อย่างมีความสุขนั้นต้องอาศัยปัจจัยอะไร';
	document.getElementById('testvariable').innerHTML = testvariable;
	
	function postdata()
	{
		$.ajax({
		type: "POST",
		dataType: "text",
		url: "makepost.php",
		data: "tags_3=" + $("#tags_3").val() + "para=" + testvariable,
		cache: false,
		success: function(reply_text)
		{
				alert(reply_text);
		}
		});
	}*/
	</script>
	
	<script type="text/javascript">
      window.onload=function(){
      $('.selectpicker').selectpicker();
      $('.rm-mustard').click(function() {
        $('.remove-example').find('[value=Mustard]').remove();
        $('.remove-example').selectpicker('refresh');
      });
      $('.rm-ketchup').click(function() {
        $('.remove-example').find('[value=Ketchup]').remove();
        $('.remove-example').selectpicker('refresh');
      });
      $('.rm-relish').click(function() {
        $('.remove-example').find('[value=Relish]').remove();
        $('.remove-example').selectpicker('refresh');
      });
      $('.ex-disable').click(function() {
          $('.disable-example').prop('disabled',true);
          $('.disable-example').selectpicker('refresh');
      });
      $('.ex-enable').click(function() {
          $('.disable-example').prop('disabled',false);
          $('.disable-example').selectpicker('refresh');
      });

      // scrollYou
      //$('.scrollMe .dropdown-menu').scrollyou();

      //prettyPrint();
      };
	  
	
	function position() {
		var currentYOffset = window.pageYOffset;  // save current page postion.
		document.cookie = "position" + "=" + currentYOffset;
		
	}
	
	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}
	
	
	window.onload = function() {
	var position = getCookie("position");
		window.scrollTo(0, position);
	}
	  
    </script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>
	
	


</body>
</html>
