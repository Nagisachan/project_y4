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

<!-- !PAGE CONTENT! http://thaiautotag.win/show_search.php?search=abc -->
<div class="w3-content" style="max-width:1500px">


<!-- Header -->
<header class="w3-panel w3-padding-128 w3-center w3-opacity">
  <h1>TaggerBot</h1>
</header>
<!--<h1 class="w3-panel w3-center">Please "Log in" For using add tag function</h1>
</br>
<h2 class="w3-panel w3-center">For Sodsri-Saridwongsa Foundation</h2>
<h1 class="w3-panel w3-center">Password: test12345</h1>
</br>
</br> -->

<div class="container">
 <div class="row">
        <div class="col-md-6">
            <div id="custom-search-input">
                <div class="input-group col-md-12">
                    <input type="text" class="form-control input-lg" placeholder="Search" />
                    <span class="input-group-btn">
                        <a href="http://thaiautotag.win/show_search.php?search=Active Learning" class="btn btn-default" >
                            <span class="glyphicon glyphicon-search"></span>
                        </a>
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
</body>
</html>
