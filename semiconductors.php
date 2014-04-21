<?
if(isset($_SERVER['PATH_INFO'])){
$cat=$_SERVER['PATH_INFO'];
$cat=explode('/',$cat);
}

if(@$cat[1]=="ICs")$title="Integrated Circuts";
else $title="Silicon Valley";
include "config.php";
include "header.php";
if(!isset($cat)){ // this catches if no second '/' ?>
<h1>Silicon Valley</h1>
Select a Category:<ul>
<li><a href="/semiconductors.php/ICs">IC's</a></li>
<li><a href="/semiconductors.php/transistors">Transistors, SCR's</a> (3-4 layers)</li>
<li><a href="/semiconductors.php/diodes">Diodes</a> (two layers)</li>
</ul>
<? }
elseif($cat[1]=="ICs"){
startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-') $addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update semiconductors_IC set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    $db->query($sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update semiconductors_IC set used = used ".$addval." where ID=".$_POST['id'].";";
    $db->query($sql);
  }elseif($_POST['com']=="rm"){
    $sql="delete from semiconductors_IC where ID=".$_POST['id'].";";
    $db->query($sql);
  }elseif($_POST['com']=="new"){
    //$values=explode('^',$_POST['val']);
    //$sql="insert into semiconductors_IC (quantity,number,type,Package,Pins,Description,datasheet) values ($values[0],'".urldecode($values[1])."','$values[2]','$values[3]',$values[4],\"".urldecode($values[5])."\",\"".urldecode($values[6])."\");";
    $sql="insert into semiconductors_IC (quantity,number,type,Package,Pins,Description,datasheet,user,locid) values (".$_POST['qty'].",'".$_POST['num']."','".$_POST['type']."','".$_POST['pkg']."',".$_POST['pins'].",\"".$_POST['descript']."\",\"".$_POST['dataURL']."\",'".$user."',\"".$_POST['box']."-".$_POST['boxLoc']."\");";
    $db->query($sql); //TODO: ADD ERROR REPORTING
    //print_r($_POST);
    echo "added ".$_POST['num'];
  }
}//endif AUR
if(!isset($cat[2])) $cat[2]=null;
?>
<h1>Integrated Circuts</h1>
<a href="semiconductors.php">Back to Silicon Valley</a>
<p><select onchange="goto('/semiconductors.php/ICs'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All</option><option value="/logic" <?if($cat[2]=='logic') echo "selected"; ?>>Logic Chips</option><option value="/linear" <?if($cat[2]=='linear') echo "selected"; ?>>Linear chips</option><option value="/mcu" <?if($cat[2]=='mcu') echo "selected"; ?>>Microcontrollers</option><option value="/mpu" <?if($cat[2]=='mpu') echo "selected"; ?>>Microprocessors</option><option value="/mem" <?if($cat[2]=='mem') echo "selected"; ?>>Memory</option><option value="/reg" <?if($cat[2]=='reg') echo "selected"; ?>>Regulator</option><option value="/interface" <?if($cat[2]=='interface') echo "selected"; ?>>Interface</option></select></</p>
<?
  if(isset($cat[2])) 
  {
    $sql = "select * from semiconductors_IC where (type=:type) and (user=:user) order by number;";
    $statement = $db->prepare($sql);
    $statement->execute(array(":user"=>$user,":type"=>$cat[2]));
  }
  else 
  {
    $sql="select * from semiconductors_IC where (user=:user) order by number;";
    $statement = $db->prepare($sql);
    $statement->execute(array("user"=>$user));
  }
  echo "<table border=1 class=\"sortable\"><tr><th>Qty</th><th>ID</th><th>Type</th><th>Pins</th><th>Description</th><th>Datasheet</th><th>Location</th><th>AUR</th>";
  echo   "</tr>";
  $result = $statement->fetchAll(PDO::FETCH_ASSOC);
  if(count($result)>0)
  {
      foreach($result as $row){
        echo "<tr>";
        $leftover=$row['quantity']-$row['used'];
        switch($row['type']){
          case 'linear': $type="Analog/Linear"; break;
          case 'mem': $type="Memory"; break;
          case 'mcu': $type="Microcontroller"; break;
          case 'logic': $type="Logic"; break;
          case 'mpu': $type="Microprocessor"; break;
          case 'reg': $type="Regulator"; break;
          case 'interface': $type="Interface"; break;
          case 'special': $type="See Desc."; break;
          default: $type=$row['type'];
        }
        echo "<td>".$leftover."/".$row['quantity']."</td>";
        echo "<td sorttable_customkey=\"";
        echo($row['sNumber']!="")? $row['sNumber']:$row['number'];
        echo "\">".$row['number']."</td>";
        echo "<td>".$type."</td>";
        echo "<td>".$row['Pins']."</td>";
        echo "<td class=\"description\">".$row['Description']."</td><td>";
        if(isset($row['datasheet'])){ 
            echo "<a target=\"_TAB\" href=\"";
            if(preg_match("#^http#",$row['datasheet'])) echo $row['datasheet'];
            elseif(preg_match("#^/#",$row['datasheet'])) echo "/datasheets".$row['datasheet'];
            else echo "/datasheets/".$row['datasheet'];
            echo "\">Datasheet</a>"; 
        }
        else echo "&nbsp;";
        echo "</td><td>".$row['locid']."</td>";
        echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
        echo "</tr>\n";
      }
      echo "</table>";
  }
  else
  {
    echo "None Available";
  }
  ?>
<p>
<script language="javascript">
    function getLocs(loc)
    {
        var xmlhttp;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
            if(xmlhttp.readyState==4 && xmlhttp.status==200)
            {
                document.getElementById("selection").innerHTML=xmlhttp.responseText;
            }
        }
        xmlhttp.open("GET","locations.php?cmd=select&data="+loc,true);
        xmlhttp.send();
    }
</script>
<form action="" method="post" name="reciever">Add a Part:<table border=0>
<tr><td align="right">Quantity:</td><td><input tabindex=1 type="text" name="qty" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">ID#:</td><td><input tabindex=2 type="text" name="num" /></td></tr>
<tr><td align="right">Type:</td><td><select tabindex=3 name="type"><option value="" selected disabled>Select One</option><option value="linear">Linear/Analog</option><option value="mcu">MCU</option><option value="mpu">Microprocessor</option><option value="mem">Memory</option><option value="logic">Logic Chip</option><option value="reg">Regulator</option><option value="interface">Interface</option></select></td></tr>
<tr><td align="right">Package:</td><td><!--select id="pkg"><option value="TO-3">TO-3<option vlaue="DIP">DIP<option value="SIP">SIP</select-->
<table border=1><tr>
<td valign="middle"><input  tabindex=4 type="radio" name="pkg" value="DIP" id="dipradio" /><img src="images/555.jpg" height=50  onclick="document.getElementById('dipradio').checked=true;" /></td>
<td valign="middle"><input type="radio" name="pkg" value="TO-220" id="to220radio" /><img src="images/TO-220" height="50" onclick="document.getElementById('to220radio').checked=true;" /></td>
<td valign="middle"><input type="radio" name="pkg" value="SIP" id="sipradio" /><img src="images/SIP.jpg" height=50 onclick="document.getElementById('sipradio').checked=true;" /></td>
<td valign="middle"><input type="radio" name="pkg" value="SOT-23" id="SOT23radio" /><img src="images/SOT-23-8.jpg" height="50" onclick="document.getElementById('SOT23radio').checked=true;" /></td>
</tr></table></td></tr>
<tr><td align="right">Pins:</td><td><input tabindex=5 type="text" name="pins" onkeypress="return onlyNumbers()" size=2 /></td></tr>
<tr><td align="right">Description:</td><td><textarea  tabindex=6 name="descript" onkeyup="document.getElementById('char').innerHTML='You have used '+this.value.length+' Characters.(60 recomended max)';"></textarea><div id="char">You have used 0 Characters.(60 recomended max)</div></td></tr>
<tr><td align="right">Location:</td><td>
<select name="box" onChange="getLocs(this.value)">
  <option value="0" selected disabled>Select One</option>
<?php
$statement=$db->query("SELECT * FROM locList;");
foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $row){
    echo "  <option value=\"".$row['name']."\">".$row['desc']."</option>\n";
}
 ?></select>
 <div id="locationDiv"><select name="boxLoc" id="selection"><option>Please select a Cabinent/Box</option></select></div>
 </td></tr>
<tr><td align="right">Datasheet URL:</td><td><input  tabindex=8 type="text" name="dataURL"</td></tr>
<tr><td></td><td><input type="submit"  tabindex=9 value="Add Chip"></td></tr>
<input type="hidden" name="com" value="new" />
</table></form></p>
<?
//end of write
}//end of Integrated Circuts ******************IC************END**************IC*******************END****************IC*********************************

elseif($cat[1]=="transistors"){////////////PMOS///////////////////NPN/////////////FET/////////////////PNP//////////////////////NMOS//////
startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-') $addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update semiconductors_trans set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update semiconductors_trans set used = used ".$addval." where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="rm"){
    $sql="delete from semiconductors_trans where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="new"){
    $values=$_POST;
    $sql="insert into semiconductors_trans (quantity,number,type,Package,Description,datasheet) values (".$values['qty'].",'".$values['num']."','".$values['type']."','".$values['pkg']."',\"".urldecode($values['descript'])."\",\"".urldecode($values['dataURL'])."\");";
    mysqli_query($dblink,$sql) or die(mysqli_error()."\n".$sql);
    echo "added";
  }
}//endif AUR
?>
<h1>Transistors and SCR's</h1>
<a href="semiconductors.php">Back to Silicon Valley</a>
<p><select onchange="goto('/semiconductors.php/transistors'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/NPN" <?if($cat[2]=='NPN') echo "selected"; ?>>NPN<option value="/PNP" <?if($cat[2]=='PNP') echo "selected"; ?>>PNP<option value="/SCR" <?if($cat[2]=='SCR') echo "selected"; ?>>SCRs</select></p>
<?
  startsql();
  //<here is where the cat[3] is>
  if(isset($cat[2]) && @$cat[2]!="") $sql = "select * from semiconductors_trans where type='".$cat[2]."' and ((user='".$user."') or (user is null)) order by number;";
  else $sql="select * from semiconductors_trans where (user='".$user."') or (user is null) order by number;";
  $result = mysqli_query($dblink,$sql);
  if(@mysqli_num_rows($result)>0){
  echo "<table border=1 class=\"sortable\"><thead><tr><th title=\"available/total\">Qty</th><th>ID</th><th>Type</th><th>Description</th><th class=\"sorttable_nosort\">Datasheet</th><th class=\"sorttable_nosort\">AUR</th></tr></thead><tbody>";
  while($row=@mysqli_fetch_array($result)){
    echo "<tr>";
    $leftover=$row['quantity']-$row['used']; 
    //if($leftover<0)$leftover=0; //commented because you couldn't tell what the value was
    echo "<td sorttable_customkey=\"$leftover\">".$leftover."/".$row['quantity']."</td>";
    echo "<td>".$row['number']."</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$row['Description']. "</td><td>";
    echo ($row['datasheet'])? "<a target=\"_TAB\" href=\"".$row['datasheet']."\">Datasheet</a>" : "&nbsp;";
    echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
    echo "</td></tr>\n";

  }
  echo "</tbody><tfoot></tfoot></table>";
  }else echo "none available";
//the following part is the form to add a part
?>
<p>
<form action="" method="post" name="reciever">Add a Part:<table border=0>
<tr><td align="right">Quantity:</td><td><input type="text" name="qty" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">ID#:</td><td><input type="text" name="num" /></td></tr>
<tr><td align="right">Type:</td><td><select name="type"><option value="NPN">NPN<option value="PNP">PNP<option value="SCR">SCR<option value="FET">FET<option value="NMOSFET">N-type MOSFET<option value="PMOSFET">P-type MOSFET</select></td></tr>
<tr><td align="right">Package:</td><td>
<table border=1><tr>
<td valign="middle"><input type="radio" name="pkg" value="TO-92" id="to92radio" checked/><img src="images/TO-92.jpg" height=50  onclick="document.getElementById('to92radio').checked=true;" alt="TO-92" title="TO-92" /></td>
<td valign="middle"><input type="radio" name="pkg" value="TO-220" id="to220radio" /><img src="images/TO-220" height="50" onclick="document.getElementById('to220radio').checked=true;" alt="TO-220" title="TO-220" /></td>
<td valign="middle"><input type="radio" name="pkg" value="TO-202" id="to202radio" /><img src="images/TO-202AB" height="50" onclick="document.getElementById('to202radio').checked=true;" alt="TO-202AB" title="TO-202AB" /></td>
<td valign="middle"><input type="radio" name="pkg" value="SIP" id="to3radio" /><img src="images/TO-3.jpg" height=50 onclick="document.getElementById('to3radio').checked=true;" alt="TO-3" title="TO-3" /></td>
<td valign="middle"><input type="radio" name="pkg" value="SOT-23" id="SOT23radio" /><img src="images/SOT-23.jpg" height="50" onclick="document.getElementById('SOT23radio').checked=true;" alt="SOT-23" title="SOT-23" /></td>
</tr></table></td></tr>
<tr><td align="right">Pins:</td><td><input type="text" name="pins" onkeypress="return onlyNumbers()" size=2 value="3" /></td></tr>
<tr><td align="right">Description:</td><td><textarea name="descript" ></textarea></td></tr>
<tr><td align="right">Datasheet URL:</td><td><input type="text" name="dataURL"</td></tr>
<tr><td></td><td align="center "><input type="submit" value="submit"></td></tr>
</table><input type="hidden" name="com" value="new"></form>
<? 
 //end of write
}//end of transistors ***************************************************************


elseif($cat[1]=="diodes"){/////////////NP//////////////////NP////////////////Diodes//////////////NP////////////////////////////
startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-') $addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update semiconductors_diodes set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update semiconductors_diodes set used = used ".$addval." where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="rm"){
    $sql="delete from semiconductors_diodes where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="new"){
    $values=explode('^',$_POST['val']);
    echo "added";
    $sql="insert into semiconductors_diodes (quantity,number,type,Package,Description,datasheet) values ($values[0],'".urldecode($values[1])."','$values[2]','$values[3]',\"".urldecode($values[4])."\",\"".urldecode($values[5])."\");";
    mysqli_query($dblink,$sql) or die(mysqli_error()."\n".$sql);
  }
}//endif AUR
?>
<h1>Diodes</h1>
<a href="semiconductors.php">Back to Silicon Valley</a>
<p><select onchange="goto('/semiconductors.php/diodes'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/NPN" <?if($cat[2]=='NPN') echo "selected"; ?>>NPN<option value="/PNP" <?if($cat[2]=='PNP') echo "selected"; ?>>PNP<option value="/SCR" <?if($cat[2]=='SCR') echo "selected"; ?>>SCRs</select></p>
<?
  startsql();
  //<here is where the cat[3] is>
  if(isset($cat[2]) && @$cat[2]!="") $sql = "select * from semiconductors_diodes where type='".$cat[2]."' and ((user='".$user."') or (user is null)) order by number;";
  else $sql="select * from semiconductors_diodes where (user='".$user."') or (user is null) order by number;";
  $result = mysqli_query($dblink,$sql);
  if(@mysqli_num_rows($result)>0){
  echo "<table border=1 class=\"sortable\"><thead><tr><th title=\"available/total\">Qty</th><th>ID</th><th>Type</th><th title=\"Specs\">V<sub>fwd</sub>@I<sub>max</sub></th><th>Description</th><th class=\"sorttable_nosort\">Datasheet</th><th class=\"sorttable_nosort\">AUR</th></tr></thead><tbody>";
  while($row=@mysqli_fetch_array($result)){
    echo "<tr>";
    $leftover=$row['quantity']-$row['used']; 
    //if($leftover<0)$leftover=0; //commented because you couldn't tell what the value was
    $amperage = ($row['amperage']<1)? ($row['amperage']*1000) . "m" : $row['amperage'];
    echo "<td>".$leftover."/".$row['quantity']."</td>";
    echo "<td sorttable_customkey=\"".$row['number']."\">".$row['number']."</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$row['voltage']."V@".$amperage."A</td>";
    echo "<td>".$row['Description']."</td><td>";
    echo (isset($row['datasheet']))? "<a target=\"_TAB\" href=\"".$row['datasheet']."\">Datasheet</a>" : "&nbsp;";
    echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
    echo "</td></tr>\n";

  }
  $result=mysqli_fetch_array(mysqli_query($dblink,"select sum(quantity)-sum(used) as total, sum(quantity) as summation from semiconductors_diodes where user='".$user."'"));
  echo "</tbody><tfoot><tr><td>".$result['total']."/".$result['summation']."</td></tr></tfoot></table>";
  }else echo "none available";
//the following part is the form to add a part
?>
<p>
<script language="javascript">
// <!--
function sendFrm(){
var dataURL=encodeURI(document.getElementById('dataURL').value);
var descript=encodeURI(document.getElementById('descript').value);
document.getElementById('val').value=document.getElementById('qty').value+"^"+encodeURI(document.getElementById('num')).value+"^"+document.reciever.pkg.value+"^"+document.getElementById('type').value+"^"+document.getElementById('pins').value+"^"+descript+"^"+dataURL;
//quantity,number,type,Package,Pins,Description,datasheet
document.sender.submit();
//alert(document.sender.val.value);
}
//-->
</script>
<form action="javascript:sendFrm()" name="reciever">Add a Part: <font color="red">DO NOT USE YET</font><table border=0>
<tr><td align="right">Quantity:</td><td><input type="text" id="qty" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">ID#:</td><td><input type="text" id="num" /></td></tr>
<tr><td align="right">Type:</td><td><select id="type"><option value="NPN">NPN<option value="PNP">PNP<option value="SCR">SCR<option value="FET">FET<option value="NMOSFET">N-type MOSFET<option value="PMOSFET">P-type MOSFET</select></td></tr>
<tr><td align="right">Package:</td><td>
<table border=1><tr>
<td valign="middle"><input type="radio" name="pkg" value="DO-35" id="do35radio" checked/><img src="images/DO-35.jpg" height=50  onclick="document.getElementById('do35radio').checked=true;" alt="DO-35" title="DO-35" /></td>
<td valign="middle"><input type="radio" name="pkg" value="TO-220" id="to220radio" /><img src="images/TO-220" height="50" onclick="document.getElementById('to220radio').checked=true;" alt="TO-220" title="TO-220" /></td>
<td valign="middle"><input type="radio" name="pkg" value="TO-202" id="to202radio" /><img src="images/TO-202AB" height="50" onclick="document.getElementById('to202radio').checked=true;" alt="TO-202AB" title="TO-202AB" /></td>
<td valign="middle"><input type="radio" name="pkg" value="SIP" id="to3radio" /><img src="images/TO-3.jpg" height=50 onclick="document.getElementById('to3radio').checked=true;" alt="TO-3" title="TO-3" /></td>
<td valign="middle"><input type="radio" name="pkg" value="SOT-23" id="SOT23radio" /><img src="images/SOT-23.jpg" height="50" onclick="document.getElementById('SOT23radio').checked=true;" alt="SOT-23" title="SOT-23" /></td>
</tr></table></td></tr>
<tr><td align="right">Description:</td><td><textarea id="descript" ></textarea></td></tr>
<tr><td align="right">Datasheet URL:</td><td><input type="text" id="dataURL"</td></tr>
<tr><td></td><td align="right"><input type="submit" value="submit"></td></tr>
</table></form>
<form name="sender" action="" method="post">
<input type="hidden" id="val" name="val">
<input type="hidden" name="com" value="new">
</form>
<?

 //end of write
}//end of diodes ***************************************************************



else{ //fallback if is not one of the above values ?>
<h1>Silicon Valey</h1>
Select a Category:<ul>
<li><a href="semiconductors.php/ICs">IC's</a></li>
<li><a href="semiconductors.php/transistors">Transistors, SCR's</a> (3-4 layers)</li>
<li><a href="semiconductors.php/diodes">Diodes</a> (two layers)</li>
</ul>
<? }
//print_r($cat); //for debug purposes
//echo $sql;
?></body></html>
