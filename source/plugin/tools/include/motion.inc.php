<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: motion.inc.php 79 2012-04-16 10:06:12Z wangbin $
 */

if(submitcheck('motion_viewsubmit')) {
		if(!is_num($_GET['tid']) || !is_num($_GET['views'])){
			cpmsg($toolslang['motion_error'],"action=plugins&pmod=operation&cp=motion&operation=$operation&do=$do&identifier=$identifier",'error');	
		}
		$_GET['tid'] = intval($_GET['tid']);
		$_GET['views'] = intval($_GET['views']);
		$threadtalbe = getallthreadtable();
		$tid = 0;
		foreach($threadtalbe as $value){
			$temptid = DB::result_first("SELECT tid FROM ".DB::table($value)." WHERE tid='".$_GET['tid']."'");
			if($temptid > 0){
				$thread = $value;	
			}
			$tid = ($tid || $temptid);
		}
		if(!$tid){
			cpmsg($toolslang['motion_emptytid'],"action=plugins&pmod=operation&cp=motion&operation=$operation&do=$do&identifier=$identifier",'error');	
		}
		DB::update($thread,array('views' => $_GET['views']),"tid = $_GET[tid]");
		cpmsg($toolslang['motion_success'],"action=plugins&pmod=operation&cp=motion&operation=$operation&do=$do&identifier=$identifier",'succeed');	
	} elseif(submitcheck('motion_hispostsubmit')) {
		if(!is_num($_GET['hispost']) || !is_num($_GET['fid'])){
			cpmsg($toolslang['motion_hiserror'],"action=plugins&pmod=operation&cp=motion&operation=$operation&do=$do&identifier=$identifier",'error');	
		}
		$_GET['hispost'] = intval($_GET['hispost']);
		$_GET['fid'] = intval($_GET['fid']);
		$fidcount = DB::result_first("SELECT count(*) FROM ".DB::table('forum_forum')." WHERE fid = $_GET[fid]");
		if($fidcount == 0){
			cpmsg($toolslang['motion_nofid'],"action=plugins&pmod=operation&cp=motion&operation=$operation&do=$do&identifier=$identifier",'error');	
		} else {
			DB::update('forum_forum',array('todayposts' => "$_GET[hispost]"),"fid = $_GET[fid]");
			cpmsg($toolslang['motion_success'],"action=plugins&pmod=operation&cp=motion&operation=$operation&do=$do&identifier=$identifier",'succeed');	
		}
	}
	
	showformheader("plugins&pmod=operation&cp=motion&operation=$operation&do=$do&identifier=$identifier",'submit');
	showtableheaders($toolslang['motion_threadclick']);
	showsetting($toolslang['motion_tid'],'tid','','text');
	showsetting($toolslang['motion_views'],'views','','text');
	showsubmit('motion_viewsubmit', $toolslang['submit']);
	showtablefooter();
	//historyposts
	showtableheaders($toolslang['motion_hispost']);
	showsetting($toolslang['motion_forumfid'],'fid','','text');
	showsetting($toolslang['motion_forumpost'],'hispost','','text');
	showsubmit('motion_hispostsubmit', $toolslang['submit']);
	showtablefooter();
	showformfooter();	
?>