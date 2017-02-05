<?php
header('Content-Type: text/html; charset=utf-8');
// Start the session
session_start();

$limit = 1000;
?>
<!DOCTYPE html>
<html>
<title>Result</title>
<link rel="stylesheet" type="text/css" href="css.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>

.gi-2x{font-size: 2em;}


dummydeclaration { padding-left: 4em; }
tab1 { padding-left: 4em; }
tab2 { padding-left: 8em; }
tab3 { padding-left: 12em; }
tab4 { padding-left: 16em; }
tab5 { padding-left: 20em; }
tab6 { padding-left: 24em; }
tab7 { padding-left: 28em; }
tab8 { padding-left: 32em; }
tab9 { padding-left: 36em; }
tab10 { padding-left: 40em; }
tab11 { padding-left: 44em; }
tab12 { padding-left: 48em; }
tab13 { padding-left: 52em; }
tab14 { padding-left: 56em; }
tab15 { padding-left: 60em; }
tab16 { padding-left: 64em; }




body,h1 {font-family: "Raleway", Arial, sans-serif}
h1 {letter-spacing:7px}
.menu {position: absolute;top: 25px;right: 25px;}

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
</style>

<body>

<!-- !PAGE CONTENT! -->
<div class="w3-content" style="max-width:1500px">


<!-- Header -->
<header class="w3-panel w3-padding-4 w3-opacity">
  <h1>TaggerBot</h1>
</header>


<div class="container">
 <div class="row">
        <div class="col-md-4" style="position:absolute; left:35px; top:30px;">
            <div id="custom-search-input" >
                <div class="input-group col-md-24">
                    <input type="text" class="form-control input-lg" placeholder="Search" />
                    <span class="input-group-btn">
                        <button class="btn btn-info btn-lg" type="button">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </span>
                
                </div>
            </div>
        </div>
 </div>
</div>


 <div class="menu">
    <div class="w3-btn-bar w3-border w3-show-inline-block">
      <a href="http://www.thaiautotag.win/mainpage.php" class="w3-btn">Home</a>
	  <a href="http://www.thaiautotag.win/login.php" class="w3-btn">Profile: Suphanut</a>
	  <a href="http://www.thaiautotag.win/regis.php" class="w3-btn">Log Out</a>
    </div>
  </div>

</div>

<div class="container">
	<div class="row">
        <div class="col-md-12">
		
        <h4>Search Result: <?php echo $_SESSION['Search_Result']."Active Learning"; ?></h4>
			<div class="table-responsive" >   
				<table id="mytable" class="table table-bordred table-striped">

					<tbody style="border:0px solid #73AD21;">
						<tr>
							<td>
								<div>
									<div style="width: 50%; border:0px solid #73AD21;position:relative; left:15px; top:5px;"><h3>story-satun-1</h3></div>
									<div style="width: 20%; border:0px solid #73AD21;position:relative; left:80%; top:-55px;"> ผู้ชม: 213 คน      <span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span></div> <!--glyphicon glyphicon-star-empty--> 
									<div align="center" style="width: 20%;  border:0px solid #73AD21;position:relative; left:80%; top:-50px;"> ผลจากคะแนน: 15 คน </div>
									
									<div style="position:relative; top:-20px;">
										ทุกสิ้นปีการศึกษา โรงเรียนจัดกระบวนการทบทวนสรุปผลการเรียนรู้ที่ผ่านมา และพบว่าเด็กได้ฝึกพัฒนาทักษะเรียนรู้กระบวนการหาคำตอบได้ก็จริง แต่ความรู้นั้นไม่ถูกนำมาใช้ประโยชน์ในชุมชน จนเป็นนโยบายโรงเรียนที่ว่าโจทย์การเรียนรู้ต้องสัมพันธ์เชื่อมโยงกับชุมชน เรียกว่าโจทย์โมเดล และเป็นครั้งแรกที่ครูไพเราะได้มาสอนชั้น ป.5 วิชาบูรณาการ ไม่เคยรู้จักนักเรียนมาก่อน แต่ไม่ว่าจะสอนชั้นไหน ครูไพเราะจะเริ่มจากลดช่องว่างระหว่างครูและนักเรียน โดยใช้ภาษาไทยที่ครูรักเป็นสื่อสร้างความสัมพันธ์กับเด็กๆ
									</div>
									
								</div>
							</td>
						</tr>
					</tbody>			
				</table>
			</div>
			<div>เอกสารต้นทาง: "story-satun-1" <a href="#">Download Here</a> <tab1>Tag: "Knowledge Management" , "PBL" , "Active Learning" , "เตรียมพื้นที่" , "จิตศึกษา/จิตตปัญญา"</tab1> </br></br>Comment</div>
				<table id="mytable" class="table table-bordred table-striped">
					   
					<thead>
						<th>Name</th>
						<th>Comment</th>
						<th>Date</th>
						<!--<th>Delete</th>-->
					</thead>
					
					<tbody>
						<tr>
							<td width="15%">Wimon</td>
							<td width="70%">บทความน่าสนใจมากค่ะ</td>
							<td width="15%">
								<?php
								$d=mktime(11, 14, 54, 8, 12, 2014);
								echo date("Y-m-d h:i:sa", $d);
								?>
							</td>
						</tr>
					</tbody>			
				</table>

			<form>
				
				<!-- Textarea -->
				<div class="form-group">
				  <label>Write a comment about this Document <tab1>Username: Suphanut</tab1></label>
				  <div class="col-md-16">                     
					<textarea class="form-control" id="texta" name="texta"></textarea>
				  </div>
				</div>

				<input id="submit" name="submit" type="submit" value="Send a comment" align="center">
			</form>
		</div>
	</div>
</div>




</body>
</html>
