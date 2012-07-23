<?php

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$filename = DISCUZ_ROOT . '/data/cache/bshare.inc';
$filenameshow = DISCUZ_ROOT . '/data/cache/bshare.show.inc';

if (file_exists($filename)) {
    unlink($filename);
}

if (file_exists($filenameshow)) {
    unlink($filenameshow);
}

if (file_exists($filenamecode)) {
    unlink($filenamecode);
}

$finish = TRUE;

?>
