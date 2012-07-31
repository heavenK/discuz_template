<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: help.inc.php 18582 2010-07-19 10:44:50Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('plugin');

$Plang = $scriptlang['smstong'];

if (!$_G['cache']['plugin']['smstong']) {
	cpmsg($Plang['smstong_plugin_closed'], "action=plugins", 'error');
}

$V_INFO['identifier'] = trim($_G['gp_identifier']);
$V_INFO['version'] = $_G['setting']['plugins']['version'][$V_INFO['identifier']];

showtableheader();

showtips(lang('plugin/smstong', 'smstong_help_question_desc'),'version' ,true, lang('plugin/smstong', 'smstong_help_question'));

showtips(lang('plugin/smstong', 'smstong_help_check_desc', array('version' => $V_INFO['version'])) ,'version' ,true, lang('plugin/smstong', 'smstong_help_check'));

showtips(lang('plugin/smstong', 'smstong_help_doc_desc'),'version' ,true, lang('plugin/smstong', 'smstong_help_doc'));

showtablefooter();

$V_INFO['siteurl'] = $_G['siteurl'];
$V_INFO['bbname'] = $_G['setting']['bbname'];
$V_INFO['charset'] = $_G['config']['output']['charset'];
$V_INFO = urlencode(base64_encode(serialize($V_INFO)));
$smstong_url = 'http://www.chanyoo.cn/plugin.php?id=smstong_version';
$smstong_url .= '&info='.$V_INFO;
echo "<script type=\"text/javascript\" src=\"{$smstong_url}\"></script>";

?>