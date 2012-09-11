<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();


isset($_REQUEST['tid']) ? $tid = $_REQUEST['tid'] : $res['err'] = 1;
isset($_REQUEST['pid']) ? $pid = $_REQUEST['pid'] : $res['err'] = 1;
isset($_REQUEST['uid']) ? $uid = $_REQUEST['uid'] : $res['err'] = 1;
isset($_REQUEST['description']) ? $description = $_REQUEST['description'] : $description = '';

$filename = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : '';
$filesize = isset($_REQUEST['filesize']) ? $_REQUEST['filesize'] : '';
$width = isset($_REQUEST['width']) ? $_REQUEST['width'] : 0;
$cover = isset($_REQUEST['cover']) ? $_REQUEST['cover'] : 0;

isset($_REQUEST['pic']) ? $s = $_REQUEST['pic'] : $res['err'] = 1;

if($res['err'] != 1){
	$path = '../data/attachment/forum/';
	$attachments = 'ios/' .date('YmdHsi') .md5(time()) .'.jpg';
	file_put_contents($path .$attachments, $s);
	$aid = getattachnewaid($uid);
	
	$update = array();
	$update['aid'] = $aid;
	$update['tid'] = $tid;
	$update['pid'] = $pid;
	$update['uid'] = $uid;
	$update['dateline'] = time();
	$update['filename'] = $filename;
	$update['filesize'] = $filesize;
	$update['attachment'] = $attachments;
	$update['filesize'] = 0;
	$update['description'] = censor(cutstr(dhtmlspecialchars($description), 100));
	$update['readperm'] = 0;
	$update['price'] = 0;
	$update['isimage'] = 1;
	$update['width'] = $width;
	$update['thumb'] = 0;
	$update['picid'] = 0;
	$update['sha1'] = '';
	
	C::t('forum_attachment_n')->insert('tid:'.$tid, $update, false, true);
	C::t('forum_attachment')->update($aid, array('tid' => $tid, 'pid' => $pid, 'tableid' => getattachtableid($tid)));
	
	$attachcount = C::t('forum_attachment_n')->count_by_id('tid:'.$tid, $pid ? 'pid' : 'tid', $pid ? $pid : $tid);
	$attachment = 0;
	if($attachcount) {
		if(C::t('forum_attachment_n')->count_image_by_id('tid:'.$tid, $pid ? 'pid' : 'tid', $pid ? $pid : $tid)) {
			$attachment = 2;
		} else {
			$attachment = 1;
		}
	} else {
		$attachment = 0;
	}
	C::t('forum_thread')->update($tid, array('attachment'=>$attachment));
	C::t('forum_post')->update('tid:'.$tid, $pid, array('attachment' => $attachment), true);

	if(!$attachment) {
		C::t('forum_threadimage')->delete_by_tid($tid);
	}
	
	if($cover){
		$basedir = DISCUZ_ROOT.'./data/attachment/';
		$coverdir = 'threadcover/'.substr(md5($tid), 0, 2).'/'.substr(md5($tid), 2, 2).'/';
		dmkdir($basedir.'./forum/'.$coverdir);
		$covername = $path.$coverdir.$tid.'.jpg';
		
		cut_pic($path.$attachments,$covername);
		
		$cover = 1;
		C::t('forum_thread')->update($tid, array('cover' => $cover));
	}
	
	$res['aid'] = $aid;
	$res['err'] = 0;
}

function cut_pic($src_img ,$filename){
	list($src_w,$src_h)=getimagesize($src_img);  // 获取原图尺寸

	$dst_h = 580;
	$dst_w = 580;

	$dst_scale = $dst_h/$dst_w; //目标图像长宽比
	$src_scale = $src_h/$src_w; // 原图长宽比
	
	if($src_scale>=$dst_scale){  // 过高
		$w = intval($src_w);
		$h = intval($dst_scale*$w);
		
		$x = 0;
		$y = ($src_h - $h)/3;
	}
	else{ // 过宽
		$h = intval($src_h);
		$w = intval($h/$dst_scale);
		
		$x = ($src_w - $w)/2;
		$y = 0;
	}
	
	// 剪裁
	$source=imagecreatefromjpeg($src_img);
	$croped=imagecreatetruecolor($w, $h);
	imagecopy($croped,$source,0,0,$x,$y,$src_w,$src_h);
	
	// 缩放
	$scale = $dst_w/$w;
	$target = imagecreatetruecolor($dst_w, $dst_h);
	$final_w = intval($w*$scale);
	$final_h = intval($h*$scale);
	imagecopyresampled($target,$croped,0,0,0,0,$final_w,$final_h,$w,$h);
	
	
	// 保存
	imagejpeg($target, $filename);
}

echo json_encode($res);
?>