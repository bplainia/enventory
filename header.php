<? @session_start(); 
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
<link rel="Shortcut Icon" src="favicon.ico" />
<link href="style.css" type="text/css" rel="stylesheet" >
<script src="sorttable.js" type="text/javascript" ></script>
<script language="javascript">
//<!--

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
