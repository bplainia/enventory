<?php
startsql();
if(isset($_POST['com'])){
    die("<p><b>not implemented</b></p></body></html>");
}
$quantity=0;
?><h1>Light Emitting Diodes</h1>
<p><a href="/opto.php">Back to Opto's Home</a></p>
<table border=1 class="sortable"><thead><tr><th>qty</th><th>Color</th><th>V<sub>f</sub> Typ.</th><th>Brightness<br>@20mA</th><th>Manufacturer</th><th>Man. #</th><th>Datasheet</th><th>AUR</th></tr></thead>
<tbody>
<?php 
    $sql="select * from opto_LED;";
    $result = mysql_query($sql)or die(mysql_error());
    while($row=mysql_fetch_array($result)){
        echo "<tr><td>";
        echo $row['quantity'];
        $quantity+=$row['quantity'];
        echo "</td><td>";
        echo $row['Color'];
        echo "</td><td>";
        echo $row['FVoltage'];
        echo "V</td><td>";
        echo $row['mcd'];
        echo "mcd</td><td>";
        echo $row['Manufacturer'];
        echo "</td><td>";
        echo $row['ManuNum'];
        echo "</td><td>";
        if($row['Datasheet']!="") echo "<a href=\"/datasheets/".$row['Datasheet']."\" target=\"datasheetOpen\">Datasheet</a>";
        echo "</td><td>";
        #Add/Update/Remove Form
        echo "<form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form>";
        echo "</td>";
    }
?>
</tbody><tfoot><tr><td><?php echo $quantity ?></td></tr></tfoot></table>
<h1>Add LED</h1>
<p>not implemented</p>