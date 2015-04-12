<?php

/*if (!isset($_SERVER['PHP_AUTH_USER'])) {
	for($tries=0;$tries>3;$tries++){
    header('WWW-Authenticate: Basic realm="spotlight.int"');
    if(in_array(strtolower(stripslashes($_SERVER['PHP_AUTH_USER'])),$users)) quit;
 }
 	//failed authentication or pressed cancel
    header('HTTP/1.0 401 Unauthorized');
    $unauth=true;
}*/

//                                                              // go through variables to make sure they exist
if(!isset($titlepng))
{
  $titlepng="/images/enventory.png";                                    //custom title picture
}
if(!isset($user))
{
  $user = "Visitor";
}
if(!isset($addscript))
{
  $addscript="";                                                // no script
}
if(!isset($bgpic))
{
  $bgpic="back.gif";                                            //custom background
}
$title = $pageObj->title();
// now to designate the header template and declare it
$header = <<<EOD
<!DOCTYPE html>
<html>
<head>
<title>$title</title>
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

\$(document).ready(function() 
{
//\$(function(){

  \$.tablesorter.addParser({
    // set a unique id
    id: 'customkey',
    is: function(s) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      return \$(cell).data('customkey');
    },
    parsed: false,
    type: 'numeric'
  });
\$.tablesorter.addParser({
    // set a unique id
    id: 'customtext',
    is: function(s) {
      // return false so this parser is not auto detected
      return false;
    },
    format: function(s, table, cell, cellIndex) {
      var \$cell = \$(cell);
      return \$cell.attr('customkey');
    },
    parsed: false,
    type: 'text'
  });

  /*\$('#myTable').tablesorter({
    theme: 'green',
    widgets: ['stickyHeader','zebra'],
    widgetOptions: {
      saveSort: true,
      stickyHeaders: "tablesorter-stickyHeader"
    }
  });
  alert("done loading table");*/
  \$(".tablesorter")
    .tablesorter({
      theme : 'green',
      widgets : ['stickyHeader','zebra']
    })
    .tablesorterPager({
      container: \$("#pager"),
      positionFixed: false
    });
    \$('.topMenu > li').bind('mouseover', openSubMenu);
    \$('.topMenu > li').bind('mouseout', closeSubMenu);
    function openSubMenu() {
      \$(this).find('ul').css('visibility','visible');
    }
    function closeSubMenu() {
      \$(this).find('ul').css('visibility','hidden');
    }
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

$addscript

//-->
</script>
</head>
<body background="$bgpic">
<div class="topBackground"><div class="topGreet">Hello, $user<br></div>
<a href="/"><img border=0 margin="12px" src="$titlepng" /></a>
<div><ul class="topMenu">
  <li><a href="/">Home</a></li>
  <li><a href="/semicondctors">Semiconductors</a><ul>
    <li><a href="/diodes">Diodes</a></li>
    <li><a href="/led">LED's</a></li>
    <li><a href="/transistors">Transistors</a></li>
    <li><a href="/ics">IC's</a></li>
  </ul></li>
  <li><a href="/passive">Passive Components</a><ul>
    <li><a href="/resistors">Resistors</a></li>
    <li><a href="/capacitors">Capacitors</a></li>
    <li><a href="/inductors">Inductors</a></li>
  </ul></li>
  <li><a href="#">Electromechanical</a></li>
  <li><a href="#">Interconnects</a></li>
  <li><a href="#">Boards</a></li>
  <li><a href="#">Locations</a></li>
  <li><a href="#">Reports</a></li>
  <li><a href="#">Manage Orders</a><ul>
    <li><a href="#">Manufacturers</a></li>
    <li><a href="#">Sellers</a></li>
    <li><a href="#">Orders</a></li>
  </ul></li>
</ul></div>
</div>
<p>&nbsp;</p>
EOD;
// end of document

$footer = "</body></html>";