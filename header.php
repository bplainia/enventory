<?php
@session_start(); 
/*if (!isset($_SERVER['PHP_AUTH_USER'])) {
	for($tries=0;$tries>3;$tries++){
    header('WWW-Authenticate: Basic realm="spotlight.int"');
    if(in_array(strtolower(stripslashes($_SERVER['PHP_AUTH_USER'])),$users)) quit;
 }
 	//failed authentication or pressed cancel
    header('HTTP/1.0 401 Unauthorized');
    $unauth=true;
}*/
if(!isset($titlepng))$titlepng="enventory.png"; //custom title picture
if(!isset($bgpic))$bgpic="back.gif"; //custom background
?>
<!DOCTYPE html>
<html><head>
<title>eNVENTORY<? if(isset($title)) echo " - " . $title; ?></title>
<base href="http://enventory.int/" />
<meta http-equiv="author" content="Benjamin Plain" />
<link rel="Shortcut Icon" src="/favicon.ico" />
<link href="/css/style.css" type="text/css" rel="stylesheet" >
<link rel="stylesheet" href="/css/theme.green.css">
<script src="/js/jquery-2.1.1.js" type="text/javascript" ></script>
<!--script src="/jquery.inlineEdit.js" type="text/javascript" ></script-->
<script src="/js/jquery.tablesorter.js" type="text/javascript" ></script>
<script src="/js/jquery.tablesorter.widgets.js" type="text/javascript" ></script>
<script src="/addons/pager/jquery.tablesorter.pager.js"></script>
<script src="/js/parsers/parser-metric.js"></script>
<script language="javascript">
//<!--

$(document).ready(function() 
{
//$(function(){

  $.tablesorter.addParser({
    // set a unique id
    id: 'customkey',
    is: function(s) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      return $(cell).data('customkey');
    },
    parsed: false,
    type: 'numeric'
  });
$.tablesorter.addParser({
    // set a unique id
    id: 'customtext',
    is: function(s) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      var $cell = $(cell);
      return $cell.attr('customkey');
    },
    parsed: false,
    type: 'text'
  });

  /*$('#myTable').tablesorter({
    theme: 'green',
    widgets: ['stickyHeader','zebra'],
    widgetOptions: {
      saveSort: true,
      stickyHeaders: "tablesorter-stickyHeader"
    }
  });
  alert("done loading table");*/
  $(".tablesorter")
    .tablesorter({
      theme : 'green',
      widgets : ['stickyHeader','zebra']
    })
    .tablesorterPager({
      container: $("#pager"),
      positionFixed: false
    });
});

function goto(place,phper){
if(phper) window.location=place+".php";
else window.location=place;
}
/*
function goto(place,page){
window.location=place+".php?page="+page;
}*/

function onlyNumbers(evt) //also below 32
{
  var e = event || evt; // for trans-browser compatibility
  var charCode = e.which || e.keyCode;
  //alert(charCode);
  if (charCode==45) return true;
  if (charCode > 31 && (charCode < 48 || charCode > 57))
    return false;
  return true;

}

<? echo @$addscript; ?>

//-->
</script>
</head>
<body background="<? echo $bgpic; ?>">
<p align="center" style="line-height:200%;">
<a href="semiconductors.php">Semiconductors</a> | <a href="passives.php">Passives</a> | <a href="electromech.php">Electromechanical</a> | <a href="interconnects.php">Interconnects</a> | <a href="opto.php">Optos</a> | <a href="kits.php">Kits, Boards</a> | <a href="reports.php">Reports</a> | <a href="/?switch=1">Switch User</a><br/>
<? if(isset($title)) echo "<a href=\"\">";
echo "<img border=0 margin=\"12px\" src=\"$titlepng\" />";
if(isset($title)) echo "</a></p>";
if(@$unauth){
	echo "<p>You are unauthorized to veiw this website. If you think there is something wrong, contact ben.</p>";	
	die();	
	}
