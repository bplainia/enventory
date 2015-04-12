<?php
if(isset($_SERVER['PATH_INFO'])){
$cat=$_SERVER['PATH_INFO'];
$cat=explode('/',$cat);
}

if(@$cat[1]=="ICs")$title="Integrated Circuts";
elseif (@$cat[1]=="transistors")$title="Transistors, SCR's";
elseif (@$cat[1]=="diodes")$title="Diodes";
else $title="Silicon Valley";
include "config.php";
include "header.php";
if(!isset($cat)){ // this catches if no second '/' 
?>
<h1>Silicon Valley</h1>
Select a Category:<ul>
<li><a href="/semiconductors.php/ICs">IC's</a></li>
<li><a href="/semiconductors.php/transistors">Transistors, SCR's</a> (3-4 layers)</li>
<li><a href="/semiconductors.php/diodes">Diodes</a> (two layers)</li>
</ul>
<?php }
elseif($cat[1]=="ICs"){
  require ".inc/ic.php";
}
elseif($cat[1]=="transistors"){
  require ".inc/transistor.php";
}
elseif($cat[1]=="diodes"){
  require ".inc/diode.php";
}
else{ //fallback if is not one of the above values ?>
<h1>Silicon Valey</h1>
Select a Category:<ul>
<li><a href="semiconductors.php/ICs">IC's</a></li>
<li><a href="semiconductors.php/transistors">Transistors, SCR's</a> (3-4 layers)</li>
<li><a href="semiconductors.php/diodes">Diodes</a> (two layers)</li>
</ul>
<?php }
//print_r($cat); //for debug purposes
//echo $sql;
?></body></html>
