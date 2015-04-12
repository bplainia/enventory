<?php
include "config.php";
// put http variables into php variables
// does not need for mysql stripping since this is a private application (though it would be a good practice)
$where=$_REQUEST['url'];
$command=$_POST['com'];
$value=$_POST['val'];
$values=explode('!!',$values);
startsql();
if($command=="addSemiTrans"){
$sql="";
mysql_query($sql);
}elseif(false){

}
header("location:$where");
