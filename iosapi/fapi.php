<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$section = isset($_GET['section']) ? $_GET['section'] : 3;
$pageat = isset($_GET['pageat']) ? $_GET['pageat'] : 1;
$pageby = isset($_GET['pageby']) ? $_GET['pageby'] : 10;
$type = isset($_GET['type']) ? $_GET['type'] : 0;

if($section == 0) $typeid = 43;
elseif($section == 1) $typeid = 42;
elseif($section == 2) $typeid = 44;
else {
	$typeid = null;
	$section = 3;
}



$start = ($pageat-1) * $pageby ;
$limit = $pageby;

$query = C::t('forum_thread')->fetch_all_by_fid_typeid_digest_displayorder(1004 , $typeid, null, '=', $start, $limit, $type);

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
	$forumlist['thread'][$i]['author'] = iconv('gbk','utf-8',$p['author']);
	
	if($p['typeid'] == 43) $section = 0;
	elseif($p['typeid'] == 42) $section = 1;
	elseif($p['typeid'] == 44) $section = 2;
	else {
		$section = 3;
	}
	
	$forumlist['thread'][$i]['section'] = $section;
	$forumlist['thread'][$i]['replies'] = $p['replies'];
	$forumlist['thread'][$i]['url'] = "http://bbs-test.we54.com/".getthreadcover($p['tid'], $p['cover']);
	$i++;
}

echo json_encode($forumlist);
?>