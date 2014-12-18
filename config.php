<?php
@session_start();
if((!isset($_SESSION['user']) && !isset($_POST['user'])) || (isset($_GET['switch'])))
{
    if(isset($_SESSION['user'])) unset($_SESSION['user']);
    include "header.php";
    echo "<div align=\"center\">\n<form method=\"POST\" action=\"/\">\nPlease enter a user: <input type=\"text\" name=\"user\" value=\"benjamin\">\n<input type=\"submit\" value=\"Go\"></form>\n</div>\n</body>\n</html>";
    die();
}
elseif(isset($_POST['user']))
{
    $user = stripslashes($_POST['user']);
    if(!is_numeric($user)) $_SESSION['user'] = $user;
}
else
{
    $user = $_SESSION['user'];
}
  $name="Benjamin";
function startsql(){
  //global $dblink;
  global $db;
  //$dblink = mysqli_connect("localhost", "enventory", "linuxisthebest","enventory")or die("cannot connect");
  $db = new PDO('mysql:host=localhost;dbname=enventory',"enventory","linuxisthebest");
  $db->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
  $db->query('SET CHARACTER SET utf8');
  //mysqli_select_db("enventory")or die("cannot select DB");
}
//$user=$_SERVER['PHP_AUTH_USER'];
//$user=(isset($_SERVER['PHP_AUTH_USER']))?$name:"Visitor";
//$users=array("benjamin","michaelm","daniel");
