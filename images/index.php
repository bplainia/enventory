<? $title="Uploaded Images";
include "../config.php";
include "../header.php";
if ($handle = opendir('.')) {
while (false !== ($file = readdir($handle))) {
if ($file != "." && $file != "..") {
echo "$file<br/>\n";
}
}
closedir($handle);
}


?>

</body></html>