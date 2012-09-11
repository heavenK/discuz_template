<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
$pageat = isset($_GET['pageat']) ? $_GET['pageat'] : 1;
$pageby = isset($_GET['pageby']) ? $_GET['pageby'] : 10;



$start = ($pageat-1) * $pageby ;
$limit = $pageby;

$query = C::t('forum_thread')->fetch_all_by_authorid($uid, $start, $limit);

if(!$query) {
	$forumlist['err'] = 2;
	echo json_encode($forumlist);
	exit;
}
else $forumlist['err'] = 0;

$i = 0;
foreach($query as $p){
	$forumlist['thread'][$i]['tid'] = $p['tid'];
	$forumlist['thread'][$i]['subject'] = iconv('gbk','utf-8',$p['subject']);
	$forumlist['thread'][$i]['dateline'] = $p['dateline'];
	$forumlist['thread'][$i]['url'] = "http://bbs-test.we54.com/".getthreadcover($p['tid'], $p['cover']);

	$attachmentns = C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$p['tid'], 'tid', $p['tid'] ,'' ,false ,false ,false ,4);
	$j=0;
	foreach($attachmentns as $att){
		$attach[$j] = "http://bbs-test.we54.com/data/attachment/forum/".$att['attachment'];
		$j++;
	}
	$forumlist['thread'][$i]['url'] = $attach;

	
	
	$i++;
}



echo json_encode($forumlist);
?>