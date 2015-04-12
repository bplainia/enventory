<?php
startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-') $addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update passives_ind set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update passives_ind set used = used ".$addval." where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="rm"){
    $sql="delete from passives_ind where ID=".$_POST['id'].";";
    mysqli_query($dblink,$sql);
  }elseif($_POST['com']=="new"){
    if($_POST['valtype']==0) $code="true"; else $code="false";
    $sql="insert into passives_ind (quantity,Used,value,voltage,type,Color,comment,code,Manufacturer,user) values (".$_POST['quantity'].",".$_POST['used'].",".$_POST['value'].",'".$_POST['volt']."','".$_POST['type']."','".$_POST['color']."',\"".$_POST['comment']."\",$code,'".$_POST['manu']."','".$user."');";
    mysqli_query($dblink,$sql) or die(mysql_error()."\n".$sql);

  }
}//endif AUR
?>
<h1>Inductors</h1>
<a href="passives.php">Back to passives home</a>
<p><select onchange="goto('passives.php/inductors'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/radial" <?if(@$cat[2]=='radial') echo "selected"; ?>>radial<option value="/axial" <?if(@$cat[2]=='axial') echo "selected"; ?>>axial<option value="/ceramic" <?if(@$cat[2]=='ceramic') echo "selected"; ?>>ceramic</select></p>
<table border=1 class="tablesorter" id=\"myTable\"><tr><th>Qty</th><th>Value</th><th>Type</th><th>Dimentions</th><th>Manufacturer</th><th>Comment</th><th class="{sorter: false}">AUR</th></tr>
<?
  if(isset($cat[2])) $sql = "select * from passives_ind where type='".$cat[2]."' and ((user='".$user."') or (user is null))  order by value;";
  else $sql="select * from passives_ind where (user='".$user."') or (user is null) order by value;";
  $result = $db->query($sql);
  foreach($result->fetchAll(PDO::FETCH_ASSOC) as $row){
    if($row['code']) list($value,$valraw)=codetovalue($row['value']);
    else { $value=$row['value']."&mu;F"; $valraw=$row['value']*pow(10,-6);}
    echo "<tr>";
    echo "<td>".$row['quantity']."</td>";
    echo "<td customkey=\"".number_format($valraw,13)."\">".$value."</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$row['length']."x".$row['width']."x".$row['height']."r".$row['radius']."</td>";
    echo "<td>".$row['Manufacturer']."</td>";
    echo "<td>".$row['Comments'];
    if($row['code']) echo " (".$row['value'].")";
    echo "</td>";
    echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
    echo "</tr>\n";
    $valraw=null;
  }
echo "</table>"; ?>
<h2>Add Inductor</h2>
<script language="javascript">
//<!--
function checkType(type){
return false;
}
//-->
</script>
<form name="addInd" method="post" action=""><table>
<tr><td align="right">Quantity:</td><td><input tabindex=1 type="text" name="quantity" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">Used:</td><td><input tabindex=2 type="text" name="used" size=2 onkeypress="return onlyNumbers()" value="0" /></td></tr>
<tr><td align="right">Type:</td><td><table border=1><tr>
<td valign="middle"><input  tabindex=3 type="radio" name="type" value="radial" id="radialradio" /><img src="images/radialInductor.jpg" height="50"  onclick="document.getElementById('radialradio').checked=true;" title="Radial Inductor"/></td>
<td valign="middle"><input type="radio" name="type" value="choke" id="chokeradio" /><img src="images/choke.jpg" height="50" onclick="document.getElementById('chokeradio').checked=true;" title="Choke"/></td>
<td valign="middle"><input type="radio" name="type" value="htoroid" id="htoroidradio" /><img src="images/hToroid.jpg" height="50" onclick="document.getElementById('htoroidradio').checked=true;" title="Horizontal Toroid"/></td>
<td valign="middle"><input type="radio" name="type" value="vtoroid" id="vtoroidradio" /><img src="images/vToroid.jpg" height="50" onclick="document.getElementById('vtoroidradio').checked=true;" title="Vertical Toroid"/></td>
<td valign="middle"><input type="radio" name="type" value="other" id="otherradio" /><img src="images/question.jpg" height="50" onclick="document.getElementById('otherradio').checked=true;" title="Other. Please put description in comments" /></td>
</tr></table></td></tr>
<tr><td align="right">Value:</td><td><input tabindex=4 size=3 type="text" name="value" /><select name="valtype" tabindex="5"><option value="-6">&mu;H</option><option value="-3">mH</option><option value=0>code</option></select></td></tr>
<tr><td align="right">Current:</td><td><input tabindex=6 type="text" name="current" size=2 onkeypress="return onlyNumbers()" /> Amps</td></tr>
<tr><td align="right">Power Rating:</td><td><input tabindex=7 type="text" name="watt" size=2 onkeypress="return onlyNumbers()" /> Watts</td></tr>
<tr><td align="right">Color:</td><td><input type="text" maxlength=15 tabindex=8 name="color"></select></td></tr>
<tr><td align="right">Comment:</td><td><textarea tabindex=9 name="comment"></textarea></td></tr>
<tr><td align="right">Manufacturer:</td><td><input tabindex=10 type="text" maxlength=15 name="manu"/></td></tr>
<tr><td align="right">Man. #:</td><td><input tabindex=11 type="text" name="manNum"/></td></tr>
<tr><td><input type="hidden" name="com" value="new" /></td><td><input tabindex=12 type="submit" value="Add Inductor" /></td></tr>
</table></form>
<?php 
}
else{ //fallback if is not one of the above values ?>
<h1>Pasives</h1>
Select a Category:<ul>
<li><a href="passives.php/capacitors">Capacitors</a></li>
<li><a href="passives.php/resistors">Resistors</a></li>
<li><a href="passives.php/inductors">Inductors</a></li>
</ul>