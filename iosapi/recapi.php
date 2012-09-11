<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();


$old_token = isset($_GET['old_token']) ? $_GET['old_token'] : '';
$new_token = isset($_GET['new_token']) ? $_GET['new_token'] : $res['check'] = 1;

if($res) {
	echo json_encode($res);
	exit;
}

if($old_token) {
	
	$old_tokens = C::t('ios_token')->fetch_by_token($old_token);
	$new_tokens = C::t('ios_token')->fetch_by_token($new_token);
	
	if($new_tokens) {
		$res['check'] = 2;
		echo json_encode($res);
		exit;
	}
	
	if($old_tokens)	$rr = C::t('ios_token')->update_token($old_token,$new_token);
	else C::t('ios_token')->insert($new_token, '');
	
}else{
	$new_tokens = C::t('ios_token')->fetch_by_token($new_token);
	
	if($new_tokens) {
		$res['check'] = 2;
		echo json_encode($res);
		exit;
	}
	
	C::t('ios_token')->insert($new_token, '');

}
$res['check'] = 0;

echo json_encode($res);



?>