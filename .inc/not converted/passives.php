<?php
if(isset($_SERVER['PATH_INFO'])){
$cat=$_SERVER['PATH_INFO'];
$cat=explode('/',$cat);
}
if(@$cat[1]=="capacitors") $title="Capacitors";
elseif(@$cat[1]=="resistors") $title="Resistors";
elseif(@$cat[1]=="inductors") $title="Inductors";
else $title="Passives";
include "config.php";
include "header.php";
if(!isset($cat) || @$_POST['gomain']){ ?>
<h1>Passives</h1>
Select a Category:<ul>
<li><a href="passives.php/capacitors">Capacitors</a></li>
<li><a href="passives.php/resistors">Resistors</a></li>
<li><a href="passives.php/inductors">Inductors</a></li>
</ul>
<?php }
elseif($cat[1]=="capacitors"){ /////////////////////////cap////////////////////////cap////////////////////////////////cap/////// 
  require ".inc/capacitors.php";
}
//////////////////////////////////////////////res/////////////////////////////////////////res///////////////////////////////
elseif($cat[1]=="resistors"){ 
  require ".inc/resistors.php";
}
elseif($cat[1]=="inductors"){ //////////////////////////inductors//////////////////////////////////inductors//////////////////////////////////
  require ".inc/inductors.php";
}
//print_r($cat); //for debug purposes
?></body></html><!--×-->
