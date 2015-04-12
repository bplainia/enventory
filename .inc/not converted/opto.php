<?php
if(isset($_SERVER['PATH_INFO'])){
$cat=$_SERVER['PATH_INFO'];
$cat=explode('/',$cat);
}
if(@$cat[1]=="led") $title="LED's";
else $title="Opto-Electronics";
include "config.php";
include "header.php";

////////////////////led////////////////////////////LED////////////////////////////////////////////////////////////////////////
if($cat[1]=="led"){
  require ".inc/led.php";
}
else {
?>
<h1>Opto-Electronics</h1>
<p><font color="red">Under Development</font></p>
<ul>
<li><a href="/opto.php/led">LED's</a></li>
<li>LED Displays</li>
<li>Liquid Crystal Displays</li>
<li>Sensors</li>
<?php } ?>
</body></html>
