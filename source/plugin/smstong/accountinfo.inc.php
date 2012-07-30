<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: acountinfo.inc.php 18582 2010-07-17 10:38:36Z я╫я╫╦Жеч $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('plugin');

$Plang = $scriptlang['smstong'];

if (!$_G['cache']['plugin']['smstong']) {
	cpmsg($Plang['smstong_plugin_closed'], "action=plugins", 'error');
}

if (($_G['cache']['plugin']['smstong']['smsusername'] == 'demo') && ($_G['cache']['plugin']['smstong']['smspassword'] == 'demo')) {
	cpmsg($Plang['smstong_username_password_empty'], "action=plugins&operation=config&do=$_G[gp_do]", 'error');
}

if (empty($_G['cache']['plugin']['smstong']['smsusername']) || empty($_G['cache']['plugin']['smstong']['smspassword'])) {
	cpmsg($Plang['smstong_username_password_empty'], "action=plugins&operation=config&do=$_G[gp_do]", 'error');
}

echo '<iframe id="frame_content" src="source/plugin/smstong/accountinfo.php?username='.$_G['cache']['plugin']['smstong']['smsusername'].'&password='.$_G['cache']['plugin']['smstong']['smspassword'].'" scrolling="no" frameborder="0" onload="this.height=this.contentWindow.document.documentElement.scrollHeight" style="position:absolute; left:0px; top:50px; width:100%; border:0px;"></iframe>';

?>