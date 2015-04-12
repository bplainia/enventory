<?php
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
function getResType($type,$size){ 
    switch($type){ 
        case "carbon": 
            return "Carbon Film"; 
        case "wire": return "Wire Wound"; 
        case "smd": 
          if($size==NULL) $size = "NULL!";
          return "SMD ($size)";
        default:
            return $type;
    }
}
if(isset($_POST['com'])){ // Parse command if it is set
    startsql(); // Initialize sql so we can use it
    $command=stripslashes($_POST['com']); // Fetch the command; do some anti inject stuff
    if($command=="new"){
        $stmt = $db->prepare("INSERT INTO passives_res (type,color1,color2,multi,tollerance,wattage,quantity,used,extra) VALUES (?,?,?,?,?,?,?,?,?);"); // generic insert statement for resistors
        $stmt->execute(array($_POST['type'],$_POST['col1'],$_POST['col2'],$_POST['multi'],$_POST['tol'],$_POST['watt'],$_POST['qty'],$_POST['used'],($_POST['type']=="smd" ? "{\"smdsize\":\"".$_POST['extra1size']."\"}" : "{}"))); // Will awlays execute
        if($_POST['sub']=="Add Resistors") $stmt->execute(array($_POST['type2'],$_POST['col12'],$_POST['col22'],$_POST['multi2'],$_POST['tol2'],$_POST['watt2'],$_POST['qty2'],$_POST['used2'], ($_POST['type2']=="smd" ? "{\"smdsize\" : \"".$_POST['extra2size']."\"}"  : "{}"))); 
    }
}
?>
<h1>Resistors</h1>
<a href="passives.php">Back to passives home</a>
<p><select onchange="goto('./passives.php/resistors'+this.value,false)"><option value="" <?if(!isset($cat[2])) echo "selected"; ?>>All<option value="/carbon" <?if(@$cat[2]=='carbon') echo "selected"; ?>>Carbon Film<option value="/wire" <?if(@$cat[2]=='wire') echo "selected"; ?>>Wire Wound<option value="/network" <?if(@$cat[2]=='nework') echo "selected"; ?>>Resistor Network<option value="smd" <?php if(@$cat[2]=="/smd") echo "selected";?>>SMD</select></p>
<?
  startsql();
  if(isset($cat[2])) $stmt = $db->query("select * from passives_res where (type='".mysql_real_escape_string($cat[2])."') and ((user='".$user."') or (user is null)) ORDER BY multi,color1,color2,wattage;");
  else $stmt = $db->query("select * from passives_res where (user='".$user."') or (user is null) ORDER BY multi,color1,color2,wattage;");
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if(count($result)>0){
      echo "<table border=1 class=\"tablesorter\" id=\"myTable\"><tr><!--th>Entry</th--><th>Qty</th><th>Value</th><th>Type</th><th>Toll.</th><th>Watts</th><th class=\"{sorter: false}\">AUR</th></tr>";
      foreach($result as $row)
      {
        $multi=color2Num($row['multi'],2);
        $resval=color2Num($row['color1'],0).color2Num($row['multi'],1).color2Num($row['color2'],0).$multi[0];
        $resvalraw=(color2Num($row['color1'],0).color2Num($row['color2'],0))*$multi[1];
        $extras = $row['extra'];
        $extras = json_decode($extras);	
        $type=getResType($row['type'],@$extras->smdsize); 
        echo "<tr>";
        $unused=$row['quantity']-$row['used'];
        if($unused<0)$unused=0;
        //echo "<td>".$row['ID']."</td>";
        echo "<td>".$unused."/".$row['quantity']."</td>";
        echo "<td customkey=\"".$resvalraw."\"><a title=\"".$row['color1']."-".$row['color2']."-".$row['multi']."\"><font color=\"black\" decoration=\"none\">".$resval."&Omega;</font></a></td>";
        echo "<td>".$type."</td>";
        echo "<td>&plusmn;".$row['tollerance']."%</td>";
        echo "<td>".$row['wattage']."W</td>";
        echo "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
        echo "</tr>\n";

      }
    $sql = "select sum(quantity) as qty, sum(used) as used from passives_res where user=:user;";
    $stmt=$db->prepare($sql);
    $stmt->execute(array(":user"=>$user));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "</table>";
    echo "<p>\"$sql\" ($user) Returned:".print_r($result,true)."</p>";
    echo "Total Resistors: ".($result['qty']-$result['used'])."/".$result['qty']."<br/><small>Quantities are the following format: 
available/all</small>";
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
<tr><td align="right">Type:</td><td><select tabindex=3 name="type" onchange="document.getElementById('smdize1').style.display = (this.value=='smd')?'':'none'"><option value="carbon" selected>Carbon Film</option><option value="wire">Wire-wound</option><option value="network">Resistor Network</option><option value="smd">SMD</option></select></td><td align="right">Type:</td><td><select tabindex=13 name="type2"><option value="carbon" selected>Carbon Film</option><option value="wire">Wire-wound</option><option value="network">Resistor Network</option><option value="smd">SMD</option></select></td><tr>
<tr id="smdize1" style="display:none;"><td align="right">Size of SMD:</td><td><input type="textbox" size="2" name="extra1size"></td><td align="right">Size of SMD:</td><td><input type="textbox" size="2" name="extra2size"></td></tr>
<tr><td align="right">Band 1:</td><td><select tabindex=4 name="col1"><option value="brown">1 Brown</option><option value="red">2 Red</option><option value="orange">3 Orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td><td align="right">Band 1:</td><td><select tabindex=14 name="col12"><option value="brown">1 Brown</option><option value="red">2 Red</option><option value="orange">3 Orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td></tr>
<tr><td align="right">Band 2:</td><td><select tabindex=5 name="col2"><option value="black">0 black</option><option value="brown">1 brown</option><option value="red">2 red</option><option value="orange">3 orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td><td align="right">Band 2:</td><td><select tabindex=15 name="col22"><option value="black">0 black</option><option value="brown">1 brown</option><option value="red">2 red</option><option value="orange">3 orange</option><option value="yellow">4 yellow</option><option value="green">5 green</option><option value="blue">6 blue</option><option value="purple">7 purple</option><option value="grey">8 grey</option><option value="white">9 white</option></select></td></tr>
<tr><td align="right">Multiplier:</td><td><select tabindex=6 name="multi"><option value="silver">none .12</option><option value="gold">gold 1.2</option><option value="black" selected="selected">black 12</option><option value="brown">brown 120</option><option value="red">red 1.2K</option><option value="orange">orange 12K</option><option value="yellow">yellow 120K</option><option value="green">green 1.2M</option><option value="blue">blue 12M</option><option value="purple">purple 120M</option></select></td><td align="right">Multiplier:</td><td><select tabindex=16 name="multi2"><option value="silver">none .12</option><option value="gold">gold 1.2</option><option value="black" selected="selected">black 12</option><option value="brown">brown 120</option><option value="red">red 1.2K</option><option value="orange">orange 12K</option><option value="yellow">yellow 120K</option><option value="green">green 1.2M</option><option value="blue">blue 12M</option><option value="purple">purple 120M</option></select></td></tr>
<tr><td align="right">Tollerance:</td><td><select tabindex=7 name="tol"><option value=20>20&plusmn;%<option value=10>10&plusmn;%<option value=5>5&plusmn;%<option value=2>2&plusmn;%<option value=1>1&plusmn;%<option value=0.5>.5&plusmn;%</select></td><td align="right">Tollerance:</td><td><select tabindex=17 name="tol2"><option value=20>20&plusmn;%<option value=10>10&plusmn;%<option value=5>5&plusmn;%</select></td></tr>
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