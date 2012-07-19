<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: file_search.inc.php 79 2012-04-16 10:06:12Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');
if(!submitcheck('search')) {
    showformheader("plugins&cp=file_search&pmod=safe&operation=$operation&do=$do&identifier=$identifier");
	showtableheaders($toolslang['file_search']);
	$dirlist[] = array('.','./');
	getdirentry('./');
	
	$showlist = array('sdir[]',$dirlist);
	showsetting($toolslang['file_keyword'],'keyword','','text','',0,$toolslang['file_keywordtip']);
	showsetting($toolslang['file_searchdir'],$showlist,'','mselect','',0,$toolslang['file_searchdirtip']);
	showsubmit('search');
	showtablefooter();
} else {
	if(empty($_GET['keyword'])){
		cpmsg($toolslang['file_nokeyword'],"action=plugins&cp=file_search&pmod=safe&operation=$operation&do=$do&identifier=$identifier",'error');
		exit;	
	}
	if(empty($_GET['sdir'])){
		cpmsg($toolslang['file_nodir'],"action=plugins&cp=file_search&pmod=safe&operation=$operation&do=$do&identifier=$identifier",'error');
		exit;
	}
	$_GET['keyword'] = str_replace('*','(.*)',$_GET['keyword']);
	$keyword = strtolower(dstripslashes($_GET['keyword']));
	$dir = $_GET['sdir'];
	$check = '';
	$keyword2 = str_replace(array('.','/','$','(',')','?','{','}','|','+','[',']','^'),array('\.','\/','\$','\(','\)','\?','\{','\}','\|','\+','\[','\]','\^'),$keyword);
	
	foreach($dir as $value){
		$sub = $value == '.' ? 0 : 1;
		//echo $value;exit;
		searchkeyword($keyword2,$value.'/',$sub);
	}
	
	if(is_array($check) && count($check) > 0) {
		showtableheaders($toolslang['file_result']."<font color=red>$keyword</font>");
		showsubtitle(array('', $toolslang['file_realpath'],$toolslang['file_keyrows']));
		foreach($check as $key => $value){
			if($value){
				showtablerow('', array(), array('',$key,$value));	
			}
		}
		showtablefooter();
	} else {
		cpmsg($toolslang['nocheck'],"action=plugins&cp=file_search&pmod=safe&operation=$operation&do=$do&identifier=$identifier",'error');
	}
}	
?>