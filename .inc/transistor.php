<?php
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
<tr><td class="labels">Quantity:</td><td><input type="text" name="qty" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td class="labels">ID#:</td><td><input type="text" name="num" /></td></tr>
<tr><td class="labels">Type:</td><td><select name="type"><option value="NPN">NPN<option value="PNP">PNP<option value="SCR">SCR<option value="FET">FET<option value="NMOSFET">N-type MOSFET<option value="PMOSFET">P-type MOSFET</select></td></tr>
<tr><td class="labels">Package:</td><td>
<table border=1><tr>
<td class="border"><input type="radio" name="pkg" value="TO-92" id="to92radio" checked/><img src="images/TO-92.jpg" height=50  onclick="document.getElementById('to92radio').checked=true;" alt="TO-92" title="TO-92" /></td>
<td class="border"><input type="radio" name="pkg" value="TO-220" id="to220radio" /><img src="images/TO-220" height="50" onclick="document.getElementById('to220radio').checked=true;" alt="TO-220" title="TO-220" /></td>
<td class="border"><input type="radio" name="pkg" value="TO-202" id="to202radio" /><img src="images/TO-202AB" height="50" onclick="document.getElementById('to202radio').checked=true;" alt="TO-202AB" title="TO-202AB" /></td>
<td class="border"><input type="radio" name="pkg" value="SIP" id="to3radio" /><img src="images/TO-3.jpg" height=50 onclick="document.getElementById('to3radio').checked=true;" alt="TO-3" title="TO-3" /></td>
<td class="border"><input type="radio" name="pkg" value="SOT-23" id="SOT23radio" /><img src="images/SOT-23.jpg" height="50" onclick="document.getElementById('SOT23radio').checked=true;" alt="SOT-23" title="SOT-23" /></td>
</tr></table></td></tr>
<tr><td class="labels">Pins:</td><td><input type="text" name="pins" onkeypress="return onlyNumbers()" size=2 value="3" /></td></tr>
<tr><td class="labels">Description:</td><td><textarea name="descript" ></textarea></td></tr>
<tr><td class="labels">Datasheet URL:</td><td><input type="text" name="dataURL"</td></tr>
<tr><td></td><td class="button"><input type="submit" value="submit"></td></tr>
</table><input type="hidden" name="com" value="new"></form>