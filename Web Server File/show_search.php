<?php
header('Content-Type: text/html; charset=utf-8');
// Start the session
session_start();

$limit = 1000;
?>
<!DOCTYPE html>
<html>
<title>Search</title>
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
                <div class="input-group col-md-12">
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
					   
					
					
					<tbody>
						<tr>
							<td>
								<div>
									<div style="width: 50%; border:0px solid #73AD21;position:relative; left:15px; top:5px;"><h3><a href='http://thaiautotag.win/show_result.php?search=Active Learning+story-satun-1'>story-satun-1</a></h3></div>
									<div style="width: 20%; border:0px solid #73AD21;position:relative; left:80%; top:-55px;"> ผู้ชม: 213 คน      <span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span></div> <!--glyphicon glyphicon-star-empty--> 
									<div align="center" style="width: 20%;  border:0px solid #73AD21;position:relative; left:80%; top:-50px;"> ผลจากคะแนน: 15 คน </div>
									<?php
									$text = 'ทุกสิ้นปีการศึกษา โรงเรียนจัดกระบวนการทบทวนสรุปผลการเรียนรู้ที่ผ่านมา และพบว่าเด็กได้ฝึกพัฒนาทักษะเรียนรู้กระบวนการหาคำตอบได้ก็จริง แต่ความรู้นั้นไม่ถูกนำมาใช้ประโยชน์ในชุมชน จนเป็นนโยบายโรงเรียนที่ว่าโจทย์การเรียนรู้ต้องสัมพันธ์เชื่อมโยงกับชุมชน เรียกว่าโจทย์โมเดล และเป็นครั้งแรกที่ครูไพเราะได้มาสอนชั้น ป.5 วิชาบูรณาการ ไม่เคยรู้จักนักเรียนมาก่อน แต่ไม่ว่าจะสอนชั้นไหน ครูไพเราะจะเริ่มจากลดช่องว่างระหว่างครูและนักเรียน โดยใช้ภาษาไทยที่ครูรักเป็นสื่อสร้างความสัมพันธ์กับเด็กๆ';
									
									?>
									<div class="text1" style="position:relative; top:-20px;">
										<?php
										if (strlen($text) < $limit) {
											echo $text;
										} else {
											echo substr($text, 0 , $limit).'...';
											echo "<br/><a href='#' align='right' onclick='showMore(2)'>read more</a>";
										}
										?>
									</div>
									<div style="width: 90%;position:relative; left:2%; top:-15px;"><br/>Tag: "Knowledge Management" , "PBL" , "Active Learning" , "เตรียมพื้นที่" , "จิตศึกษา/จิตตปัญญา" </div>
								</div>
							</td>
						</tr>
						
						<tr>
							<td>
								<div>
									<div style="width: 50%; border:0px solid #73AD21;position:relative; left:15px; top:5px;"><h3><a href='#'>story-thaphra-khonkaen</a></h3></div>
									<div style="width: 20%; border:0px solid #73AD21;position:relative; left:80%; top:-55px;"> ผู้ชม: 115 คน      <span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span><span class="glyphicon glyphicon glyphicon-star gi-2x"></span></div> <!--glyphicon glyphicon-star-empty--> 
									<div align="center" style="width: 20%;  border:0px solid #73AD21;position:relative; left:80%; top:-50px;"> ผลจากคะแนน: 8 คน </div>
									<?php
									$text = '“...ตอนนั้นเป็นช่วงเดือนพฤศจิกายน   เป็นช่วงของต้นกล้วย  สัปดาห์นั้นยังคิดไม่ออกว่าจะให้เด็กทำอาหารอะไรดี  ที่บ้านคุณยายครูปลูกกล้วยไว้เยอะ  เห็นกล้วยผลสวยเชียว  คุณยายเคยต้มให้กิน  หวานอร่อยมาก  ครูคิดว่าเด็กจะชอบเหมือนเราไหม  เลยเอามาให้เด็กกิน  เด็กบอกคุณครูมันอร่อยดีนะ หนูเคยกินคุณแม่เคยทำให้  บางคนบอกว่าหนูเคยกินกล้วยบวชชีอร่อยกว่า  เด็กเริ่มถกเถียงกัน  คุณครูก็ให้เขาจัดการกันเอง  แล้วก็หาจุดลงตัวได้  บอกไปว่า  เราต่างคนต่างชอบไม่เหมือนกัน  ไม่จำเป็นต้องให้คนอื่นชอบเหมือนเรา  ที่จริงครูมีแผนในใจ  แต่ลองถามเด็กดูก่อนว่าจะทำอะไรดี  ส่วนมากใช้วิธีคุยกัน  มีเด็กคนหนึ่งบอกว่าทำกล้วยบวชชี  อีกคนหนึ่งบอกว่าทำไมไม่ลองทำกล้วยที่คุณครูเอามาให้กิน  กล้วยต้มน่าจะง่ายนะ  แค่เอามาต้มเฉยๆ  เลยสำรวจความคิดเด็ก  โดยครูเขียนบนกระดาน  มีชื่ออาหารอะไรบ้าง  วาดรูปใส่เป็นสัญลักษณ์ให้เด็กดูว่ามันคืออะไร  เพราะว่าเด็กบางคนยังอ่านไม่ออก  พอเสร็จแล้ว  ให้เด็กยกมือเราจะทำอะไรดี  ระหว่างกล้วยต้มกับกล้วยบวชชี  ทีนี้คะแนนมันเท่ากัน คือ ๑๒ ต่อ ๑๒  ครูเลยบอกถ้าอย่างนั้นเริ่มวางแผนว่าจะทำอะไร  ระหว่างกล้วยต้มกับกล้วยบวชชี   เพราะคะแนนเท่ากัน  เด็กบอกว่าอยากทำทั้ง ๒ อย่าง  ถ้าอย่างนั้นเราทำ ๒ อย่างเลยดีไหม  เด็กๆ ดีใจ  วันทำกิจกรรมเด็กมาเรียนกันครบ  เราก็ถือโอกาสว่าก็กล้วยเหมือนกัน  คงทำไม่ยากหรอก  ก็เลยทำ ';
									
									?>
									<div class="text2" style="position:relative; top:-20px;">
										<?php
										if (strlen($text) < $limit) {
											echo $text;
										} else {
											echo substr($text, 0 , $limit).'...';
											echo "<br/><a href='#' align='right' onclick='showMore(2)'>read more</a>";
										}
										?>
									</div>
									<div style="width: 90%;position:relative; left:2%; top:-15px;"><br/>Tag: "Active Learning" , "FACILITATOR" , "จิตศึกษา/จิตตปัญญา" , "ทักษะเรียนรู้ - สื่อสาร" , "ทักษะ IT - วิเคราะห์"</div>
								</div>
							</td>
						</tr>
					</tbody>			
				</table>

				
				
			</div>
		</div>
	</div>
</div>

<script>
    function showMore(number) {
        var text = '<?php echo $text; ?>'
		
        $(".text".concat(number)).html(text);
    }
</script>


</body>
</html>
