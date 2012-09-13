<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

$xmlDoc = new DOMDocument(); 
$xmlDoc->load('url.xml'); 

$type = isset($_GET['type']) ? $_GET['type'] : 'update';

if($type == 'update'){
	$ads = $xmlDoc->getElementsByTagName("UPDATE"); 
}else{
	$ads = $xmlDoc->getElementsByTagName("FEEDBACK"); 
}



foreach($ads as $ad){
	$appURL = $ad->getElementsByTagName("appURL");
	$res['appURL'] = $appURL->item(0)->nodeValue; 
}
echo json_encode($res);
?>