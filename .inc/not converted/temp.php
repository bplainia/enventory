<?
include "config.php";
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


startsql();

$multi=color2Num($row['multi'],2);
$resval=color2Num($row['color1'],0).color2Num($row['multi'],1).color2Num($row['color2'],0).$multi[0];
$resvalraw=(color2Num($row['color1'],0).color2Num($row['color2'],0))*$multi[1];
$sql="UPDATE `passives_res` SET `bind`=\"$bind\", resistance=$resistance WHERE;"; 
//mysql_query($sql) or die(mysql_error() . "<br/>" . $sql);

$sql = "UPDATE `passives_res` SET `bind`= \n"
    . "CASE\n"
    . "WHEN multi = \'silver\' THEN 1\n"
    . "WHEN multi=\'gold\' THEN 1\n"
    . "WHEN multi = \'black\' THEN 2\n"
    . "WHEN multi = \'brown\' THEN 3\n"
    . "WHEN multi = \'red\' THEN 4\n"
    . "WHEN multi = \'orange\' THEN 5\n"
    . "WHEN multi = \'yellow\' THEN 6\n"
    . "WHEN multi = \'green\' then 7\n"
    . "WHEN multi =\'blue\' then 7\n"
    . "WHEN multi = \'purple\' THEN 7\n"
    . "else 0\n"
    . "END";
?>