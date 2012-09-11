<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$sql = 'SELECT authorid,author,count(author) FROM '.DB::table('forum_thread').' WHERE fid=743 GROUP BY authorid ORDER BY COUNT(author) DESC LIMIT 0,30';



$query = DB::query($sql);

if(!$query) $userlist['err'] = 1;
else	$userlist['err'] = 0;

while($result = DB::fetch($query)) {
	$content = C::t('common_member_field_forum')->fetch_all($result['authorid']);
	$dd = C::t('common_member_count')->fetch_all($result['authorid']);
	$users['uid'] = $result['authorid'];
	$users['author'] = iconv('gbk','utf-8',$result['author']);
	$users['bean'] = $dd[$result['authorid']]['extcredits5'];
	$users['description'] = iconv('gbk','utf-8',$content[$result['authorid']]['sightml']);
	$data[] = $users;
}
$userlist['paike'] = $data;


echo json_encode($userlist);
?>