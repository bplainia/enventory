<?php
startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add"){
    $action=substr($_POST['val'],0,1);
    if ($action == '+' || $action == '-')
        {
            $addval = $action . " " . substr($_POST['val'], 1);
        } else
        {
            $addval = "+ $action";
        }
        $sql="update semiconductors_IC set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    $db->query($sql);
  }elseif($_POST['com']=="use"){
    $action=substr($_POST['val'],0,1);
    if ($action == '+' || $action == '-')
        {
            $addval = $action . " " . substr($_POST['val'], 1);
        } else
        {
            $addval = "+ $action";
        }
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
if (!isset($cat[2]))
{
    $cat[2] = null;
}
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
        if (isset($row['datasheet']))
        {
            echo "<a target=\"_TAB\" href=\"";
            if (preg_match("#^http#", $row['datasheet']))
            {
                echo $row['datasheet'];
            } elseif (preg_match("#^/#", $row['datasheet']))
            {
                echo "/datasheets" . $row['datasheet'];
            } else
            {
                echo "/datasheets/" . $row['datasheet'];
            }
            echo "\">Datasheet</a>";
        }
        else
        {
            echo "&nbsp;";
        }
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
	<tr><td class="labels">Quantity:</td>
	<td><input tabindex=1 type="text" name="qty" size=2 onkeypress="return onlyNumbers()" /></td></tr>
	<tr><td class="labels">ID#:</td><td><input tabindex=2 type="text" name="num" /></td></tr>
	<tr><td class="labels">Type:</td>
	<td><select tabindex=3 name="type"><option value="" selected disabled>Select One</option><option value="linear">Linear/Analog</option><option value="mcu">MCU</option><option value="mpu">Microprocessor</option><option value="mem">Memory</option><option value="logic">Logic Chip</option><option value="reg">Regulator</option><option value="interface">Interface</option></select></td></tr>
	<tr><td class="labels">Package:</td><td><!--select id="pkg"><option value="TO-3">TO-3<option vlaue="DIP">DIP<option value="SIP">SIP</select-->
<table border=1><tr>
	<td class="border"><input  tabindex=4 type="radio" name="pkg" value="DIP" id="dipradio" /><img src="images/555.jpg" height=50  onclick="document.getElementById('dipradio').checked=true;" /></td>
	<td class="border"><input type="radio" name="pkg" value="TO-220" id="to220radio" /><img src="images/TO-220" height="50" onclick="document.getElementById('to220radio').checked=true;" /></td>
	<td class="border"><input type="radio" name="pkg" value="SIP" id="sipradio" /><img src="images/SIP.jpg" height=50 onclick="document.getElementById('sipradio').checked=true;" /></td>
	<td class="border"><input type="radio" name="pkg" value="SOT-23" id="SOT23radio" /><img src="images/SOT-23-8.jpg" height="50" onclick="document.getElementById('SOT23radio').checked=true;" /></td>
</tr></table></td></tr>
	<tr><td class="labels">Pins:</td><td><input tabindex=5 type="text" name="pins" onkeypress="return onlyNumbers()" size=2 /></td></tr>
	<tr><td class="labels">Description:</td><td><textarea  tabindex=6 name="descript" onkeyup="document.getElementById('char').innerHTML='You have used '+this.value.length+' Characters.(60 recomended max)';"></textarea><div id="char">You have used 0 Characters.(60 recommended max)</div></td></tr>
	<tr><td class="labels">Location:</td><td>
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
<tr><td class="labels">Datasheet URL:</td><td><input  tabindex=8 type="text" name="dataURL"</td></tr>
<tr><td></td><td><input type="submit"  tabindex=9 value="Add Chip"></td></tr>
<input type="hidden" name="com" value="new" />
</table></form></p>