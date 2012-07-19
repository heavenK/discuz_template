<?php
$dz_root = substr(DISCUZ_ROOT, 0, -8);
define('UCENTER_URL', $_config['ucenter']['ucenterurl']);
define('UCENTER_ROOT',$dz_root.'./uc_server/');	
define('DZ_ROOT', $dz_root);
define('IN_DISCUZ', TRUE);
$curprg = basename(__FILE__);
//echo UCENTER_ROOT;exit();
if(!file_exists(UCENTER_ROOT.'./data/avatar')) {
	echo 'UCenter 路径不存在，请修改本工具设置正确';
	exit;
}
$nextid = 0;
$start = intval(getgpc('start'));
$total = intval(getgpc('total'));
$limit = 1;
$table_source = $db_source->tablepre.'memberfields';

$query = $db_source->query("SELECT uid, avatar FROM $table_source WHERE uid>$start && avatar!='' LIMIT $limit");
while($data = $db_source->fetch_array($query)) {

	$nextid = $data['uid'];
	if(preg_match_all('/^customavatars\/(\d+)\.(.+?)$/', $data['avatar'], $a)) {
		set_home($data['uid'], UCENTER_ROOT.'./data/avatar');
		$avatar = DZ_ROOT.'old/customavatars/'.$a[1][0].'.'.$a[2][0];
		$ucavatar = UCENTER_ROOT.'data/avatar/'.get_avatar($data['uid'], 'middle');
		if(!file_exists($ucavatar)) {
			$create = FALSE;
			$img = new Image_Lite($avatar, $ucavatar);
			if($img->imagecreatefromfunc && $img->imagefunc) {
				if($img->Thumb(120, 120)) {
					$create = TRUE;
					$total++;
				}
			}
			if($create) {
				$ucavatar = UCENTER_ROOT.'./data/avatar/'.get_avatar($data['uid'], 'small');
				$img = new Image_Lite($avatar, $ucavatar);
				if($img->imagecreatefromfunc && $img->imagefunc) {
					$img->Thumb(48, 48);
				}
				$ucavatar = UCENTER_ROOT.'./data/avatar/'.get_avatar($data['uid'], 'big');
				@copy($avatar, $ucavatar);
				echo '<img src="'.UCENTER_URL.'./data/avatar/'.get_avatar($data['uid'], 'small').'" />';
			}
		}
	}
}

if($nextid) {
	showmessage("继续转换主题分类数据表，uid=$nextid", "index.php?a=$action&total=$total&source=$source&prg=$curprg&start=$nextid");
} else {
	echo "<br /><br /><br />导入完毕，共导入 $total 个头像！";
}

function set_home($uid, $dir = '.') {
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	!is_dir($dir.'/'.$dir1) && mkdir($dir.'/'.$dir1, 0777);
	!is_dir($dir.'/'.$dir1.'/'.$dir2) && mkdir($dir.'/'.$dir1.'/'.$dir2, 0777);
	!is_dir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3) && mkdir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3, 0777);
	return $dir1.'/'.$dir2.'/'.$dir3;
}

function get_home($uid) {
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	return $dir1.'/'.$dir2.'/'.$dir3;
}

function get_avatar($uid, $size = 'big') {
	$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
	$uid = abs(intval($uid));
	$uid = sprintf("%09d", $uid);
	$dir1 = substr($uid, 0, 3);
	$dir2 = substr($uid, 3, 2);
	$dir3 = substr($uid, 5, 2);
	return $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2)."_avatar_$size.jpg";
}

class Image_Lite {

	var $attachinfo = '';
	var $srcfile = '';
	var $targetfile = '';
	var $imagecreatefromfunc = '';
	var $imagefunc = '';
	var $attach = array();
	var $animatedgif = 0;

	function Image_Lite($srcfile, $targetfile) {
		$this->srcfile = $srcfile;
		$this->targetfile = $targetfile;
		$this->attachinfo = @getimagesize($srcfile);
		switch($this->attachinfo['mime']) {
			case 'image/jpeg':
				$this->imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
				$this->imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
				break;
			case 'image/gif':
				$this->imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
				$this->imagefunc = function_exists('imagegif') ? 'imagegif' : '';
				break;
			case 'image/png':
				$this->imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
				$this->imagefunc = function_exists('imagepng') ? 'imagepng' : '';
				break;
		}

		$this->attach['size'] = @filesize($srcfile);
		if($this->attachinfo['mime'] == 'image/gif') {
			$fp = fopen($srcfile, 'rb');
			$targetfilecontent = fread($fp, $this->attach['size']);
			fclose($fp);
			$this->animatedgif = strpos($targetfilecontent, 'NETSCAPE2.0') === FALSE ? 0 : 1;
		}
	}

	function Thumb($thumbwidth, $thumbheight, $preview = 0) {
		$return = $this->Thumb_GD($thumbwidth, $thumbheight);
		$this->attach['size'] = @filesize($this->targetfile);
		return $return;
	}

	function Thumb_GD($thumbwidth, $thumbheight) {
		if(function_exists('imagecreatetruecolor') && function_exists('imagecopyresampled') && function_exists('imagejpeg')) {
			$imagecreatefromfunc = $this->imagecreatefromfunc;
			$imagefunc = $this->imagefunc;
			list($img_w, $img_h) = $this->attachinfo;

			if(!$this->animatedgif && ($img_w >= $thumbwidth || $img_h >= $thumbheight)) {

				$attach_photo = $imagecreatefromfunc($this->srcfile);

				$imgratio = $img_w / $img_h;
				$thumbratio = $thumbwidth / $thumbheight;

				if($imgratio >= 1 && $imgratio >= $thumbratio || $imgratio < 1 && $imgratio > $thumbratio) {
					$cuty = $img_h;
					$cutx = $cuty * $thumbratio;
				} elseif($imgratio >= 1 && $imgratio <= $thumbratio || $imgratio < 1 && $imgratio < $thumbratio) {
					$cutx = $img_w;
					$cuty = $cutx / $thumbratio;
				}

				$dst_photo = imagecreatetruecolor($cutx, $cuty);
				imageCopyMerge($dst_photo, $attach_photo, 0, 0, 0, 0, $cutx, $cuty, 100);

				$thumb['width'] = $thumbwidth;
				$thumb['height'] = $thumbheight;

				$targetfile = $this->targetfile;

				$thumb_photo = imagecreatetruecolor($thumb['width'], $thumb['height']);
				imageCopyreSampled($thumb_photo, $dst_photo ,0, 0, 0, 0, $thumb['width'], $thumb['height'], $cutx, $cuty);
				clearstatcache();
				if($this->attachinfo['mime'] == 'image/jpeg') {
					$imagefunc($thumb_photo, $targetfile, 100);
				} else {
					$imagefunc($thumb_photo, $targetfile);
				}
				return TRUE;
			}
		}
		return FALSE;
	}

}

?>
