<?php
// Start the session
session_start();


?>
<!DOCTYPE html>
<html>
<title>Main Page</title>
<link rel="stylesheet" type="text/css" href="css.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>
body,h1 {font-family: "Raleway", Arial, sans-serif}
h1 {letter-spacing:7px}
.menu {position: absolute;top: 25px;right: 25px;}

.border_red { border: 4px solid red; }
.border_lime { border: 4px solid lime; }

table.table-hover {
    width: 25%;
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
</style>

<body>

<!-- !PAGE CONTENT! -->
<div class="w3-content" style="max-width:1500px">



<!-- Header -->
<header class="w3-panel w3-padding-128 w3-center w3-opacity">
  <h1>Tagvisor</h1>
</header>
<table class="table table-hover" align="center" >
<?php

	$user=$_SESSION['login'];
	$conn = mysqli_connect('localhost','root','password','tagvisor');

	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	//echo "Connected successfully";

	$sql = "SELECT stats.docid AS did,docname,status FROM stats INNER JOIN users ON users.id = stats.userid INNER JOIN doc ON doc.docid = stats.docid WHERE users.username = '".$user."' ORDER BY did;";
	$result = mysqli_query($conn,$sql);
	while($rs = $result->fetch_array(MYSQLI_ASSOC)){
		if($rs["status"] == 0){
			echo'<tr><td align="center"><a href="http://www.thaiautotag.win/upload.php?docid='.$rs["did"].'">'.$rs[docname].'</a></td></tr>';
		}
		else if($rs["status"] == 1){
			echo'<tr><td class="danger" align="center"><a href="http://www.thaiautotag.win/upload.php?docid='.$rs["did"].'">'.$rs[docname].'</a></td></tr>';
		}
		else{
			echo'<tr><td class="success" align="center"><a href="http://www.thaiautotag.win/upload.php?docid='.$rs["did"].'">'.$rs[docname].'</a></td></tr>';
		}
	}
	
	?>
</table>
  <br>
  <br>

<div class="container">
 <div class="row">
        <div class="col-md-6">
            <div id="custom-search-input">
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

</div>
</body>
</html>
