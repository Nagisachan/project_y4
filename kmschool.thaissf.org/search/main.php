<?php
include_once("db.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title>TaggerBot</title>

  <!-- Bootstrap -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

  <!-- Local css -->
  <link rel="stylesheet" type="text/css" href="css/main.css">

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

  <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsxK2jlO7zzJyyhPG9ZcpJFJ_vswGdkag"></script> -->
  <!-- <script type="text/javascript" src="js/infobox.js"></script> -->
  <script src="https://api.longdo.com/map/?key=dc22bb618715332baabae2de29bc461e"></script>

</head>

<body>

  <nav class="navbar navbar-inverse navbar-fixed-top" style="background-color: #428bca; border-color: #428bca; height: 51px;">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a href="#" class="navbar-brand"><img src="img/logo-white@2x.png" alt="logo" /></a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar" style="background-color: #428bca; border-color: #428bca;">
        <ul class="nav navbar-nav">
          <li class="active"><a href="main.php">หน้าหลัก</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <!-- <li><a href="JavaScript: alert('open popup');"><span class="glyphicon glyphicon-log-in"></span> เข้าสู่ระบบ</a></li> -->
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid fill-height text-center" style="padding-top: 51px;">
    <div class="row content fill-height">
      <div class="col-md-2 sidenav left fill-height text-left">
        <div id="schoolSearchPanel" class="row">
          <select id="schoolListSelect2Multiple" multiple="multiple">
            <?php
            $query = mysqli_query($con, "select school_all.id,school_all.name from file join school_all on file.school = school_all.id group by school_all.id");

            while ($row = mysqli_fetch_assoc($query)) {
              ?>
              <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php
          }
          ?>
          </select>
        </div>
        <div id="keywordSearchPanel" class="row">
          <input id="keywordSearch" class="keywordSearch" type="text" placeholder="Keyword">

        </div>
        <div id="tagSearchPanel" class="row">
          <?php
          $query = mysqli_query($con, "select id,question,subtag_id,subtag_name from question");

          $tag_category = array();
          array_push($tag_category, "อ่านไม่ออก เขียนไม่ได้ คิดเลขไม่เป็น ทำไงดี");
          $tag_content = array();
          $subCategory = array();
          $id = 1;
          while ($row = mysqli_fetch_assoc($query)) {
            if ($id == $row['id']) {
              $tag_content['item'] = $row['subtag_id'];
              $tag_content['item_name'] = $row['subtag_name'];
              $category_name = $row['question'];
              $subCategory[$category_name][] = $tag_content;
            } else {
              $id++;
              array_push($tag_category, $row['question']);
              $tag_content['item'] = $row['subtag_id'];
              $tag_content['item_name'] = $row['subtag_name'];
              $category_name = $row['question'];
              $subCategory[$category_name][] = $tag_content;
            }
          }

          ?>

          <?php for ($domainTagNo = 0; $domainTagNo <= 5; $domainTagNo++) { ?>
            <div class="row" style="margin: 0px; padding: 0px;">
              <ul class="checklist_tag checkbox domainTag col-xs-11">
                <li style="list-style: none;">
                  <label><input id="domainTag" type="checkbox"> <?php $tag_category_name = $tag_category[$domainTagNo];
                                                                echo $tag_category_name; ?></label>
                  <ul id="subDomainTag<?php echo $domainTagNo ?>List" class="subDomainTagList">
                    <?php for ($subDomainTagNo = 0; $subDomainTagNo <= count($subCategory[$tag_category[$domainTagNo]]) - 1; $subDomainTagNo++) { ?>
                      <li class="checkbox col-xs-12 subDomainTag">
                        <label><input type="checkbox" class="name_tag" value="<?php echo $subCategory[$tag_category[$domainTagNo]][$subDomainTagNo]['item'] ?>"><?php echo $subCategory[$tag_category[$domainTagNo]][$subDomainTagNo]['item_name'] ?></label>
                      </li>
                    <?php } ?>
                  </ul>
                </li>
              </ul>
              <a id="moreTag<?php echo $domainTagNo ?>Toggle" class="col-xs-1 moreTagToggle" href="JavaScript: moreTagToggle(<?php echo $domainTagNo ?>);">[+]</a>
            </div>
          <?php } ?>
        </div>
        <div class="row" style="padding: 5px 15px 10px 15px;">
          <input id="submitSearch" class="submitSearch" type="button" value="search" onclick="search(1)">
        </div>
      </div>

      <div class="col-md-5 fill-height text-center" style="margin: 0px; padding: 0px;">
        <div id="googleMap" style="width: 100%; height: 100%; display: block;">
        </div>
        <div id="schoolPopup" onclick="">
        </div>
      </div>

      <div class="col-md-5 sidenav right fill-height">
        <div class="row" style="margin: 0px; padding: 0px 0px 10px 0px; width: 100%;">
          <div class="col-xs-5" style="padding: 0px 0px 0px 0px; text-align: left;">
            หน้า <input id="pageNo" class="pageNo" type="number" value="1" min="1" max="1" step="1"> จาก <label id="numberOfPage">0</label>
          </div>
          <div class="col-xs-7" style="padding: 0px; text-align: right;">
            เรียงลำดับ:
            <select id="orderBy">
              <option value="0" selected>วันที่เผยแพร่ - ล่าสุด</option>
              <option value="1">วันที่เผยแพร่ - เก่าสุด</option>
              <option value="2">จำนวนครั้งที่ดาวน์โหลด - จากมากไปน้อย</option>
              <option value="3">จำนวนครั้งที่ดาวน์โหลด - จากน้อยไปมาก</option>
            </select>
          </div>
        </div>
        <div id="loader1" class="loader" style="display: none;"></div>
        <div id="searchResult">
          </br> กรุณาค้นหาจาก Tag ด้านซ้าย
        </div>

      </div>

    </div>
  </div>
  <script type="text/javascript" src="js/main.js"></script>
</body>

</html>