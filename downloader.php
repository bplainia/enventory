<?php
require("config.php");
startsql();

echo "<!DOCTYPE html>\n<html>\n<body>\n";
echo "Processing IC's<br>\n";
$sql="SELECT ID,number,datasheet FROM semiconductors_IC WHERE datasheet like \"http:%\";";
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)){
    echo "Fetching datasheet for '".$row['number']."'. ";
    $return=downloadDatasheet($row['datasheet']);
    switch($return) {
        case 0: 
            echo "Success. Changing link. ";
            if(stripHTTP('semiconductors_IC',$row['ID'],$row['datasheet'])) echo "OK.";
            else die("Fail <br>\n".mysql_error());
            break;
        case 1: 
            echo "Failed: No pdf extention";
            break;
        case 2: 
            echo "Failed: Does not exist or not a PDF";
            break;
        case 3: 
            echo "Failed: Unable to make directory";
            break;
        case 4: 
            echo "Failed: Unable to make file";
            break;
    }
    echo "<br>\n";
}
echo "Finished!\n</body>\n</html>\n";


/* TODO: downloader:
if the link starts with http, then
 - check and if not there, create a directory structure similar to the url.
 - download the file to this place. therefore the you can either go to the url normally, or locally.
 - save the datasheet link, stripping the protocol
elseif the link starts with file:// or c:/ (windows) or just / (linux), then
 - copy the file to the datasheets directory under the correct folder (e.g. "ic")
 - set the datasheet link to that location
 
other thoughts: use a select right before the textbox. have web, local, and server.
*/
function downloadDatasheet($url,$location="web") {
    $eURL=preg_split("#[/|\\\\]#",$url);
    if($location="web"){
        // process url so it is easy to handle.
        $eURL=array_slice($eURL,1);
        if($eURL[0]=="") $eURL=array_slice($eURL,1); // should always run. the if statement is a precaution in case there is only one '/'
        $filename=explode('.',$eURL[count($eURL)-1]);
        //check that it goes to a file with the extention of pdf
        if(strtolower($filename[count($filename)-1]) != "pdf") return 1;
        //Now that it has been processed, check if it is there and that the server says it is a pdf
        $file_headers = @get_headers($url);
        if(in_array('HTTP/1.1 404 Not Found',$file_headers) or $file_headers=="" or !in_array('Content-Type: application/pdf',$file_headers)) return 2;
        // since it is there, create directory and place it in.
        $directory = $_SERVER['DOCUMENT_ROOT']."/datasheets/".implode('/',array_slice($eURL,0,-1));
        if(!is_dir($directory)) { //if the directory doesn't exist, make it
            if(!mkdir($directory,0775,true)) {  //if unable to make directory, error.
                return 3;
            }
        }
        // put the file where it needs to go. if it fails, error.
        if(!file_put_contents($directory."/".implode('.',array_slice($filename,0,-1)).".pdf",fopen($url,'r'))) return 4;
        
    }
    return 0;
}

function stripHTTP($table,$id,$url){
    // process url first
    $url=preg_replace('/http:\/\//','',$url);
    return mysql_query("UPDATE $table SET datasheet='$url' WHERE ID=$id;"); //returns true on success
}

?>