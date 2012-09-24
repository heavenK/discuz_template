<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

$xmlDoc = new DOMDocument(); 
$xmlDoc->load('adv.xml'); 

$ads = $xmlDoc->getElementsByTagName("AD1"); 

foreach($ads as $ad){
	$adimageURL = $ad->getElementsByTagName("adverImageURL"); 
	$adverURL = $ad->getElementsByTagName("adverURL");
	$res['adimageURL'] = $adimageURL->item(0)->nodeValue; 
	$res['adverURL'] = $adverURL->item(0)->nodeValue; 
}
echo json_encode($res);
?>