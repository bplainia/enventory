<?php
$title="Passives";
include "../config.php";
include "../header.php";

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
?>
<h1>Resistors</h1>
<a href="passives.php">Back to passives home</a>
<?
  startsql();
  if(isset($cat[2])) $stmt = $db->query("select * from passives_res where (type='".mysql_real_escape_string($cat[2])."') and ((user='".$user."') or (user is null)) ORDER BY multi,color1,color2,wattage;");
  else $stmt = $db->query("select * from passives_res where (user='".$user."') or (user is null) ORDER BY multi,color1,color2,wattage;");
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if(count($result)>0){
      echo "<table border=1><tr><th>Value</th></tr>";
      foreach($result as $row)
      {
        $multi=color2Num($row['multi'],2);
        $resval=color2Num($row['color1'],0).color2Num($row['multi'],1).color2Num($row['color2'],0).$multi[0];
        $resvalraw=(color2Num($row['color1'],0).color2Num($row['color2'],0))*$multi[1];
        $type=getResType($row['type'],@$extras->smdsize); 
        echo "<tr>";
        echo "<td><a title=\"".$row['color1']."-".$row['color2']."-".$row['multi']."\"><font color=\"black\" decoration=\"none\">".$resval."&Omega;</font></a></td><td>$resvalraw</td>";
        echo "</tr>\n";

      }
    echo "</table>";
    }
    else 
    {
        echo "No Resistors in Database";
    }
//start of write  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /  /
?></body></html>