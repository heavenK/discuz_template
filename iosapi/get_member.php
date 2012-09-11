<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$sql = "SELECT cmp.mobile FROM `pre_common_member` cm LEFT JOIN `pre_common_member_count` cmc ON cm.uid=cmc.uid LEFT JOIN `pre_common_member_profile` cmp ON cm.uid=cmp.uid WHERE cmc.oltime>5 AND cm.groupid=22 AND mobile<>''";



$query = DB::query($sql);




while($result = DB::fetch($query)) {
var_dump($result);
exit;
}


?>