<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

$xmlDoc = new DOMDocument(); 
$xmlDoc->load('ad.xml'); 

$ads = $xmlDoc->getElementsByTagName("ad"); 

$i=0;
foreach($ads as $ad){
	$imgURL = $ad->getElementsByTagName("imgURL"); 
	$type = $ad->getElementsByTagName("type");
	$content = $ad->getElementsByTagName("content");
	
	$pic[$i]['imgURL'] = $imgURL->item(0)->nodeValue; 
	$pic[$i]['type'] = $type->item(0)->nodeValue; 
	$pic[$i]['content'] = $content->item(0)->nodeValue; 

	$i++;
}


$res['pics'] = $pic;

echo json_encode($res);
?>