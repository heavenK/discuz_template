<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$vt = C::t('forum_thread')->fetch_all_by_tid($_GET['tid']);

if(!$vt) {
	$viewthread['err'] = 1;
	exit;
}
else	$viewthread['err'] = 0;

$pageat = isset($_GET['pageat']) ? $_GET['pageat'] : 1;
$pageby = isset($_GET['pageby']) ? $_GET['pageby'] : 10;

$start = ($pageat-1) * $pageby + 1;
$end = $start + $pageby;

C::t('forum_thread')->increase($_GET['tid'], array('views' => 1), true);

$post = C::t('forum_post')->fetch_all_by_tid_range_position(0, $_GET['tid'], $start, $end, 10, 0);

foreach($vt as $v){
	$viewthread['replies'] = $v['replies'];
	$viewthread['views'] = $v['views'];
	$viewthread['subject'] = iconv('gbk','utf-8',$v['subject']);
	
}

if(!$vt) $viewthread['err'] = 1;
else	$viewthread['err'] = 0;

$n=0;

foreach($post as $key => $p){
	$viewthread['post'][$n]['pid'] = $p['pid'];
	$viewthread['post'][$n]['first'] = $p['first'];
	$viewthread['post'][$n]['author'] = iconv('gbk','utf-8',$p['author']);
	$viewthread['post'][$n]['authorid'] = $p['authorid'];
	$viewthread['post'][$n]['date'] = $p['dateline'];
	
	$arr = preg_replace_conten($p['message']);
	$attachmentns = C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$p['tid'], 'pid', $p['pid']);
	$attach = array();
	$i = 0;
	foreach($attachmentns as $att){
		$attach[$i]['url'] = "http://bbs-test.we54.com/data/attachment/forum/".$att['attachment'];
		$attach[$i]['description'] = $att['description'];
		$i++;
	}
	$viewthread['post'][$n]['attachment'] = $attach;
	
	$viewthread['post'][$n]['message'] = iconv('gbk','utf-8',$arr['content']);
	$n++;
}

echo json_encode($viewthread);

function preg_replace_conten($content){
	$reg_q = "/\[quote\](.)*\[\/quote\]/isU"; //过滤引用

	$reg_i = "/\[i\](.)*\[\/i\]/isU";   //过滤i标签
	
	$reg_pic = "/\[attach\](.)*\[\/attach\]/isU"; //过滤图片

	$reg_o = "/\[(.)*\]/isU";     //过滤其它所有的标签
	$reg_n = "/{[\d]*}/isU";      //过滤表情
	
	$content = preg_replace($reg_q,'',$content);
	$content = preg_replace($reg_i,'',$content);
	
	preg_match_all($reg_pic,$content,$match);
	$arr['att'] = get_att($match[0]);
	
	$content = preg_replace($reg_pic,'',$content);
	$content = preg_replace($reg_o,'',$content);
	$content = preg_replace($reg_n,'',$content);
	
	$arr['content'] = $content;
	
	return $arr;
}

function get_att($arr){
	$reg_att = "/\[(.)*\]/isU";
	foreach($arr as $key => $att){
		$arr[$key] = preg_replace($reg_att,'',$att);
	}
	return $arr;
}
?>