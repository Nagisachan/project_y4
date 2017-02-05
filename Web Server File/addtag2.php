<?php
header('Content-Type: text/html; charset=utf-8');
// Start the session
session_start();
$_SESSION['filename'] = "ALtest";
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

<body>

<!-- !PAGE CONTENT! -->
<div class="w3-content" style="max-width:1500px">


<!-- Header -->
<header class="w3-panel w3-padding-128 w3-center w3-opacity">
  <h1>TaggerBot</h1>
</header>

 <div class="menu">
    <div class="w3-btn-bar w3-border w3-show-inline-block">
      <a href="http://www.thaiautotag.win/expert_ez.php" class="w3-btn">Home</a>
	  
	  <a href="http://thaiautotag.win/profile.php" class="w3-btn">Profile: 
	  <?php
		echo $_SESSION['login'];
	  ?>
	  </a>
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
			echo ' align = "right">Download full document [ '.$_SESSION['docname'].' ] </a>';
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
							<table>
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
											  <option>Professional Learning Community </option>
											  <option>Knowledge Management</option>
											  <option>อบรม/สัมมนา</option>
											  <option>พัฒนาครูให้เป็นโค้ชการเรียนรู้</option>
											  <option>Lesson Study</option>
											  <option>ความเชี่ยวชาญ</option>
											</optgroup>
											<optgroup label="Soft Side">
											  <option>จิตวิญญาณ</option>
											  <option>Reflection</option>
											</optgroup>
										</select>
									</td>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <option>โลก/Globalisation</option>
										  <option>การเงิน</option>
										  <option>เศรษฐกิจ</option>
										  <option>ธุรกิจ</option>
										  <option>การเป็นผู้ประกอบการ</option>
										  <option>สิทธิพลเมือง</option>
										  <option>สุขภาพ</option>
										  <option>สิ่งแวดล้อม</option>
										  <option>เศรษฐกิจพอเพียง</option>
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
										  <option>ศิลปะ</option>
										  <option>การงานอาชีพ</option>
										  <option>พละศึกษา</option>
										  <option>ภาษาต่างประเทศ</option>
										  <option>ภาษาไทย</option>
										  <option>สังคม</option>
										  <option>วิทยาศาสตร์</option>
										  <option>คณิตศาสตร์</option>
										</select>
									</td>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
										  <option>RBL</option>
										  <option>PBL</option>
										  <option>STEM</option>
										  <option>BBL</option>
										  <option>Open approach</option>
										  <option>Active Learning </option>
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
										  <option>ครูเป็นโค้ชการเรียนรู้</option>
										  <option>FACILITATOR</option>
										</select>
									</td>
									<td align="center">
										<select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="display: none;" width="auto" style="padding-left: 10px;">
											<option>เตรียมพื้นที่</option>
											<optgroup label="เตรียมเด็ก">
											  <option>จิตศึกษา/จิตตปัญญา</option>
											</optgroup>
											<optgroup label="Class management ">
											  <option>ปรับพฤติกรรมเด็ก</option>
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
											  <option>เด็กปกติ - Multiple Intelligence</option>
											  <option>เด็กปกติ - Formative Assessment</option>
											  <option>เด็กปกติ - Summative Assessment </option>
											  <option>เด็กปกติ - Authentic </option>
											</optgroup>
											<optgroup label="เด็กที่ต้องการความช่วยเหลือในการเรียนรู้">
											  <option>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Multiple intelligence</option>
											  <option>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Formative Assessment</option>
											  <option>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Summative Assessment</option>
											  <option>เด็กที่ต้องการความช่วยเหลือในการเรียนรู้ - Authentic</option>
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
								  <option>อ่าน</option>
								  <option>เขียน</option>
								  <option>คำนวณ</option>
								</optgroup>
								<optgroup label="ทักษะศตวรรษที่ 21">
								  <option>ทักษะเรียนรู้ - คิดเชิงวิพากษ์</option>
								  <option>ทักษะเรียนรู้ - สื่อสาร</option>
								  <option>ทักษะเรียนรู้ - ทำงานเป็นทีม</option>
								  <option>ทักษะเรียนรู้ - นวัตกรรม</option>
								  <option>การเรียนรู้ข้ามวัฒนธรรม</option>
								  <option>ทักษะชีวิต - มีเป้าหมาย</option>
								  <option>ทักษะชีวิต - วางแผน/ค้นหาทางเลือก</option>
								  <option>ทักษะชีวิต - ตัดสินใจ</option>
								  <option>ทักษะชีวิต - รับผิดรับชอบ</option>
								  <option>ทักษะชีวิต - ยืดหยุ่น</option>
								  <option>ทักษะ IT - รู้จักเสพข้อมูล</option>
								  <option>ทักษะ IT - วิเคราะห์</option>
								  <option>ทักษะ IT - ใช้ IT ให้เป็นประโยชน์ต่อการดำรงชีวิต</option>
								</optgroup>
							  </select>
							</div>
						</div>
						
						<div class="container">
							<div class="bs-docs-example">
							<h3 style="padding-left: 10px;" font-family: "Raleway">อื่นๆ:</h3>
							  <select name="select'.$i.'[]" class="selectpicker" multiple="multiple" style="padding-left: 20px;" width="auto" >
								<optgroup label="กิจกรรมพัฒนาผู้เรียน">
								  <option>กิจกรรมสภาการศึกษา</option>
								  <option>กิจกรรมชมรม/ชุมนุม</option>
								  <option>กิจกรรมวิชาการ</option>
								  <option>กิจกรรมบำเพ็ญประโยชน์</option>
								</optgroup>
								<optgroup label="ระบบดูแลช่วยเหลือนักเรียน">
								  <option>ทุนการศึกษา</option>
								  <option>เยี่ยมบ้าน</option>
								  <option>ให้คำปรึกษาแนะนำ</option>
								</optgroup>
								<optgroup label="Transformative Learning ">
								  <option>Transformative Learning - ครู</option>
								  <option>Transformative Learning - นักเรียน</option>
								</optgroup>
								<option>โครงสร้างหลักสูตร</option>
							  </select>
							</div>
						</div>
						
						</br>
						
						<div class="bs-example">
							<input id="tags'.$i.'" name="tags'.$i.'" type="text" class="tags" value="" />
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

	<input id="save" name="submit" type="submit" value="SaveDraft" onclick="position()" >
	<input id="submit" name="submit" type="submit" value="Submit" >
</form>

</a>
</div>
</div>
	
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
