<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: clean_att.inc.php 79 2012-04-16 10:06:12Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');
showtipss($toolslang['clearatt']);
if(submitcheck('att_clean_submit')){
	if(count($_G['gp_attarray']) <= 0) {
		cpmsg($toolslang['clearatt_noselect'],NULL,'error');	
	} else {
		foreach($_G['gp_attarray'] as $value){
			@unlink(DISCUZ_ROOT.'/data/attachment/'.$value);	
			@unlink(DISCUZ_ROOT.'/data/attachment/'.$value.'.thumb.jpg');
		}
		cpmsg($toolslang['clearatt_done'],"action=plugins&operation=$operation&do=$do&identifier=$identifier&pmod=maintain&cp=clean_att",'succeed');	
	}
}
if(submitcheck('att_submit')){
	set_time_limit(0);
	if(function_exists(ini_set)){
		ini_set('memory_limit','256M');
	}		
	
	$dlist = array();
    
	$dir = $_G['gp_dira'];
	$mod = preg_match('/(album|forum|portal)/im', $dir,$match);
	$mod = $match[0];
	$att = '';
	dlist($dir,intval($xver));
	if(count($dlist) <= 0) {
		cpmsg($toolslang['clearatt_nolaji'],"action=plugins&operation=$operation&do=$do&identifier=$identifier&pmod=maintain&cp=clean_att",'error');	
	}
	foreach($dlist as $key => $value){
		$att .= showtablerow('', array('class="td25"', ''), array(
				"<input type=\"checkbox\" name=\"attarray[]\" value=\"$value\" class=\"checkbox\">",
				"<a href=\"data/attachment/{$mod}/{$key}\" target=\"_blank\">$value</a>",
			), TRUE);
	}
	showformheader("plugins&operation=$operation&do=$do&identifier=$identifier&pmod=maintain&cp=clean_att");
	showtableheaders($toolslang['clearatt_lajiatt']);
	echo $att;
	showsubmit('att_clean_submit', 'submit', '<input type="checkbox" name="chkall" onclick="checkAll(\'prefix\', this.form, \'attarray\')" class="checkbox">'.cplang('del'), '');
	showtablefooter();
	showformfooter();
} else {
	if(intval($xver) >= 2){
		$tablearray = array('forum_attachment_0' => 'attachment',
		'forum_attachment_1' => 'attachment',
		'forum_attachment_2' => 'attachment',
		'forum_attachment_3' => 'attachment',
		'forum_attachment_4' => 'attachment',
		'forum_attachment_5' => 'attachment',
		'forum_attachment_6' => 'attachment',
		'forum_attachment_7' => 'attachment',
		'forum_attachment_8' => 'attachment',
		'forum_attachment_9' => 'attachment',
		'home_pic' => 'filepath',
		'portal_attachment' => 'attachment');
	} else {
		$tablearray = array('forum_attachment' => 'attachment',
		'home_pic' => 'filepath',
		'portal_attachment' => 'attachment');	
	}
	foreach($tablearray as $key => $value){
	  
		checkattindex($key,$value);	
	}
	
	$dirlist = array();
	foreach(glob(DISCUZ_ROOT."/data/attachment/portal/*",GLOB_ONLYDIR) as $dirname){
		$dirlist[] = array($dirname,str_replace(DISCUZ_ROOT.'/data/attachment/','',$dirname));		
	}
	foreach(glob(DISCUZ_ROOT."/data/attachment/album/*",GLOB_ONLYDIR) as $dirname){
		if(strpos($dirname,'cover') === false){
			$dirlist[] = array($dirname,str_replace(DISCUZ_ROOT.'/data/attachment/','',$dirname));	
		}
	}
	foreach(glob(DISCUZ_ROOT."/data/attachment/forum/*",GLOB_ONLYDIR) as $dirname){
		$dirlist[] = array($dirname,str_replace(DISCUZ_ROOT.'/data/attachment/','',$dirname));		
	}
	showformheader("plugins&operation=$operation&do=$do&identifier=$identifier&pmod=maintain&cp=clean_att",'submit');
	
    showtableheaders();
	showsetting('dir',array('dira',$dirlist),'','select','','');
	showsubmit('att_submit', 'submit');
	showtablefooter();
	showformfooter();
}
?>