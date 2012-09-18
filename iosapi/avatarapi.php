<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();



isset($_REQUEST['uid']) ? $uid = $_REQUEST['uid'] : $res['changeCheck'] = 0;
$size = isset($_REQUEST['size']) ? $_REQUEST['size'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
isset($_REQUEST['pic']) ? $s = $_REQUEST['pic'] : $res['changeCheck'] = 0;


if($res || $res == NULL){
	
	$avatar = '../uc_server/data/avatar/'.get_avatar($uid, $size, $type);

	$basedir = DISCUZ_ROOT.'./uc_server/data/avatar/'.get_avatar_path($uid, $size, $type);

	dmkdir($basedir);

	//$s=base64_decode($s);

	file_put_contents($avatar, $s);

	$res['changeCheck'] = 1;
}

function get_avatar($uid, $size = 'middle', $type = '') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
}

function get_avatar_path($uid, $size = 'middle', $type = '') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	$typeadd = $type == 'real' ? '_real' : '';
	return $dir1.'/'.$dir2.'/'.$dir3.'/';
}

echo json_encode($res);
?>