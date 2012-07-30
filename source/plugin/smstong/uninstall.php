<?php
/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: uninstall.php 20324 2011-06-02 15:31:40Z я╫я╫╦Жеч $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

--DROP TABLE IF EXISTS `pre_common_verifycode`;

--DROP TABLE IF EXISTS `sms_send`;

ALTER TABLE `pre_common_member` DROP COLUMN `mobilestatus`;

EOF;

runquery($sql);

require_once libfile('function/cache');
if(!isset($_G['setting']['privacy']['profile']) || $_G['setting']['privacy']['profile'][$fieldid] != $_POST['privacy']) {
	$_G['setting']['privacy']['profile'][$fieldid] = $_POST['privacy'];
	DB::insert('common_setting', array('skey'=>'privacy', 'svalue'=> addslashes(serialize($_G['setting']['privacy']))), false, true);
}
updatecache(array('profilesetting','fields_required', 'fields_optional', 'fields_register', 'setting'));

$finish = TRUE;

?>