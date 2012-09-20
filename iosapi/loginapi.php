<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$author = isset($_GET['author']) ? $_GET['author'] : exit;
$passwd = isset($_GET['password']) ? $_GET['password'] : exit;

$author=iconv('utf-8','gbk',$author);
loaducenter();
$ucresult = uc_user_login($author,$passwd);
list($tmp['uid']) = daddslashes($ucresult);

if($tmp['uid'] > 0) {
	$res['loginCheck'] = 2;
	$res['uid'] = $tmp['uid'];
}
elseif($tmp['uid'] == -2) $res['loginCheck'] = 1;
elseif($tmp['uid'] == -1) $res['loginCheck'] = 0;
else exit;



echo json_encode($res);


?>