<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

isset($_REQUEST['tid']) ? $tid = $_REQUEST['tid'] : $res['err'] = 1;

isset($_REQUEST['author']) ? $author = $_REQUEST['author'] : $res['err'] = 1;
isset($_REQUEST['uid']) ? $uid = $_REQUEST['uid'] : $res['err'] = 1;

$message = isset($_REQUEST['message']) ? $_REQUEST['message'] : '';

$message = iconv('utf-8','gbk',$message);
$author = iconv('utf-8','gbk',$author);


if($res['err'] != 1){
$pid = insertpost(array(
		'fid' => 1004,
		'tid' => $tid,
		'first' => '0',
		'author' => $author,
		'authorid' => $uid,
		'subject' => '',
		'dateline' => time(),
		'message' => $message,
		'useip' => $_G['clientip'],
		'invisible' => 0,
		'anonymous' => 0,
		'usesig' => 1,
		'htmlon' => 0,
		'bbcodeoff' => false,
		'smileyoff' => -1,
		'parseurloff' => false,
		'attachment' => '0',
		'tags' => '',
		'replycredit' => 0,
		'status' => 100
	));
if($pid) {
	updatethreadcount($tid,0);
	$res['tid'] = $tid;
	$res['pid'] = $pid;
	$res['err'] = 0;
}
}
function updatethreadcount($tid, $updateattach = 0) {
	$replycount = C::t('forum_post')->count_visiblepost_by_tid($tid) - 1;
	$lastpost = C::t('forum_post')->fetch_visiblepost_by_tid('tid:'.$tid, $tid, 0, 1);

	$lastpost['author'] = $lastpost['anonymous'] ? lang('forum/misc', 'anonymous') : addslashes($lastpost['author']);
	$lastpost['dateline'] = !empty($lastpost['dateline']) ? $lastpost['dateline'] : TIMESTAMP;

	$data = array('replies'=>$replycount, 'lastposter'=>$lastpost['author'], 'lastpost'=>$lastpost['dateline']);
	if($updateattach) {
		$attach = C::t('forum_post')->fetch_attachment_by_tid($tid);
		$data['attachment'] = $attach ? 1 : 0;
	}
	C::t('forum_thread')->update($tid, $data);
}
echo json_encode($res);
?>