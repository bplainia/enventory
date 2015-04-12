<?
include "config.php";
include "header.php";
echo "<p>That page does not exist.</p>";
?>
<p>Please select a section from above or below.<br/>
<ul><li><a href="semiconductors.php">Semiconductors</a></li>
<li><a href="passives.php">Passives</a></li>
<li><a href="electromech.php">Electromechanical</a></li>
<li><a href="interconnects.php">Interconnects</a></li>
<li><a href="reports.php">Reports</a></li>
</ul>
<? if($_SERVER['PHP_AUTH_USER']=="benjamin") echo "Access: Full";
else echo "Access: Read-Only";

?>
</body></html>