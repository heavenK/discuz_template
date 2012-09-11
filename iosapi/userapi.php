<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$userID = isset($_GET['userID']) ? $_GET['userID'] : exit;

$content = C::t('common_member_field_forum')->fetch_all($userID);
$dd = C::t('common_member_count')->fetch_all($userID);
$pp = C::t('common_member_profile')->fetch_all($userID);
$name = C::t('common_member')->fetch_all_username_by_uid($userID);



$users['userName'] = iconv('gbk','utf-8',$name[$userID]);
$users['faceURL'] = 'http://bbs-test.we54.com/uc_server/avatar.php?uid='.$userID.'&size=middle';

if($pp[$userID]['gender'] == 1 ) $users['sex'] = '男';
elseif($pp[$userID]['gender'] == 2 ) $users['sex'] = '女';
else $users['sex'] = '未知';

//$users['sex'] = iconv('gbk','utf-8',$users['sex']);

$users['birthday'] = strtotime($pp[$userID]['birthyear'].'-'.$pp[$userID]['birthmonth'].'-'.$pp[$userID]['birthday']);
$users['bean'] = $dd[$userID]['extcredits5'];
$users['describe'] = iconv('gbk','utf-8',$content[$userID]['sightml']);


echo json_encode($users);
?>