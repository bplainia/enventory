 <?php
 $url="http://www.datasheetcatalog.org/datasheets/105/108953_DS.pdf";
$url="http://pdf.datasheetcatalog.com/datasheet2/8/0uegdz0rfihfjplhkh6u551zz67y.pdf"; 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true); 
curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // Videos are needed to transfered in binary
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // That's the clue! 
$result = curl_exec($ch); // $result will have your video.
curl_close($ch);
 ?>