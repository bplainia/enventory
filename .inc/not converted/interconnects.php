<?
if(isset($_SERVER['PATH_INFO'])){
$cat=$_SERVER['PATH_INFO'];
$cat=explode('/',$cat);
}

$title="Interconnects";
include "config.php";
include "header.php";

?>
<h1>Interconnects</h1>
<p><select onchange="goto('/enventory/interconnects.php'+this.value,false)"><option value="">All</option><option value="/Adapter" <?if(@$cat[1]=="Adapter")echo "selected"?>>Adapter</option><option value="/AV" <?if(@$cat[1]=="AV")echo "selected"?>>AV</option><option value="/Circular" <?if(@$cat[1]=="Circular")echo "selected"?>>Circular</option><option value="/D-Sub" <?if(@$cat[1]=="D-Sub")echo "selected"?>>D-Sub</option><option value="/Socket" <?if(@$cat[1]=="Socket")echo "selected"?>>Socket</option><option value="/Network" <?if(@$cat[1]=="Network")echo "selected" ?>>Network/Telecom</option><option value="/PCB" <?if(@$cat[1]=="PCB")echo "selected" ?>>PCB</option><option value="/Power" <?if(@$cat[1]=="Power")echo "selected" ?>>Power</option><option value="/Rectangular" <?if(@$cat[1]=="Rectangular")echo "selected" ?>>Rectangular</option><option value="/RF" <?if(@$cat[1]=="RF")echo "selected" ?>>RF</option><option value="/Terminal-Block" <?if(@$cat[1]=="Terminal-Block")echo "selected" ?>>Terminal Block</option><o</p><option value="/Terminal" <?if(@$cat[1]=="Terminal")echo "selected"?>>Terminal</option></select>
<?
  startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-') $addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update interconnects set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update interconnects set used = used ".$addval." where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="rm"){
    $sql="delete from interconnects where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="new"){
    $datasheet = ($_POST['datasheet']=="")? "null" : "\"".$_POST['datasheet']."\"";
    $sql="insert into interconnects (quantity,used,voltage,amperage,type,name,description,manufacturer,datasheet,user) values (".$_POST['quantity'].",".$_POST['used'].",".$_POST['volt'].",".$_POST['amp'].",'".$_POST['type']."','".$_POST['name']."',\"".$_POST['description']."\",'".$_POST['manu']."',$datasheet,'".$user."');";
//echo $sql;
    mysql_query($sql) or die(mysql_error()."\n".$sql);

  }
}//endif AUR
  if(@$cat[1]) $sql = "select * from interconnects where (type='".$cat[1]."') and ((user='".$user."') or (user is null));";
  else $sql="select * from interconnects where (user='".$user."') or (user is null);";
  $result = mysql_query($sql);
  if(mysql_num_rows($result)){
  echo "<table border=1 class=\"sortable\"><thead><tr><th>Qty</th><th>Name</th><th>Type</th><th>Specs</th><th>Manuf</th><th>Description</th><th class=\"sorttable_nosort\">Datasheet</th><th class=\"sorttable_nosort\">AUR</th></tr></thead><tbody>";
  while($row=mysql_fetch_array($result)){
    echo "<tr>";
    $unused=$row['quantity']-$row['used'];
    if($unused<0)$unused=0;
    echo "<td>".$unused."/".$row['quantity']."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$row['amperage']."mA@".$row['voltage']."V</td>";
    echo "<td>".$row['manufacturer']."</td>";
    echo "<td>".$row['description']."</td><td>";
    echo (isset($row['datasheet']))? "<a target=\"_TAB\" href=\"".$row['datasheet']."\">Datasheet</a>" : "&nbsp;";
    echo "</td>";
    echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
    echo "</tr>\n";

  }
$result=mysql_fetch_array(mysql_query("select sum(quantity)-sum(used) as total, sum(quantity) as summation from interconnects where user='".$user."'"));
echo "</tbody><tfoot><tr><td>".$result['total']."/".$result['summation']."</td></tr></tfoot></table>";
}else echo "<p>No Interconnects in Database</p>";
?>
<h2>Add Interconnect</h2>
<form name="addCap" method="post" action=""><table>
<tr><td align="right">Quantity:</td><td><input tabindex=1 type="text" name="quantity" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">Used:</td><td><input tabindex=2 type="text" name="used" size=2 onkeypress="return onlyNumbers()" value="0" /></td></tr>
<tr><td align="right">Type:</td><td><select tabindex=6 name="type"><option value="" disabled selected>Please Select One</option><option value="Adapter">Adapter</option><option value="AV">AV</option><option value="Circular">Circular</option><option value="D-Sub">D-Sub</option><option value="Socket">Socket</option><option value="Network">Network</option><option value="PCB">PCB</option><option value="Power">Power</option><option value="Rectangular">Rectangular</option><option value="RF">RF</option><option value="Terminal-Block">Terminal-Block</option><option value="Terminal">Terminal</option></select></td></tr>
<tr><td align="right">Amperage:</td><td><input tabindex=6 type="text" name="amp" size=2 onkeypress="return onlyNumbers()" /> milliAmps</td></tr>
<tr><td align="right">Voltage:</td><td><input tabindex=6 type="text" name="volt" size=2 onkeypress="return onlyNumbers()" /> Volts</td></tr>
<tr><td align="right">Name:</td><td><input type="text" maxlength=50 tabindex=7 name="name"></td></tr>
<tr><td align="right">Description:</td><td><textarea tabindex=8 name="description"></textarea></td></tr>
<tr><td align="right">Manufacturer:</td><td><input tabindex=9 type="text" maxlength=50 name="manu"/></td></tr>
<tr><td align="right">Datasheet:</td><td><input tabindex=9 type="text" name="data"/></td></tr>
<tr><td><input type="hidden" name="com" value="new" /></td><td><input tabindex=10 type="submit" value="Add Part" /></td></tr>
</table></form>
</body></html>
