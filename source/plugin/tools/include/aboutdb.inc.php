<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: aboutdb.inc.php 79 2012-04-16 10:06:12Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');
$rpp = $_G['gp_rpp'] ? $_G['gp_rpp'] : 1000;
$rows = $_G['gp_rows'] ? $_G['gp_rows'] : 0;

$data = DB::fetch_first("SELECT MAX(tid) as maxtid,MIN(tid) as mintid,count(tid) as count FROM ".DB::table('forum_thread'));
$maxtid = $data['maxtid'];$mintid = $data['mintid'];$count = $data['count'];
$posttable = array('p' => getposttable('p',0),'a' => getposttable('a',0));
if($xver == 2.5){ //X2.5 兼容
	$posttable = array('p' => DB::table(getposttable('p',0)),'a' => DB::table(getposttable('a',0)));	
}

$data = DB::fetch_first("SELECT MAX(pid) as maxpid,MIN(pid) as minpid,count(pid) as count FROM ".$posttable['p']);
$maxpid = $data['maxpid'];$minpid = $data['minpid'];$countpid = $data['count'];
$maxposttableid = DB::result_first("SELECT MAX(posttableid) FROM ".DB::table('forum_thread'));
$allposttalbe = array('forum_post');
$i = 1;
while($i <= $maxposttableid){
	$allposttalbe[] = 'forum_post_'.$i;
	$i++;
}
loadcache('threadtableids');
foreach($_G['cache']['threadtableids'] as $value){
	$allthreadtalbe[] = 'forum_thread_'.$value;
}

showtipss($toolslang['cleardbtips']);
if(submitcheck('clearpostsubmit',1)){
	$id = getmaxmin(getposttable('primary'),'pid');
	if($_G['gp_start'] == 0){
		$_G['gp_start'] = $id['min'];
	}
	$start = $_G['gp_start'];
	$end = $_G['gp_start'] + $rpp;
	$posttable = getposttable('primary');
	
	$query = DB::query("SELECT pid,tid FROM ".DB::table($posttable)." WHERE pid >= $start AND pid < $end");
	//note
	while($post = DB::fetch($query)){
		$tid = DB::result_first("SELECT tid FROM ".DB::table('forum_thread')." WHERE tid='".$post['tid']."'");
		foreach($allthreadtalbe as $value) {
			$tid = ($tid || DB::result_first("SELECT tid FROM ".DB::table($value)." WHERE tid='".$post['tid']."'"));
		}
		if(!$tid) {
			$rows ++;
			DB::delete($posttable,"pid = $post[pid]");
		}
	}
	$nextlink = "action=plugins&cp=aboutdb&pmod=maintain&identifier=tools&operation=$operation&do=$do&start=$end&rows=$rows&clearpostsubmit=yes&rpp=$rpp";
	if($end <= $id['max']+1){
		cpmsg("$lang[counter_forum]: ".cplang('counter_processing', array('current' => $start, 'next' => $end)), $nextlink, 'loading');	
	} else {
		$inc = $id['max']+1;
		DB::query("ALTER TABLE ".DB::table('forum_post')." AUTO_INCREMENT = $inc");
		cpmsg('tools:success',"action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb",'succeed');	
	}
} elseif(submitcheck('clearthreadsubmit',1)) {
	$id = getmaxmin('forum_thread','tid');
	if($_G['gp_start'] == 0){
		$_G['gp_start'] = $id['min'];
	}
	$start = $_G['gp_start'];
	$end = $_G['gp_start'] + $rpp;

	$query = DB::query("SELECT tid,subject FROM ".DB::table('forum_thread')." WHERE tid >= $start AND tid < $end");
	while($thread = DB::fetch($query)){
		$posttableid = getposttablebytid($thread[tid]);
		$posts = DB::result_first("SELECT count(*) FROM ".DB::table("$posttableid")." WHERE tid = $thread[tid]");
		if($posts <= 0) {
			$rows ++;
			DB::delete('forum_thread',"tid = $thread[tid]");
		} elseif($thread['subject'] == '') {
			$rows ++;
			DB::delete('forum_thread',"tid = $thread[tid]");	
			DB::delete("$posttableid","tid = $thread[tid]");	
		} else {
			$query = DB::query("SELECT a.aid FROM ".DB::table("$posttableid")." p,".DB::table('forum_attachment')." a WHERE a.tid = $thread[tid] AND a.pid = p.pid AND p.invisible = 0 LIMIT 1");	
			$attachment = DB::num_rows($query) ? 1 : 0;//修复附件
			$query  = "SELECT pid, subject, rate FROM ".DB::table("$posttableid")." WHERE tid= $thread[tid]  AND invisible='0' ORDER BY dateline LIMIT 1";
			$firstpost = DB::fetch_first($query);
			$firstpost['subject'] = trim($firstpost['subject']) ? $firstpost['subject'] : $thread['subject']; //针对某些转换过来的论坛的处理
			$firstpost['subject'] = addslashes($firstpost['subject']);
			@$firstpost['rate'] = $firstpost['rate'] / abs($firstpost['rate']);//修复发帖
			$query  = "SELECT author, dateline FROM ".DB::table("$posttableid")." WHERE tid= $thread[tid] AND invisible='0' ORDER BY dateline DESC LIMIT 1";
			$lastpost = DB::fetch_first($query);//修复最后发帖
			DB::update('forum_thread',array("subject" => $firstpost[subject],"replies" => $posts,"lastpost" => $lastpost[dateline],"lastposter" => addslashes($lastpost[author]),"rate" => $firstpost[rate],"attachment" => $attachment),"tid = $thread[tid]",1);
			DB::update("$posttableid",array('first' => '1','subject' => $firstpost[subject]),"pid = $firstpost[pid]",1);
			DB::update("$posttableid",array('first' => '0','subject' => $firstpost[subject]),"tid = $thread[tid] AND pid <> $firstpost[pid]",1);
		}
	}
	$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&start=$end&rows=$rows&clearthreadsubmit=yes&rpp=$rpp&pmod=maintain&cp=aboutdb";
	if($end <= $id['max']+1){
		cpmsg("$lang[counter_forum]: ".cplang('counter_processing', array('current' => $start, 'next' => $end)), $nextlink, 'loading');	
	} else {
		$inc = $id['max']+1;
		DB::query("ALTER TABLE ".DB::table('forum_thread')." AUTO_INCREMENT = $inc");
		cpmsg('tools:success',"action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb",'succeed');	
	}
} elseif(submitcheck('clearattachmentsubmit',1)) {
	$id = getmaxmin('forum_attachment','aid');
	
	if($_G['gp_start'] == 0){
		$_G['gp_start'] = $id['min'];
	}
	$start = $_G['gp_start'];
	$end = $_G['gp_start'] + $rpp;
	if(intval($xver) >= 2){
		
		$query = DB::query("SELECT aid,pid,tid FROM ".DB::table('forum_attachment')." WHERE aid >= $start AND aid <= $end");
		while($attach = DB::fetch($query)){
			$tid = $attach['tid'];
			$aid = $attach['aid'];

			foreach($allposttalbe as $value){
				$pid = ($pid || DB::result_first("SELECT pid FROM ".DB::table($value)." WHERE pid='".$attach['pid']."'"));
			}
			if(!$pid) {
				$rows ++;
				DB::delete('forum_attachment',"aid = $attach[aid]");
				$tableid = $tid{strlen($tid)-1};
				$attach['attachment'] = DB::result_first("SELECT attachment FROM ".DB::table('forum_attachment_'.$tableid)." WHERE aid = $aid");
				DB::delete('forum_attachment_'.$tableid,"aid = $attach[aid]");	
				//DB::delete('forum_attachpaymentlog',"aid = $attach[aid]"); DiscuzX 613 去掉
				@unlink($_G['setting']['attachdir'].'/forum/'.$attach['attachment']);
			}
		}
	} else {
		$query = DB::query("SELECT aid,pid,attachment FROM ".DB::table('forum_attachment')." WHERE aid >= $start AND aid <= $end");
		while($attach = DB::fetch($query)){
			$pid = DB::result_first("SELECT pid FROM ".DB::table('forum_post')." WHERE pid='".$attach['pid']."'");
			foreach($allposttalbe as $value){
				$pid = ($pid || DB::result_first("SELECT pid FROM ".DB::table($value)." WHERE pid='".$attach['pid']."'"));
			}
			if(!$pid) {
				$rows ++;
				DB::delete('forum_attachment',"aid = $attach[aid]");
				DB::delete('forum_attachmentfield ',"aid = $attach[aid]");
				//DB::delete('forum_attachpaymentlog',"aid = $attach[aid]"); DiscuzX 613 去掉
				@unlink($_G['setting']['attachdir'].'/forum/'.$attach['attachment']);
			}
		}
	}

	$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&start=$end&rows=$rows&clearattachmentsubmit=yes&rpp=$rpp&pmod=maintain&cp=aboutdb";
	if($end <= $id['max']+1){
		cpmsg("$lang[counter_forum]: ".cplang('counter_processing', array('current' => $start, 'next' => $end)), $nextlink, 'loading');	
	} else {
		$inc = $id['max']+1;
		DB::query("ALTER TABLE ".DB::table('forum_attachment')." AUTO_INCREMENT = $inc");
		cpmsg('tools:success',"action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb",'succeed');	
	}
} elseif(submitcheck('clearmemberssubmit',1)) {
	$id = getmaxmin('common_member_field_forum','uid');
	if($_G['gp_start'] == 0){
		$_G['gp_start'] = $id['min'];
	}
	$start = $_G['gp_start'];
	$end = $_G['gp_start'] + $rpp;
	$query = DB::query("SELECT uid FROM ".DB::table('common_member_field_forum')." WHERE uid >= $start AND uid <= $end");
	while($member = DB::fetch($query)){
		$uid = DB::result_first("SELECT uid FROM ".DB::table('common_member')." WHERE uid='".$member['uid']."'");
		if(!$uid) {
			$rows ++;
			DB::delete('common_member',"uid = $member[uid]");
			@DB::delete('common_member_field_home',"uid = $member[uid]");
			@DB::delete('common_member_log',"uid = $member[uid]");
			@DB::delete('common_member_security',"uid = $member[uid]");
			@DB::delete('common_member_status',"uid = $member[uid]");
			@DB::delete('common_member_profile',"uid = $member[uid]");
		}
	}
	$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&start=$end&rows=$rows&clearmemberssubmit=yes&rpp=$rpp&pmod=maintain&cp=aboutdb";
	if($end <= $id['max']+1){
		cpmsg("$lang[counter_forum]: ".cplang('counter_processing', array('current' => $start, 'next' => $end)), $nextlink, 'loading');	
	} else {
		cpmsg('tools:success',"action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb",'succeed');	
	}
} elseif(submitcheck('clearalbumsubmit',1)) {
	$id = getmaxmin('home_album','albumid');
	if($_G['gp_start'] == 0){
		$_G['gp_start'] = $id['min'];
	}
	$start = $_G['gp_start'];
	$end = $_G['gp_start'] + $rpp;
	$query = DB::query("SELECT albumid,pic FROM ".DB::table('home_album')." WHERE albumid >= $start AND albumid <= $end");
	while($album = DB::fetch($query)){
		$pic = DB::result_first("SELECT count(picid) FROM ".DB::table('home_pic')." WHERE albumid = $album[albumid]");
		if($pic == 0){
			$rows ++;
			DB::delete('home_album',"albumid = $album[albumid]");
			@unlink($_G['setting']['attachdir'].'/album/'.$album['pic']);
		}
	}
	$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&start=$end&rows=$rows&clearalbumsubmit=yes&rpp=$rpp&pmod=maintain&cp=aboutdb";
	if($end <= $id['max']+1){
		cpmsg("$lang[counter_forum]: ".cplang('counter_processing', array('current' => $start, 'next' => $end)), $nextlink, 'loading');	
	} else {
		$inc = $id['max']+1;
		DB::query("ALTER TABLE ".DB::table('home_album')." AUTO_INCREMENT = $inc");
		cpmsg('tools:success',"action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb",'succeed');	
	}	
} elseif(submitcheck('clearpicsubmit',1)) {
	$id = getmaxmin('home_pic','picid');
	if($_G['gp_start'] == 0){
		$_G['gp_start'] = $id['min'];
	}
	$start = $_G['gp_start'];
	$end = $_G['gp_start'] + $rpp;
	$query = DB::query("SELECT picid,albumid,filepath FROM ".DB::table('home_pic')." WHERE picid >= $start AND picid <= $end AND albumid > 0");
	while($pic = DB::fetch($query)){
		$album = DB::result_first("SELECT albumid FROM ".DB::table('home_album')." WHERE albumid = $pic[albumid]");
		if($album == 0){
			$rows ++;
			DB::delete('home_pic',"picid = $pic[picid]");
			DB::delete('home_picfield ',"picid = $pic[picid]");
			@unlink($_G['setting']['attachdir'].'/album/'.$pic['filepath']);
			@unlink($_G['setting']['attachdir'].'/album/'.$pic['filepath'].'.thumb.jpg');
		}
	}
	$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&start=$end&rows=$rows&clearpicsubmit=yes&rpp=$rpp&pmod=maintain&cp=aboutdb";
	if($end <= $id['max']+1){
		cpmsg("$lang[counter_forum]: ".cplang('counter_processing', array('current' => $start, 'next' => $end)), $nextlink, 'loading');	
	} else {
		$inc = $id['max']+1;
		DB::query("ALTER TABLE ".DB::table('home_pic')." AUTO_INCREMENT = $inc");
		cpmsg('tools:success',"action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb",'succeed');	
	}
} elseif(submitcheck('repairmfsubmit',1)) {
	$id = getmaxmin('common_member ','uid');
	if($_G['gp_start'] == 0){
		$_G['gp_start'] = $id['min'];
	}
	$start = $_G['gp_start'];
	$end = $_G['gp_start'] + $rpp;
	$query = DB::query("SELECT uid FROM ".DB::table('common_member')." WHERE uid >= $start AND uid <= $end");
	$field = array('statusid' => 'common_member_status',
			'profileid' => 'common_member_profile',
			'forumuid' => 'common_member_field_forum',
			'homeuid' => 'common_member_field_home',
			'countid' => 'common_member_count');
	while($member = DB::fetch($query)){
		foreach($field as $key => $value){
			$$key = DB::result_first("SELECT uid FROM ".DB::table($value)." WHERE uid = $member[uid]");
			if(!$$key){
				if($key == 'forumuid') {
					DB::insert('common_member_field_forum',array('uid' => $member['uid'],'customshow' => '26'));	
				} else {
					DB::insert($value,array('uid' => $member['uid']));	
				}
			}
		}
	}
	$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&start=$end&rows=$rows&repairmfsubmit=yes&rpp=$rpp&pmod=maintain&cp=aboutdb";
	if($end <= $id['max']+1){
		cpmsg("$lang[counter_forum]: ".cplang('counter_processing', array('current' => $start, 'next' => $end)), $nextlink, 'loading');	
	} else {
		cpmsg('tools:success',"action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb",'succeed');	
	}
} elseif(submitcheck('replacetidsubmit',1)) {
	
	$percent = $_G['gp_percent'] ? $_G['gp_percent'] : 2;
	if($percent == '10') {
		$auto = $maxtid + 1;
		DB::query("ALTER TABLE ".DB::table('forum_thread')." AUTO_INCREMENT = $auto");
		$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do";
		cpmsg($toolslang['replacesuccess'],$nextlink,'succeed');	
	}
	$eightpre = floor(($maxtid - $mintid - $count)*$percent/10);
	$pretid = DB::result_first("SELECT tid FROM ".DB::table('forum_thread')." WHERE tid < $eightpre ORDER BY tid DESC LIMIT 1");
	$pretid = $pretid ? $pretid : 0;
	$oldcount = DB::result_first("SELECT count(tid) FROM ".DB::table('forum_thread')." WHERE tid > $pretid AND tid < $eightpre");
	//echo "SELECT count(tid) FROM ".DB::table('forum_thread')." WHERE tid > $pretid AND tid < $eightpre".'<br/>';
	$differ = $eightpre - 1 - $pretid;
	//echo $differ;
	if(($differ > 0) && ($oldcount == 0)) {
		//echo "UPDATE ".DB::table('forum_thread')." SET tid = tid - $differ WHERE tid >= $eightpre";
		DB::query("UPDATE ".DB::table('forum_thread')." SET tid = tid - $differ WHERE tid >= $eightpre ORDER BY tid");
		$tablelist = array('forum_attachment','forum_activity','forum_activityapply','forum_attachmentfield','forum_debate',
				'forum_debatepost','forum_debatepost','forum_forumrecommend','forum_memberrecommend','forum_poll','forum_polloption','forum_postcomment',
				'forum_postlog','forum_postposition','forum_relatedthread','forum_rsscache','forum_threadlog','forum_threadmod','forum_trade','forum_tradelog','forum_typeoptionvar');
		$tablelist = array_merge($allposttalbe,$tablelist);
		foreach($tablelist as $value){
			DB::query("UPDATE ".DB::table($value)." SET tid = tid - $differ WHERE tid >= $eightpre ORDER BY tid");
		}		
		$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&percent=$percent&replacetidsubmit=yes&pmod=maintain&cp=aboutdb";
		cpmsg($toolslang['nowreplace'],$nextlink,'loading',array('percent' => ($percent - 1)));
	}
	if($differ == 0) {
		$nextpercent = $percent + 1;
		$lastpercent = $percent - 1;
		$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&percent=$nextpercent&replacetidsubmit=yes&pmod=maintain&cp=aboutdb";
		cpmsg($toolslang['nowreplace'],$nextlink,'loading',array('percent' => $percent));
	}
} elseif(submitcheck('replacepidsubmit',1)) {
	
	$percent = $_G['gp_percent'] ? $_G['gp_percent'] : 2;
	if($percent == '10') {
		$auto = $maxpid + 1;
		DB::query("ALTER TABLE ".$posttable['p']." AUTO_INCREMENT = $auto");
		$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&pmod=maintain&cp=aboutdb";
		cpmsg($toolslang['replacesuccess'],$nextlink,'succeed');	
	}
	
	$eightpre = floor(($maxpid - $minpid - $countpid)*$percent/10);
	
	$prepid = DB::result_first("SELECT pid FROM ".$posttable['p']." WHERE pid < $eightpre ORDER BY pid DESC LIMIT 1");
	$prepid = $prepid ? $prepid : 0;
	$oldcount = DB::result_first("SELECT count(pid) FROM ".$posttable['p']." WHERE pid > $prepid AND pid < $eightpre");
	$oldcount = $oldcount ? $oldcount : 0;
	$differ = $eightpre - 1 - $prepid;
	//echo "SELECT count(pid) FROM ".$posttable['p']." WHERE pid > $prepid AND pid < $eightpre";
	if(($differ > 0) && ($oldcount == 0)) {
		DB::query("UPDATE ".$posttable['p']." SET pid = pid - $differ WHERE pid >= $eightpre ORDER BY pid");
		$tablelist = array('forum_attachment','forum_attachmentfield','forum_debatepost','forum_postcomment','forum_postlog','forum_postposition','forum_ratelog','forum_trade','forum_tradecomment','forum_tradelog',
					'forum_warning');
		foreach($tablelist as $value){
			DB::query("UPDATE ".DB::table($value)." SET pid = pid - $differ WHERE pid >= $eightpre ORDER BY pid");
		}
		$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&percent=$percent&replacepidsubmit=yes&pmod=maintain&cp=aboutdb";
		cpmsg($toolslang['nowreplace'],$nextlink,'loading',array('percent' => ($percent - 1)));
	}
	if($differ == 0) {
		$nextpercent = $percent + 1;
		$lastpercent = $percent - 1;
		$nextlink = "action=plugins&identifier=tools&operation=$operation&do=$do&percent=$nextpercent&replacepidsubmit=yes&pmod=maintain&cp=aboutdb";
		cpmsg($toolslang['nowreplace'],$nextlink,'loading',array('percent' => $percent));
	}
	
} elseif(submitcheck('clearmoderetersumit',1)) {
	$tids = array();       //主题ID
	$pids = array();      //回复ID
	$blogids = array();  //日志ID
	$doids = array();   //记录ID
	$picids = array();
	$sids = array();
	$cids = array();
	$aids = array();
	$acids = array();

	$query = DB::query("SELECT tid FROM ".DB::table('forum_thread')." WHERE displayorder = -2");
	while($data = DB::fetch($query)){
		$tids[] = $data['tid'];	
	}
	
	$query = DB::query("SELECT pid FROM ".DB::table('forum_post')." WHERE invisible = -2");
	while($data = DB::fetch($query)){
		$pids[] = $data['pid'];	
	}
	
	$query = DB::query("SELECT blogid FROM ".DB::table('home_blog')." WHERE status = 1");
	while($data = DB::fetch($query)){
	
		$blogids[] = $data['blogid'];	
	}
	
	$query = DB::query("SELECT doid FROM ".DB::table('home_doing')." WHERE status = 1");
	while($data = DB::fetch($query)){
		$doids[] = $data['doid'];	
	}
	
	$query = DB::query("SELECT picid FROM ".DB::table('home_pic')." WHERE status = 1");
	while($data = DB::fetch($query)){
		$picids[] = $data['picid'];	
	}
	
	$query = DB::query("SELECT sid FROM ".DB::table('home_share')." WHERE status = 1");
	while($data = DB::fetch($query)){
		$sids[] = $data['sid'];	
	}

	$query = DB::query("SELECT cid FROM ".DB::table('home_comment')." WHERE status = 1");
	while($data = DB::fetch($query)){
		$cids[] = $data['cid'];	
	}
	$query = DB::query("SELECT aid FROM ".DB::table('portal_article_title')." WHERE status = 1");
	while($data = DB::fetch($query)){
		$aids[] = $data['aid'];	
	}
	$query = DB::query("SELECT cid FROM ".DB::table('portal_comment')." WHERE status = 1");
	while($data = DB::fetch($query)){
		$acids[] = $data['cid'];	
	}
	
	//处理
	if(count($tids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'tid' and id NOT IN (".implode(',',$tids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'tid'");
	}
	
	if(count($pids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'postid' and id NOT IN (".implode(',',$pids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'postid'");
	}
	//update 20110705
	if(count($pids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'pid' and id NOT IN (".implode(',',$pids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'pid'");
	}
	
	if(count($blogids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'blogid' and id NOT IN (".implode(',',$blogids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'blogid'");
	}
	if(count($doids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'doid' and id NOT IN (".implode(',',$doids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'doid'");
	}
	if(count($picids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'picid' and id NOT IN (".implode(',',$picids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'picid'");
	}
	if(count($sids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'sid' and id NOT IN (".implode(',',$sids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'sid'");
	}
	if(count($cids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype IN ('uid_cid','blogid_cid','picid_cid') and id NOT IN (".implode(',',$cids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype IN ('uid_cid','blogid_cid','picid_cid')");
	}
	if(count($aids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'aid' and id NOT IN (".implode(',',$aids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype = 'aid'");
	}
	if(count($acids)){
		$query = "DELETE FROM ".DB::table('common_moderate')." WHERE idtype IN ('aid_cid','topicid_cid') and id NOT IN (".implode(',',$acids).")";
		DB::query($query);
	} else {
		DB::query("DELETE FROM ".DB::table('common_moderate')." WHERE idtype IN ('aid_cid','topicid_cid')");
	}
	
	cpmsg($toolslang['clearmodereter_suc'],dreferer(),'succeed');
}
//需要清理数据库冗余的列表
$dbtablearray = array(
	'clearpost' => 'pid',
	'clearthread' => 'tid',
	'clearattachment' => 'aid',
	'clearmembers' => 'uid',
	'clearalbum' => 'albumid',
	'clearpic' => 'picid',
	);


showformheader("plugins&cp=aboutdb&pmod=maintain&operation=$operation&do=$do",'submit');
showtableheader($toolslang['cleardb']);
	showtablerow('', array('class="td21"'), array(
		$toolslang['jump'],
		'<input type="text" class="txt" name="rpp" value="1000" />',
	));

foreach($dbtablearray as $key => $value){
	showtablerow('', array('class="td21"'), array(
		"$toolslang[$key]",
		'<input type="submit" class="btn" name="'.$key.'submit" value="'.$lang['submit'].'" />'
	));
}	
showtablefooter();
showtableheader($toolslang['repairmf']);
	showtablerow('', array('class="td21"'), array(
		"$toolslang[repairmf]",
		'<input type="submit" class="btn" name="repairmfsubmit" value="'.$lang['submit'].'" />'
	));
showtablefooter();
/**
 * if($xver >= 2){
 * 	showtableheader($toolslang['clearmodereter']);
 * 		showtablerow('', array('class="td21"'), array(
 * 			"$toolslang[clearmodereter]",
 * 			'<input type="submit" class="btn" name="clearmoderetersumit" value="'.$lang['submit'].'" />'
 * 		));
 * 	showtablefooter();
 * }
 */

showformfooter();
?>