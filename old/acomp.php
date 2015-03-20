<?
$page=@$_GET['page'];
if($page=="semiconductors")$title="Semiconductors";
elseif($page=="passives")$title="Passives";
else header("location:/");
include "config.php";
include "header.php";
echo "<h1>Components: $title</h1>";
if($page=="passives"){ ?>
<p>

<? } ?>
</body></html>
