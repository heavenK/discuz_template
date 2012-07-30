<?php
If(!defined('IN_DISCUZ')) {
	Exit('Access Deined');
}
@include_once DISCUZ_ROOT . '/data/cache/bshare.inc';
@include_once DISCUZ_ROOT . '/data/cache/bshare.show.inc';

global $_G;

$time = time() . '000';	
$md = md5('ts=' . time() . '000uuid=' . $cfg_bshare['uuid'] . $cfg_bshare['sk']);

$dataUrl ='http://www.bshare.cn/publisherStatisticsEmbed?uuid=' . $cfg_bshare['uuid'] . '&ts=' . $time . '&sig=' . $md;

include template('bshare:data');
?>