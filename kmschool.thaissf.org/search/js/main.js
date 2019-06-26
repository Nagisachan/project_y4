var schoolId = [];
var keywordSearch = "";
var map;
var marker = null;
var infoBox;
var subDomainTag = [];
var currentPageNo;
var orderby = ["order by file_uploaded_date DESC,download_time DESC", "order by file_uploaded_date ASC", "order by download_time DESC", "order by download_time ASC"];

function preload() {
  $("#loader1").fadeToggle();
  $("#searchResult").fadeToggle();
}

function moreTagToggle(domainTagNo) {
  if ($("#subDomainTag" + domainTagNo + "List").css("display") == "none") {
    $("#moreTag" + domainTagNo + "Toggle").html("[-]");
  } else {
    $("#moreTag" + domainTagNo + "Toggle").html("[+]");
  }
  $("#subDomainTag" + domainTagNo + "List").fadeToggle();
}

function myMap() {
  // var mapProp = {
  //   center: new google.maps.LatLng(13.7562017, 100.5000158),
  //   zoom: 6,
  // };

  // map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

  map = new longdo.Map({
    placeholder: document.getElementById("googleMap")
  });

  map.location({
    lat: 13.7562017,
    lon: 100.5000158
  });

  map.zoom(6);
}

function search(pageNo) {
  // preload();
  currentPageNo = pageNo;
  schoolId = $("#schoolListSelect2Multiple").val();
  keywordSearch = $("#keywordSearch").val();
  subDomainTag = [];
  $('.name_tag:checked').each(function () {
    subDomainTag.push($(this).val());
  });
  getParagraphContent();
  $("#pageNo").val(currentPageNo);
}

function getParagraphContent() {
  $.ajax({
    type: "POST",
    url: "service/query_document_rightside.php",
    data: {
      tag_box: subDomainTag,
      school_list: schoolId,
      free_search: keywordSearch,
      orderby: orderby[$("#orderBy").val()],
      pageNo: currentPageNo
    },
    beforeSend: function () {
      preload();
    },
    success: function (data) {
      //alert(data);
      if (data == "Exceed Page") {
        text = "0";
        $("#numberOfPage").html(text);
        $("#pageNo").prop("max", text);
        text = "";
      } else {
        var jsonData = JSON.parse(data);

        var text = "";
        if (Object.keys(jsonData).length > 0) {
          text = Math.ceil(parseFloat(jsonData[0]['rows_count']) / 10).toString();
        } else {
          text = "0";
        }
        $("#numberOfPage").html(text);
        $("#pageNo").prop("max", text);

        text = "";
        var tag = [];
        for (var i = 0; i < Object.keys(jsonData).length; i++) {
          text = text + '<div class="well paragraphItem" style="cursor: pointer;" onclick="gotoSchool(\'' +
            jsonData[i]['school'] +
            '\',\'' +
            jsonData[i]['school_name'] +
            '\',\'' +
            ((jsonData[i]['department'] != null) ? jsonData[i]['department'] : "ไม่มี") +
            '\',' + jsonData[i]['latitude'] +
            ', ' + jsonData[i]['longitude'] +
            ');"><div class="row"><div class="col-xs-4 text-left">หัวข้อของเอกสาร:</div><div class="col-xs-8 text-left">' +
            jsonData[i]['file_name'] +
            '</div></div><div class="row"><div class="col-xs-4 text-left">พารากราฟที่:</div><div class="col-xs-8 text-left">' +
            jsonData[i]['paragraph_id'] +
            '</div></div><div class="row"><div class="col-xs-4 text-left">เนื้อหาที่เกี่ยวข้องของพารากราฟนี้:</div><div class="col-xs-8 text-left">';

          tag = jsonData[i]['tag'].split(",");
          for (var j = 0; j < tag.length; j++) {
            text += '<label class="tagLabel">' + tag[j] + '</label>';
          }

          text += '</div></div><div class="row"><div class="col-xs-4 text-left">วันที่เผยแพร่:</div><div class="col-xs-8 text-left">' +
            jsonData[i]['file_uploaded_date'] +
            '</div></div><div class="row"><div class="col-xs-4 text-left">โรงเรียน:</div><div class="col-xs-8 text-left">' +
            jsonData[i]['school_name'] +
            '</div></div><div class="row"><div class="col-xs-4 text-left">จำนวนครั้งที่ถูกดาวน์โหลดเอกสาร:</div><div class="col-xs-8 text-left">' +
            jsonData[i]['download_time'] +
            '</div></div><br><div class="row"><div class="col-md-12 text-left"><p style="text-indent: 30px; text-align: justify; text-justify: distribute;">' +
            jsonData[i]['content'] +
            '</p></div></div><div class="row" style="margin: 15px 0px 5px 0px;"><div class="col-xs-12 col-md-4 text-right" style="float: right; padding: 0px"><a target="_blank" class="downloadBtn" href="/kmschool/search/service/get_download_document.php?filename=' + jsonData[i]['file_name'] + '" onclick="increaseDownload(\'' + jsonData[i]['file_name'] + '\')">' +
            'ดาวน์โหลดเอกสาร' +
            '</a></div></div></div>';
        }
      }
      $("#searchResult").html(text);

    },
    async: true,
    complete: function () {
      preload();
    }
  });

}

function increaseDownload(fileName) {
  //search(1);
  $.ajax({
    type: "POST",
    url: "service/increaseDownload.php",
    data: {
      file_name: fileName
    },
    success: function (data) {
      //alert(data);
    },
    async: false
  });

}

function schoolPopupOpen(schoolId) {
  //$("#googleMap").fadeToggle("fast", "linear");
  $("#schoolPopup").fadeToggle("fast", "swing");

  $.ajax({
    type: "POST",
    url: "service/query_schoolinfo_from_id.php",
    data: {
      school_id: schoolId
    },
    beforeSend: function () {
      var text = '<div id="loader2" class="loader" style="display: block;"></div>';
      $("#schoolPopup").html(text);
    },
    success: function (data) {
      jsonData = JSON.parse(data)[0];

      var str = "\N";
      var website = "";
      var url = "";
      if (str.localeCompare(jsonData['website'])) {
        website = "no website";
      } else {
        url = 'href=\"' + jsonData['website'] + '\"';
        website = jsonData['website'];
      }

      var text = "";
      text +=
        '<div id="schoolDetailPopup" class="row">' +
        '<div class="well" style="margin: 0px; width: 100%; height: 100%; overflow-y: auto; overflow-x: hidden;">' +
        '<a href="Javascript: $(\'#schoolPopup\').fadeToggle(\'fast\', \'swing\');"><label id="closeSchoolPopup">X</label></a>' +
        '<h3 class="text-left" style="margin: 0px 0px 10px 0px;">' + jsonData['name'] + '</h3>' +
        '<div class="row">' +
        '<div class="col-xs-4 col-md-2 text-left"><label>ที่อยู่:</label></div>' +
        '<div class="col-xs-8 col-md-10 text-left">แขวง/ตำบล ' + jsonData['subdistrict'] + ' เขต/อำเภอ ' + jsonData['district'] + ' จังหวัด ' + jsonData['province'] + ' ' + jsonData['postcode'] + ' </div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-4 col-md-2 text-left"><label>สังกัด:</label></div>' +
        '<div class="col-xs-8 col-md-10 text-left">' + jsonData['type'] + '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-4 col-md-2 text-left"><label>หน่วยงาน:</label></div>' +
        '<div class="col-xs-8 col-md-10 text-left">' + jsonData['department'] + '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-4 col-md-2 text-left"><label>เบอร์ติดต่อ:</label></div>' +
        '<div class="col-xs-8 col-md-10 text-left">' + jsonData['telephone'] + '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-4 col-md-2 text-left "><label>เว็บไซต์:</label></div>' +
        '<div class="col-xs-8 col-md-10 text-left"><a target="_blank" ' + url + '>' + website + '</a></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div id="schoolItemHeadPopup" class="row">' +
        '<div class="row center" style="width: 100%; margin: 0px;">' +
        '<div class="col-xs-2 col-md-1 text-center" style="padding: 0px;"><label>ลำดับที่</label></div>' +
        '<div class="col-xs-3 col-md-4 text-center" style="padding: 0px;"><label>ชื่อเอกสาร</label></div>' +
        '<div class="col-xs-5 col-md-7 text-center" style="padding: 0px;"><label>เนื้อหาที่เกี่ยวข้องของพารากราฟนี้</label></div>' +
        '</div>' +
        '</div>' +
        '<div id="schoolItemPopup" class="row">' +
        '</div>';

      $("#schoolPopup").html(text);
    },
    async: false
  });

  $.ajax({
    type: "POST",
    url: "service/query_document_from_school.php",
    data: {
      school_id: schoolId
    },
    success: function (data) {
      //alert(data);
      jsonData = JSON.parse(data);

      text = "";

      for (var i = 0; i < Object.keys(jsonData).length; i++) {
        text +=
          '<div class="row center" style="width: 100%; margin: 0px;">' +
          '<div class="col-xs-2 col-md-1 text-center" style="padding: 0px;">' + (i + 1) + '</div>' +
          '<div class="col-xs-3 col-md-4 text-left" style="padding: 0px;"><a >' + jsonData[i]['file_name'] + '</a></div>' +
          '<div class="col-xs-5 col-md-7 text-left" style="padding: 0px;">';

        tag = jsonData[i]['tag'].split(",");
        for (var j = 0; j < tag.length; j++) {
          text += '<label class="tagLabel" style="cursor: default;">' + tag[j] + '</label>';
        }

        text +=
          '</div>' +
          '</div>' +
          '<br>';
      }

      $("#schoolItemPopup").html(text);
    },
    async: false
  });
}

function gotoSchool(schoolId, schoolName, department, lat, lon) {
  if ($('#schoolPopup').css("display") != "none") {
    $('#schoolPopup').fadeToggle('fast', 'swing');
  }
  // if (marker != null) {
  //   marker.setMap(null);
  // }
  // map.setCenter(new google.maps.LatLng(lat, lon));
  // marker = new google.maps.Marker({
  //   position: new google.maps.LatLng(lat, lon)
  // });
  // marker.setMap(map);

  if (marker != null) {
    map.Overlays.remove(marker);
  }

  map.location({lat,lon});

  let popupContent = 
    '<h3>' + schoolName + '</h3>' +
    '<p>' + department + '</p>' +
    '<a href="JavaScript: schoolPopupOpen(\'' + schoolId + '\');">เพิ่มเติม</a>';

  marker = new longdo.Marker({lat,lon},{
    title: 'รายละเอียดโรงเรียน',
    detail: popupContent,
  });

  map.Overlays.add(marker);

  // $('div.infoBox').remove();
  // infoBox = new InfoBox({
  //   content: '<div class="marker_info none" id="marker_info">' +
  //     '<div class="info" id="info">' +
  //     '<h3>' + schoolName + '</h3>' +
  //     '<p>' + department + '</p>' +
  //     '<a href="JavaScript: schoolPopupOpen(\'' + schoolId + '\');">เพิ่มเติม</a>' +
  //     '</div>' +
  //     '</div>',
  //   disableAutoPan: true,
  //   maxWidth: 0,
  //   // pixelOffset: new google.maps.Size(-150, -170),
  //   closeBoxMargin: '0px 0px',
  //   closeBoxURL: '',
  //   isHidden: false,
  //   pane: 'floatPane',
  //   enableEventPropagation: true
  // });
  // infoBox.position_ = new google.maps.LatLng(lat, lon);
  // infoBox.setMap(map);
}

$("#schoolListSelect2Multiple").select2({
  placeholder: "Select Schools",
  allowClear: true
});

$('.checklist_tag :checkbox').bind('click', function () {
  var $chk = $(this),
    $li = $chk.closest('li'),
    $ul, $parent;
  if ($li.has('ul')) {
    $li.find(':checkbox').not(this).prop('checked', this.checked)
  }

  do {
    $ul = $li.parent();
    $parent = $ul.siblings(':checkbox');
    if ($chk.is(':checked')) {
      $parent.prop('checked', $ul.has(':checkbox:not(:checked)').length == 0)
    } else {
      $parent.prop('checked', false)
    }
    $chk = $parent;
    $li = $chk.closest('li');
  } while ($ul.is(':not(.someclass)'));
});

$('#pageNo').bind('keydown', function () {
  //alert('Key pressed: ' + e.keyCode);
  if (event.keyCode == 13 || event.keyCode == 9) {
    search($(this).val());
  }
});

$('#orderBy').bind('change', function () {
  search(1);
});

$(document).ready(function () {
  $(window).resize();
});

$(window).resize(function () {

  $("span.select2.select2-container.select2-container--default").width(
    $("#keywordSearch").width() +
    12
  );

  $("#tagSearchPanel").height(
    $(".sidenav.left.fill-height").height() -
    $("span.select2.select2-container.select2-container--default").height() -
    $("#keywordSearch").height() -
    $("#submitSearch").height() -
    70
  );
});

myMap();