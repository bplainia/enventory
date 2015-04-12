<?php
include "config.php";
include "header.php";
echo "<p>Welcome, ".$user.".</p>";
?>
<p>Please select a section from above or below.<br/>
<ul><li><a href="semiconductors.php">Semiconductors</a></li>
<li><a href="passives.php">Passives</a></li>
<li><a href="electromech.php">Electromechanical</a></li>
<li><a href="interconnects.php">Interconnects</a></li>
<li><a href="kits.php">Kits, Boards</a></li>
<li><a href="locations.php">Locations</a></li>
<li><a href="reports.php">Reports</a></li>
</ul>
<?php 
/* 
if($user=="benjamin") echo "Access: Full";
else echo "Access: Read-Only";
*/
?>
</body></html>
