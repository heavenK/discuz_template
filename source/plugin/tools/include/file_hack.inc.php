<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: file_hack.inc.php 79 2012-04-16 10:06:12Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');
if(submitcheck('firsthacksubmit') || submitcheck('sechacksubmit')){
	$check = '';
	if($_GET['firsthacksubmit']) {
		$rule2 = $rule['first'];
		searchkeyword($rule2,'./',1,array('attachment','template'),1);	
	} elseif($_GET['sechacksubmit']) {
		$rule2 = $rule['sec'];
		searchkeyword($rule2,'./',1,array('attachment','template'),1);			
	}
	
	if(is_array($check) && count($check) > 0) {
		showtableheader($toolslang['file_result']."<font color=red>$rule2</font>");
		showsubtitle(array('', $toolslang['file_realpath'],$toolslang['file_hackresult']));
		foreach($check as $key => $value){
			if($value){
				showtablerow('', array(), array('',$key,$value));	
			}
		}
		showtablefooter();
	} else {
		cpmsg($toolslang['nocheck'],"action=plugins&cp=file_hack&pmod=safe&operation=$operation&do=$do&identifier=$identifier",'error');	
	}
}
showformheader("plugins&cp=file_hack&pmod=safe&operation=$operation&do=$do&identifier=$identifier");
showtipss($toolslang['file_hacktip']);
showtableheaders($toolslang['file_hack']);
foreach($rule as $key => $value){
	showsubmit($key.'hacksubmit','submit',$value);
}
showtablefooter();
if(is_array($filelist) && count($filelist) > 0){
	showtableheader($toolslang['file_php_result']);
	showsubtitle(array('', $toolslang['file_path']));
	foreach($filelist as $value) {
		showtablerow('',array(),array('',realpath($value)));	
	}
	showtablefooter();	
}
?>