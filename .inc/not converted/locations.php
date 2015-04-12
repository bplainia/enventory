<?php
$command = stripslashes(@$_GET['cmd']);
$data    = stripslashes(@$_GET['data']);
require "config.php";
startsql();

if(!isset($_GET['cmd']))
{
    $title="Locations";
    require "header.php";
    $sql="SELECT * FROM locList;";
    $result = $db->query($sql);
    echo "<h1>Locations</h1>\n";
    echo "<div><form id=\"box\" method=\"POST\"><select name=\"box\" onChange=\"document.getElementById('box').submit()\"><option value=\"0\" selected disabled>Select One</option>";
    foreach($result->fetchAll(PDO::FETCH_ASSOC) as $row)
    {
        echo "  <option value=\"".$row['name']."\">".$row['desc']."</option>";
    }
    echo "</select></form></div>";
    if(isset($_POST['box']) && is_numeric($_POST['box']))
    {
        
        $smt = $db->prepare("SELECT * FROM locList WHERE name=:box LIMIT 1;");
        $smt->bindParam(":box",$_POST['box']);
        $smt->execute();
        $result = $smt->fetch(PDO::FETCH_ASSOC);
        $style=json_decode($result['style']);
        $colspan = explode(';',$result['colspan']);
        foreach($colspan as $col) $colspans[] = explode(',',$col);
        $result = explode(',',$result['valid']);
        if($result[count($result)-1] < 100) $length=2;
        else $length = 4;
        $y=0;
        $curCol = 0;
        $curRow = 0;
        $colMerge = 1;
        //Begin Table
        echo "\n<div>\n\t<table border=\"1\" ";
        if(isset($style->bordercolor)) echo " color=\"".$style->bordercolor."\" ";
        echo ">\n\t\t<tr>";
        for($i=0;$i < count($result);$i++)
        {
            $location = str_split(str_pad($result[$i],$length),$length/2);
            if($location[0] != $y)
            {
                echo "\n\t\t</tr>\n\t\t<tr>";
                $y = $location[0];
            }
            echo "\n\t\t\t<td";
            if($colMerge > 1) echo " colspan=$colMerge";
            echo ">".str_pad($result[$i],$length)."</td>";
            if($result[$i] == $colspans[$curCol][0]) $colMerge = $colspans[$curCol][1];
        }
        echo "\n\t\t</tr>\n\t</table>\n</div>";
    }
    exit("\n</body>\n</html>");
}
switch($command)
{
    case "select":
        if(!is_numeric($data)) exit("Bad Request"); // Validation
        $sql="SELECT valid FROM locList WHERE name=$data;";
        $result = $db->query($sql);
        $result = $result->fetch(PDO::FETCH_ASSOC);
        $result = explode(',',$result['valid']);
        $longest = $result[count($result)-1];
        if($longest < 100)
            foreach($result as $location)
            { 
                echo "<option value=\"$location\">".str_pad($location,2)."</option>\n  ";
            }
        else
            foreach($result as $location)
            { 
                echo "<option value=\"$location\">".str_pad($location,4)."</option>\n  ";
            }
}
