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
<h1>Silicon Valey</h1>
Select a Category:<ul>
<li><a href="semiconductors.php/ICs">IC's</a></li>
<li><a href="semiconductors.php/transistors">Transistors, SCR's</a> (3-4 layers)</li>
<li><a href="semiconductors.php/diodes">Diodes</a> (two layers)</li>
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
    mysql_query($sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update semiconductors_IC set used = used ".$addval." where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="rm"){
    $sql="delete where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="new"){
    //$values=explode('^',$_POST['val']);
    echo "added";
    //$sql="insert into semiconductors_IC (quantity,number,type,Package,Pins,Description,datasheet) values ($values[0],'".urldecode($values[1])."','$values[2]','$values[3]',$values[4],\"".urldecode($values[5])."\",\"".urldecode($values[6])."\");";
    $sql="insert into semiconductors_IC (quantity,number,type,Package,Pins,Description,datasheet) values (".$_POST['qty'].",'".urldecode($_POST['num'])."','".$_POST['type']."','".$_POST['pkg']."',".$_POST['pins'].",\"".urldecode($_POST['descript'])."\",\"".urldecode($_POST['dataURL'])."\");";
    mysql_query($sql) or die(mysql_error()."\n".$sql);
  }
}//endif AUR
?>
<h1>Integrated Circuts</h1>
<a href="semiconductors.php">Back to Silicon Valley</a>
<p><select onchange="goto('/semiconductors.php/ICs'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/logic" <?if($cat[2]=='logic') echo "selected"; ?>>Logic Chips<option value="/linear" <?if($cat[2]=='linear') echo "selected"; ?>>Linear chips<option value="/mcu" <?if($cat[2]=='mcu') echo "selected"; ?>>MCU's</select></p>
<?
  if(isset($cat[2])) $sql = "select * from semiconductors_IC where type='".$cat[2]."' order by number;";
  else $sql="select * from semiconductors_IC order by number;";
  $result = mysql_query($sql);
  if(@mysql_num_rows($result)){
  echo "<table border=1 class=\"sortable\"><tr><th>Qty</th><th>ID</th><th>Type</th><th>Pins</th><th>Description</th><th>Datasheet</th><th>AUR</th></tr>";
  while($row=@mysql_fetch_array($result)){
    echo "<tr>";
    $leftover=$row['quantity']-$row['used'];
    echo "<td>".$leftover."/".$row['quantity']."</td>";
    echo "<td sorttable_customkey=\"".$row['number']."\">".$row['number']."</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$row['Pins']."</td>";
    echo "<td>".$row['Description']."</td><td>";
    echo (isset($row['datasheet']))? "<a target=\"_TAB\" href=\"".$row['datasheet']."\">Datasheet</a>" : "&nbsp;";
    echo "</td>";
    echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
    echo "</tr>\n";

  }
  echo "</table>";
  }else echo "none available";

}
elseif($cat[1]=="transistors"){ ?>
<h1>Transistors and SRC's</h1>
<a href="semiconductors.php">Back to Silicon Valley</a>
<p><select onchange="goto('/semiconductors.php/transistors'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/NPN" <?if($cat[2]=='NPN') echo "selected"; ?>>NPN<option value="/PNP" <?if($cat[2]=='PNP') echo "selected"; ?>>PNP<option value="/SCR" <?if($cat[2]=='SCR') echo "selected"; ?>>SCRs</select></p>
<?
  startsql();
  //<here is where the cat[3] is>
  if(isset($cat[2]) && @$cat[2]!="") $sql = "select * from semiconductors_trans where type='".$cat[2]."' order by number;";
  else $sql="select * from semiconductors_trans order by number;";
  $result = mysql_query($sql);
  if(@mysql_num_rows($result)>0){
  echo "<table border=1 class=\"sortable\"><tr><th>Qty</th><th>ID</th><th>Pins</th><th>Description</th><th>Datasheet</th></tr>";
  while($row=@mysql_fetch_array($result)){
    echo "<tr>";
    echo "<td>".$row['quantity']."</td>";
    echo "<td sorttable_customkey=\"".$row['number']."\">".$row['number']."</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$row['Description']."</td><td>";
    echo (isset($row['datasheet']))? "<a target=\"_TAB\" href=\"".$row['datasheet']."\">Datasheet</a>" : "&nbsp;";
    echo "</td></tr>\n";

  }
  echo "</table>";
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
alert(document.sender.val.value);
}
//-->
</script>
<form action="" method="post" name="reciever">Add a Part:<table border=0>
<tr><td align="right">Quantity:</td><td><input type="text" id="qty" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">ID#:</td><td><input type="text" id="num" /></td></tr>
<tr><td align="right">Type:</td><td><select id="type"><option value="linear">linear<option>mcu<option>mem<option>logic<option>processor</select></td></tr>
<tr><td align="right">Package/Pins:</td><td><!--select id="pkg"><option value="TO-3">TO-3<option vlaue="DIP">DIP<option value="SIP">SIP</select-->
<input type="radio" name="pkg" value="DIP" id="dipradio"/><label for="dipradio"><img src="555.jpg" height=50 /></label><input type="radio" name="pkg" value="TO-220" id="to220radio" /><label for="to220radio"><img src="TO-220" height="50" /></label><label for="sipradio"><input type="radio" name="pkg" value="SIP" id="sipradio" /><img src="SIP.jpg" height=50 />SIP</label>
<input type="text" id="pins" onkeypress="return onlyNumbers()" size=2 /></td></tr>
<tr><td align="right">Description:</td><td><textarea id="descript" ></textarea></td></tr>
<tr><td align="right">Datasheet URL:</td><td><input type="text" id="dataURL"</td></tr>
<tr><td></td><td align="right"><input type="submit" value="submit"></td></tr>
<input type="hidden" name="com" value="new" />
</table></form>
<form name="sender" action="" method="post">
<input type="hidden" id="val" name="val">
<input type="hidden" name="com" value="new">
</form>
<?

}//end of transistors ***************************************************************
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