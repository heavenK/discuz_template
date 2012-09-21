<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$tid = 585775;
$pid = 5511127;

		
		$cover = C::t('forum_attachment_n')->count_image_by_id('tid:'.$tid, 'pid', $pid);



var_dump($cover);
?>