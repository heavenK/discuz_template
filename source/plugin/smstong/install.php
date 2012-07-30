<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: install.php 22778 2011-06-01 15:31:40Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$_GET['finish'] = (isset($_GET['finish']) && $_GET['finish'] == 1) ? 1 : 0;
if (!$_GET['finish']) {

echo '<iframe src="source/plugin/smstong/checkenv.php?operation='.$_GET['operation'].'&installtype='.$_GET['installtype'].'" scrolling="no" frameborder="0" onload="this.height=this.contentWindow.document.documentElement.scrollHeight" style="position:absolute; left:0px; top:50px; width:100%; border:0px;"></iframe>';

} else {

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `pre_common_verifycode` (
`id` mediumint(8) unsigned NOT NULL auto_increment,
`mobile` char(12) NOT NULL,
`getip` char(15) NOT NULL,
`verifycode` char(6) NOT NULL,
`dateline` int(10) unsigned NOT NULL default '0',
`reguid` mediumint(8) unsigned default '0',
`regdateline` int(10) unsigned default '0',
`status` tinyint(1) NOT NULL default '1',
PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

ALTER TABLE `pre_common_member` ADD COLUMN `mobilestatus` tinyint(1) NOT NULL default '0' AFTER `status`;

UPDATE `pre_common_member_profile_setting` SET available=1, invisible=1, displayorder=0, unchangeable=0, showincard=0, showinthread=0, showinregister=0, allowsearch=0 WHERE fieldid='mobile';

-- ----------------------------
-- Table structure for sms_send
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sms_send` (
`id` mediumint(8) NOT NULL AUTO_INCREMENT,
`mobile` char(12) NOT NULL,
`content` varchar(300) NOT NULL,
`addtime` timestamp NULL DEFAULT NULL,
`senttime` timestamp NULL DEFAULT NULL,
`count` smallint(6) NOT NULL DEFAULT '1',
`status` smallint(6) NOT NULL DEFAULT '0',
`remark` varchar(32) DEFAULT NULL,
`refno` smallint(6) NOT NULL DEFAULT '0',
`port` char(10) NOT NULL DEFAULT 'COM1',
PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ----------------------------
-- Table structure for sms_recv
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sms_recv` (
`id` mediumint(8) NOT NULL AUTO_INCREMENT,
`mobile` char(32) NOT NULL,
`content` varchar(300) NOT NULL,
`recvtime` timestamp NOT NULL,
`port` char(10) NOT NULL DEFAULT 'COM1',
`senttime` timestamp NOT NULL,
`remark` varchar(32) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM;

EOF;

runquery($sql);

require_once libfile('function/cache');
if(!isset($_G['setting']['privacy']['profile']) || $_G['setting']['privacy']['profile'][$fieldid] != $_POST['privacy']) {
	$_G['setting']['privacy']['profile'][$fieldid] = $_POST['privacy'];
	DB::insert('common_setting', array('skey'=>'privacy', 'svalue'=> addslashes(serialize($_G['setting']['privacy']))), false, true);
}
updatecache(array('profilesetting','fields_required', 'fields_optional', 'fields_register', 'setting'));

$finish = TRUE;

}

?>