<?
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
<? }
elseif($cat[1]=="capacitors"){ /////////////////////////cap////////////////////////cap////////////////////////////////cap/////// 


function calcValues($value,$code) {
    if($code) {
        if(strlen($value)==3 or strlen($value)==2){
            if (strlen($value)==2){
                $power = -12;
            }
            else {
                $power  = (int)substr($value,2,1)-12;
            }
            $raw = substr($value,0,2);
            if(!isset($_GET['micronly'])){
                if($power < -10) $output=$raw*pow(10,$power+12)."pF";
                elseif($power < -7) $output=$raw*pow(10,$power+9)."nF";
                else $output = $raw*pow(10,$power+6)."&mu;F";
            }
            else $output = number_format($raw*pow(10,$power+6),6)."&mu;F";
            return array($output,$raw*pow(10,$power));
        }else return array("error",null);
        //SetText("tolerance", tolval[obj.tolerancecode.selectedIndex]);
    }
    else { 
        $rawValue=$value*pow(10,-6);
        $value=(float)$value;
        if($value < 0.001){
            $value *= 1000000;
            $unit = "pF";
        }
        elseif($value < 1.0){
            $value = $value * 1000;
            $unit = "nF";
        }
        else {
            $unit = "&mu;F";
        }
        return array($value.$unit,$rawValue);
    }
}

startsql();
if(isset($_POST['com'])){ //check for Add/Update/Remove
  if($_POST['com']=="add")
  {
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-') $addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update passives_caps set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
    $db->query($sql);
  }
  elseif($_POST['com']=="use")
  {
    $action=substr($_POST['val'],0,1);
    if($action=='+' || $action=='-')$addval = $action." ".substr($_POST['val'],1);
    else $addval = "+ $action";
    $sql="update passives_caps set used = used ".$addval." where ID=".$_POST['id'].";";
    $db->query($sql);
  }
  elseif($_POST['com']=="rm")
  {
    $sql="delete from passives_caps where ID=".$_POST['id'].";";
    $db->query($sql);
  }
  elseif($_POST['com']=="new")
  {
    if($_POST['valtype']==0) $code="true"; else $code="false";
    $stment = $db->prepare("insert into passives_caps (quantity,Used,value,voltage,type,Color,comment,code,Manufacturer,user) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $stment->execute(array($_POST['quantity'],$_POST['used'],$_POST['value'],$_POST['volt'],$_POST['type'],$_POST['color'],$_POST['comment'],$code,$_POST['manu'],$user));
  }
}//endif AUR
?>
<h1>Capacitors</h1>
<a href="passives.php">Back to passives home</a>
<p><select onchange="goto('passives.php/capacitors'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/radial" <?if(@$cat[2]=='radial') echo "selected"; ?>>radial<option value="/axial" <?if(@$cat[2]=='axial') echo "selected"; ?>>axial<option value="/ceramic" <?if(@$cat[2]=='ceramic') echo "selected"; ?>>ceramic</select></p>
<table border=1 class="sortable"><thead><tr><th>Qty</th><th>Value</th><th>Rating</th><th>Type</th><th>Color</th><th>Manufacturer</th><th>Comment</th><th class="sorttable_nosort">AUR</th></tr></thead><tbody>
<?
  if(isset($cat[2])) $sql = "select * from passives_caps where type='".$cat[2]."' and ((user='".$user."') or (user is null))  order by value;";
  else $sql="select * from passives_caps where (user='".$user."') or (user is null) order by value;";
  $result = $db->query($sql);
  $quantity=0;
  foreach($result->fetchAll(PDO::FETCH_ASSOC) as $row)
  {
    list($value,$valraw)=calcValues($row['value'],$row['code']);
    echo "<tr>";
    echo "<td>".$row['quantity']."</td>";
    $quantity += $row['quantity'];
    echo "<td sorttable_customkey=\"".number_format($valraw,13)."\">".$value."</td>";
    if($row['voltage']!=0) echo "<td>".$row['voltage']."V</td>";
    else echo "<td>-</td>";
    echo "<td>".$row['type']."</td>";
    echo "<td>".$row['Color']."</td>";
    echo "<td>".$row['Manufacturer']."</td>";
    echo "<td>".$row['comment'];
    if($row['code']) echo " (".$row['value'].")";
    echo "</td>";
    echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
    echo "</tr>\n";
    $valraw=null;
  }
echo "</tbody><tfoot><tr><td>$quantity</td></tr></tfooot></table>"; ?>
<h2><a name="add">&nbsp;</a>Add Capacitor</h2>
<script language="javascript">
//<!--
function checkType(type){
return false;
}
//-->
</script>
<form name="addCap" method="post" action="/passives.php/capacitors#add"><table>
<tr><td align="right">Quantity:</td><td><input tabindex=1 type="text" name="quantity" size=2 onkeypress="return onlyNumbers()" /></td></tr>
<tr><td align="right">Used:</td><td><input tabindex=2 type="text" name="used" size=2 onkeypress="return onlyNumbers()" value="0" /></td></tr>
<tr><td align="right">Type:</td><td><select tabindex=6 name="type"><option></option><option value="radial">Radial</option><option value="axial">Axial</option><option value="ceramic">Ceramic</option><option value="poly">Polymer</option><option value="mylar">Mylar</option></select></td></tr>
<tr><td align="right">Value:</td><td><input tabindex=4 size=3 type="text" name="value" /><select name="valtype" tabindex=5><option value=1>&mu;F</option><option value=0>code</option></select></td></tr>
<tr><td align="right">Voltage:</td><td><input tabindex=6 type="text" name="volt" size=2 onkeypress="return onlyNumbers()" /> Volts</td></tr>
<tr><td align="right">Color:</td><td><input type="text" maxlength=15 tabindex=7 name="color"></select></td></tr>
<tr><td align="right">Comment:</td><td><textarea tabindex=8 name="comment"></textarea></td></tr>
<tr><td align="right">Manufacturer:</td><td><input tabindex=9 type="text" maxlength=15 name="manu"/></td></tr>
<tr><td><input type="hidden" name="com" value="new" /></td><td><input tabindex=10 type="submit" value="Add Capacitor" /></td></tr>
</table></form>
<? }
//////////////////////////////////////////////res/////////////////////////////////////////res///////////////////////////////
elseif($cat[1]=="resistors"){ 
function color2Num($color,$multi){ //color is color, returns string
    switch($color){
      case "silver":
        if($multi==1) return;
        if($multi==2) return array(" m" ,.01);
        return;
      case "gold":
        if($multi==1) return ".";
        if($multi==2) return array(null,.1);
        return;
      case "black":
        if($multi==1) return;
        if($multi==2) return array(null,1);
        return '0';
      case "brown":
        if($multi==1) return;
        if($multi==2) return array("0",10);
        return '1';
      case "red":
       if($multi==1) return ".";
       if($multi==2) return array(" K",100);
       return "2";
      case "orange":
        if($multi==1) return;
        if($multi==2) return array(" K",1000);
        return "3";
      case "yellow":
        if($multi==1) return;
        if($multi==2) return array("0 K",10000);
        return "4";
      case "green":
        if($multi==1) return ".";
        if($multi==2) return array(" M",100000);
        return "5";
      case "blue":
        if($multi==1) return;
        if($multi==2) return array(" M",1000000);
        return "6";
      case "purple":
        if($multi==1) return;
        if($multi==2) return array("0 M",10000000);
        return "7";
      case "violet":
        if($multi==1) return;
        if($multi==2) return array("0 M",100000000);
        return "7";
      case "grey":
        if($multi==1) return;
        if($multi==2) return array("00 M",1000000000);
        return "8";
      case "white":
        if($multi==1) return;
        if($multi==2) return array("Not a valid multiplier",10000000000);
        return "9";
  }
} //end color2Num
function getResType($type){ 
    switch($type){ 
        case "carbon": 
            return "Carbon Film"; 
        case "wire": return "Wire Wound"; 
        default:
            return $type;
    }
}
if(isset($_POST['com'])){ // Parse command if it is set
    startsql(); // Initialize sql so we can use it
    $command=stripslashes($_POST['com']); // Fetch the command; do some anti inject stuff
    if($command=="new"){
        $stmt = $db->prepare("INSERT INTO passives_res (type,color1,color2,multi,tollerance,wattage,quantity,used) VALUES (?,?,?,?,?,?,?,?);"); // generic insert statement for resistors
        $stmt->execute(array($_POST['type'],$_POST['col1'],$_POST['col2'],$_POST['multi'],$_POST['tol'],$_POST['watt'],$_POST['qty'],$_POST['used'])); // Will awlays execute
        if($_POST['sub']=="Add Resistors") $stmt->execute(array($_POST['type2'],$_POST['col12'],$_POST['col22'],$_POST['multi2'],$_POST['tol2'],$_POST['watt2'],$_POST['qty2'],$_POST['used2'])); 
    }
}
?>
<h1>Resistors</h1>
<a href="passives.php">Back to passives home</a>
<p><select onchange="goto('./resistors'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/carbon" <?if($cat[2]=='carbon') echo "selected"; ?>>Carbon Film<option value="/wire" <?if($cat[2]=='wire') echo "selected"; ?>>Wire Wound<option value="/network" <?if($cat[2]=='nework') echo "selected"; ?>>Resistor Network</select></p>
<?
  startsql();
  if(isset($cat[2])) $stmt = $db->query("select * from passives_res where (type='".mysql_real_escape_string($cat[2])."') and ((user='".$user."') or (user is null));");
  else $stmt = $db->query("select * from passives_res where (user='".$user."') or (user is null);");
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if(count($result)>0){
      echo "<table border=1 class=\"sortable\"><tr><!--th>Entry</th--><th>Qty</th><th>Value</th><th>Type</th><th>Toll.</th><th>Watts</th><th class=\"sorttable_nosort\">AUR</th></tr>";
      foreach($result as $row)
      {
        $multi=color2Num($row['multi'],2);
        $resval=color2Num($row['color1'],0).color2Num($row['multi'],1).color2Num($row['color2'],0).$multi[0];
        $resvalraw=(color2Num($row['color1'],0).color2Num($row['color2'],0))*$multi[1];
        $type=getResType($row['type']); 
        echo "<tr>";
        $unused=$row['quantity']-$row['used'];
        if($unused<0)$unused=0;
        //echo "<td>".$row['ID']."</td>";
        echo "<td>".$unused."/".$row['quantity']."</td>";
        echo "<td sorttable_customkey=\"".$resvalraw."\"><a title=\"".$row['color1']."-".$row['color2']."-".$row['multi']."\"><font color=\"black\" decoration=\"none\">".$resval."&Omega;</font></a></td>";
        echo "<td>".$type."</td>";
        echo "<td>&plusmn;".$row['tollerance']."%</td>";
        echo "<td>".$row['wattage']."W</td>";
        echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
        echo "</tr>\n";

      }
    $result=mysql_fetch_array(mysqli_query($dblink,"select sum(quantity)-sum(used) as total, sum(quantity) as summation from passives_res where user='".$user."'"));
    echo "</table>";
    echo "Total Resistors: ".$result['total']."/".$result['summation']."<br/><small>Quantities are the following format: available/all</small>";
    }
    else 
    {
        echo "No Resistors in Database";
    }
//start of write  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /
?>
<h2><a name="add">Add</a> a resistor</h2>
<form method="post" action="passives.php/resistors#add" name="addres"><table border=0>
<!--tr><td></td><td></td><td column=3><input type="hidden" name="ignore" value="true" tabindex=10 /><input type="checkbox" value="false" name="ignore" id="ignorer" checked="true" />Ignore</td><td></td><tr-->
<tr><td align="right">Quantity:</td><td><input tabindex=1 type="text" size=2 name="qty" /></td><td align="right">Quantity:</td><td><input type="text" size=2 name="qty2" onclick="document.getElementById('ignorer').checked='false';" tabindex=11 /></td></tr>
<tr><td align="right">Used:</td><td><input tabindex=2 type="text" size=2 value="0" name="used" /></td><td align="right">Used:</td><td><input tabindex=12 type="text" size=2 value="0" name="used2" /></td></tr>
<tr><td align="right">Type:</td><td><select tabindex=3 name="type"><option value="carbon" selected>Carbon Film</option><option value="wire">Wire-wound</option><option value="network">Resistor Network</option></select></td><td align="right">Type:</td><td><select tabindex=13 name="type2"><option value="carbon" selected>Carbon Film</option><option value="wire">Wire-wound</option><option value="network">Resistor Network</option></select></td><tr>
<tr><td align="right">Band 1:</td><td><select tabindex=4 name="col1"><option value="brown">1 Brown</option><option value="red">2 Red</option><option value="orange">3 Orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td><td align="right">Band 1:</td><td><select tabindex=14 name="col12"><option value="brown">1 Brown</option><option value="red">2 Red</option><option value="orange">3 Orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td></tr>
<tr><td align="right">Band 2:</td><td><select tabindex=5 name="col2"><option value="black">0 black</option><option value="brown">1 brown</option><option value="red">2 red</option><option value="orange">3 orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td><td align="right">Band 2:</td><td><select tabindex=15 name="col22"><option value="black">0 black</option><option value="brown">1 brown</option><option value="red">2 red</option><option value="orange">3 orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td></tr>
<tr><td align="right">Multiplier:</td><td><select tabindex=6 name="multi"><option value="silver">none .12</option><option value="gold">gold 1.2</option><option value="black" selected="selected">black 12</option><option value="brown">brown 120</option><option value="red">red 1.2K</option><option value="orange">orange 12K</option><option value="yellow">yellow 120K</option><option value="green">green 1.2M</option><option value="blue">blue 12M</option><option value="purple">purple 120M</option></select></td><td align="right">Multiplier:</td><td><select tabindex=16 name="multi2"><option value="silver">none .12</option><option value="gold">gold 1.2</option><option value="black" selected="selected">black 12</option><option value="brown">brown 120</option><option value="red">red 1.2K</option><option value="orange">orange 12K</option><option value="yellow">yellow 120K</option><option value="green">green 1.2M</option><option value="blue">blue 12M</option><option value="purple">purple 120M</option></select></td></tr>
<tr><td align="right">Tollerance:</td><td><select tabindex=7 name="tol"><option value=20>20&plusmn;%<option value=10>10&plusmn;%<option value=5>5&plusmn;%</select></td><td align="right">Tollerance:</td><td><select tabindex=17 name="tol2"><option value=20>20&plusmn;%<option value=10>10&plusmn;%<option value=5>5&plusmn;%</select></td></tr>
<tr><td align="right">Wattage:</td><td><input tabindex=8 type="text" size=2 name="watt" value="0.5" /><small>(In Decimal)</small></td><td align="right">Wattage:</td><td><input tabindex=18 type="text" size=2 name="watt2" value="0.5" /><small>(In Decimal)</small></td></tr>
<input type="hidden" name="com" value="new" />
<tr><td></td><td><input tabindex=9 type="submit" title="Add this resistor ONLY" name="sub" value="Add Resistor" /></td><td></td><td><input tabindex=19 type="submit" name="sub" title="Add BOTH resistors" value="Add Resistors" /><input type="checkbox" name="gomain" value="true" tabindex=20/>This is last resistor (go to passives)</td></tr>
</table>
<h2>Colors</h2>
<table>
<tr>
<th width="80">Color</th>
<th>Significant<br />
figures</th>
<th>Multiplier</th>
<th colspan="2" width="100">Tolerance</th>
<th colspan="2" width="100">Temp. Coefficient (ppm/K)</th>
</tr>
<tr style="background:black; color:white">
<td><font color="white">Black</font></td>
<td>0</td>
<td>x10<sup>0</sup></td>
<td colspan="2">&mdash;</td>
<td>250</td>
<td>U</td>
</tr>
<tr style="background:#964B00; color:white">
<td><font color="white">Brown</font></td>
<td>1</td>
<td>x10<sup>1</sup></td>
<td>&plusmn;1%</td>
<td>F</td>
<td>100</td>
<td>S</td>
</tr>
<tr style="background:#FF0000; color:white">
<td><font color="white">Red</font></td>
<td>2</td>
<td>x10<sup>2</sup></td>
<td>&plusmn;2%</td>
<td>G</td>
<td>50</td>
<td>R</td>
</tr>
<tr bgcolor="#FFA500">
<td>Orange</td>
<td>3</td>
<td>x10<sup>3</sup></td>
<td colspan="2">&mdash;</td>
<td>15</td>
<td>P</td>
</tr>
<tr bgcolor="#FFFF00">
<td>Yellow</td>
<td>4</td>
<td>x10<sup>4</sup></td>
<td>&plusmn;5%</td>
<td>&mdash;</td>
<td>25</td>
<td>Q</td>
</tr>
<tr bgcolor="#9ACD32">
<td>Green</td>
<td>5</td>
<td>x10<sup>5</sup></td>
<td>&plusmn;0.5%</td>
<td>D</td>
<td>20</td>
<td>Z</td>
</tr>
<tr bgcolor="#6495ED">
<td>Blue</td>
<td>6</td>
<td>x10<sup>6</sup></td>
<td>&plusmn;0.25%</td>
<td>C</td>
<td>10</td>
<td>Z</td>
</tr>
<tr bgcolor="#EE82EE">
<td>Violet</td>
<td>7</td>
<td>x10<sup>7</sup></td>
<td>&plusmn;0.1%</td>
<td>B</td>
<td>5</td>
<td>M</td>
</tr>
<tr bgcolor="#A0A0A0">
<td>Gray</td>
<td>8</td>
<td>x10<sup>8</sup></td>
<td>&plusmn;0.05% (&plusmn;10%)</td>
<td>A</td>
<td>1</td>
<td>K</td>
</tr>
<tr bgcolor="#FFFFFF">
<td>White</td>
<td>9</td>
<td>x10<sup>9</sup></td>
<td colspan="2">&mdash;</td>
<td colspan="2">&mdash;</td>
</tr>
<tr bgcolor="#CFB53B">
<td>Gold</td>
<td>&mdash;</td>
<td>x10<sup>-1</sup></td>
<td>&plusmn;5%</td>
<td>J</td>
<td colspan="2">&mdash;</td>
</tr>
<tr bgcolor="#C0C0C0">
<td>Silver</td>
<td>&mdash;</td>
<td>x10<sup>-2</sup></td>
<td>&plusmn;10%</td>
<td>K</td>
<td colspan="2">&mdash;</td>
</tr>
<tr>
<td>None</td>
<td>&mdash;</td>
<td>&mdash;</td>
<td>&plusmn;20%</td>
<td>M</td>
<td colspan="2">&mdash;</td>
</tr></table>
<?
//end of write and resistors
}
elseif($cat[1]=="inductors"){ //////////////////////////inductors//////////////////////////////////inductors//////////////////////////////////
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
<table border=1 class="sortable"><tr><th>Qty</th><th>Value</th><th>Type</th><th>Dimentions</th><th>Manufacturer</th><th>Comment</th><th class="sorttable_nosort">AUR</th></tr>
<?
  if(isset($cat[2])) $sql = "select * from passives_ind where type='".$cat[2]."' and ((user='".$user."') or (user is null))  order by value;";
  else $sql="select * from passives_ind where (user='".$user."') or (user is null) order by value;";
  $result = $db->query($sql);
  foreach($result->fetchAll(PDO::FETCH_ASSOC) as $row){
    if($row['code']) list($value,$valraw)=codetovalue($row['value']);
    else { $value=$row['value']."&mu;F"; $valraw=$row['value']*pow(10,-6);}
    echo "<tr>";
    echo "<td>".$row['quantity']."</td>";
    echo "<td sorttable_customkey=\"".number_format($valraw,13)."\">".$value."</td>";
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
<? 
}
else{ //fallback if is not one of the above values ?>
<h1>Pasives</h1>
Select a Category:<ul>
<li><a href="passives.php/capacitors">Capacitors</a></li>
<li><a href="passives.php/resistors">Resistors</a></li>
<li><a href="passives.php/inductors">Inductors</a></li>
</ul>
<? }
//print_r($cat); //for debug purposes
?></body></html><!--×-->
