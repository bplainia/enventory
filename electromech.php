<?php 
include "config.php";
if(isset($_SERVER['PATH_INFO'])){
$cat=$_SERVER['PATH_INFO'];
$cat=explode('/',$cat);
}
if(@$cat[1]=="motors") $title="Motors";
else $title="Passives";

include "header.php";
if(!isset($cat)){ ?>
<h1>Electro-Mechanical</h1>
<p><font color="red">Under Development</font></p>
<ul><li><a href="electromech.php/motors">Motors</a></li>
<li>Switches</li>
<li>Nuts and Bolts</li>
</ul>
<?php
}


elseif($cat[1]=="motors"){ ?>
<h1>Motors</h1>
<p><a href="/enventory/electromech.php">Back to Electro-Mechanical Index</a></p>
<p><select onchange="goto('/enventory/electromech.php/motors'+this.value,false)"><option value="">All</option><option value="/Direct" <?php if(@$cat[2]=="Direct") echo "selected"; ?>>Direct</option><option value="/Gearhead" <?php if(@$cat[2]=="Gearhead")echo "selected"; ?>>Gearhead</option><option value="/Servo" <?php if(@$cat[2]=="Servo") echo "selected"; ?>>Servos</option><option value="/Bi-Stepper" <?php if(@$cat[2]=="Bi-Stepper") echo "selected"; ?>>Bipolar Stepper</option><option value="/Uni-Stepper" <?php if(@$cat[2]=="Uni-Stepper") echo "selected"; ?>>Unipole Stepper</option><option value="/Vibrating" <?php if(@$cat[2]=="Vibrating") echo "selected"; ?>>Vibrators</option></select>
<?php
startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-') $addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update electromech_motors set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update electromech_motors set used = used ".$addval." where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="rm"){
    $sql="delete from electromech_motors where ID=".$_POST['id'].";";
    mysql_query($sql);
  }elseif($_POST['com']=="new"){
    $datasheet = ($_POST['data']=="")? "null" : "\"".$_POST['data']."\"";
    $sql="insert into electromech_motors (quantity,used,voltage,amperage,type,name,description,manufacturer,datasheet,user) values (".$_POST['quantity'].",".$_POST['used'].",".$_POST['volt'].",".$_POST['amp'].",'".$_POST['type']."','".$_POST['name']."',\"".$_POST['description']."\",'".$_POST['manu']."',$datasheet,'".$user."');";
//echo $sql;
    mysql_query($sql) or die(mysql_error()."\n".$sql);

  }
}//endif AUR
  if(@$cat[2]) $sql = "select * from electromech_motors where (type='".$cat[2]."') and (user='".$user."');";
  else $sql="select * from electromech_motors where user='".$user."';";
  $result = mysql_query($sql);
  if(mysql_num_rows($result)){
  echo "<table border=1 class=\"sortable\"><thead><tr><th>Qty</th><th>Name</th><th>Type</th><th>Specs</th><th>Manuf</th><th>Description</th><th class=\"sorttable_nosort\">Datasheet</th><th class=\"sorttable_nosort\">AUR</th></tr></thead><tbody>";
  while($row=mysql_fetch_array($result)){
    echo "<tr>";
    $unused=$row['quantity']-$row['used'];
    $amperage = ($row['amperage']<1)? ($row['amperage']*1000) . "m" : $row['amperage'];
    if($unused<0)$unused=0;
    echo "<td>".$unused."/".$row['quantity']."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$amperage."A@".$row['voltage']."V</td>";
    echo "<td>".$row['manufacturer']."</td>";
    echo "<td>".$row['description']."</td><td>";
    echo (isset($row['datasheet']))? "<a target=\"_TAB\" href=\"".$row['datasheet']."\">Datasheet</a>" : "&nbsp;";
    echo "</td>";
    echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
    echo "</tr>\n";

  }
$result=mysql_fetch_array(mysql_query("select sum(quantity)-sum(used) as total, sum(quantity) as summation from electromech_motors where user='".$user."'"));
echo "</tbody><tfoot><tr><td>".$result['total']."/".$result['summation']."</td></tr></tfoot></table>";
}else echo "<p>No Motors in Database</p>";
?>
<h2>Add Motor</h2>
<script language="javascript">
function concatDesc(input){
  desc=document.getElementById('desc');
  desc.value=desc.value+input;
  //document.getElementById('addDesc').option[0].selected=true;
}
//http://www.irishwebmasterforum.com/coding-help/11930-adding-text-at-cursor-position-textarea.html
var globalCursorPos; // global variabe to keep track of where the cursor was

function setCursorPos() {
 globalCursorPos = getCursorPos(document.form.large_profile);
}

function getCursorPos(textElement) {

 var sOldText = textElement.value;

 var objRange = document.selection.createRange();
 var sOldRange = objRange.text;

 var sWeirdString = '#%~';

 objRange.text = sOldRange + sWeirdString; objRange.moveStart('character', (0 - sOldRange.length - sWeirdString.length));

 var sNewText = textElement.value;

 objRange.text = sOldRange;

 for (i=0; i <= sNewText.length; i++) {
   var sTemp = sNewText.substring(i, i + sWeirdString.length);
   if (sTemp == sWeirdString) {
     var cursorPos = (i - sOldRange.length);
     return cursorPos;
   }
 }
}

function insertString(stringToInsert) {
 var firstPart = document.form.large_profile.value.substring(0, globalCursorPos);
 var secondPart = document.form.large_profile.value.substring(globalCursorPos, document.form.large_profile.value.length);
 document.form.large_profile.value = firstPart + stringToInsert + secondPart;
}

</script>
<form name="addMotor" method="post" action=""><table>
<tr><td align="right">Quantity:</td><td><input tabindex=1 type="text" name="quantity" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">Used:</td><td><input tabindex=2 type="text" name="used" size=2 onkeypress="return onlyNumbers()" value="0" /></td></tr>
<tr><td align="right">Name:</td><td><input type="text" maxlength=50 tabindex=3 name="name"></td></tr>
<tr><td align="right">Type:</td><td><select tabindex=4 name="type"><option value="" disabled selected>Please Select One</option><option value="Direct" <?php if(@$cat[2]=="Direct") echo "selected"; ?>>Direct</option><option value="Gearhead" <?php if(@$cat[2]=="Gearhead")echo "selected"; ?>>Gearhead</option><option value="Servo" <?php if(@$cat[2]=="Servo") echo "selected"; ?>>Servos</option><option value="Bi-Stepper" <?php if(@$cat[2]=="Bi-Stepper") echo "selected"; ?>>Bipolar Stepper</option><option value="Uni-Stepper" <?php if(@$cat[2]=="Uni-Stepper") echo "selected"; ?>>Unipole Stepper</option><option value="Vibrating" <?php if(@$cat[2]=="Vibrating") echo "selected"; ?>>Vibrators</option></select></td></tr>
<tr><td align="right">Amperage:</td><td><input tabindex=5 type="text" name="amp" id="amp" size=2 onkeypress="return onlyNumbers()" onfocus="if(this.value=='null'){ this.value=''; document.getElementById('ampnull').checked=false; }" /> Ampers <input type="checkbox" tabindex=6 id="ampnull" onclick="if(this.checked==true) document.getElementById('amp').value='null'; else { document.getElementById('amp').value=''; document.getElementById('amp').focus() }" /><lable for="ampnull">N/A</label></td></tr>
<tr><td align="right">Voltage:</td><td><input tabindex=7 type="text" name="volt" size=2 onkeypress="return onlyNumbers()" /> Volts</td></tr>
<tr><td align="right">Description:</td><td><textarea tabindex=8 name="description" id="desc"></textarea> <select onchange="concatDesc(this.value); this.options[0].selected=true;"><option value="" disabled selected>Add Character</option><option value="&deg;">Degree</option><option value="&reg;">Registered</option><option value="&trade;">Trademark</option><option value="&copy;">Copyright</option></select></td></tr>
<tr><td align="right">Manufacturer:</td><td><input tabindex=9 type="text" maxlength=50 name="manu"/></td></tr>
<tr><td align="right">Datasheet:</td><td><input tabindex=10 type="text" name="data"/></td></tr>
<tr><td><input type="hidden" name="com" value="new" /></td><td><input tabindex=11 type="submit" value="Add Part" /></td></tr>
</table></form><?php


}
?>
</body></html>
