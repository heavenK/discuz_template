<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: aboutucenter.inc.php 79 2012-04-16 10:06:12Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');
$mod = in_array($_GET['mod'],array('synusername','clrnotice','synuid','clrfeed','pm','avator')) ? $_GET['mod'] : 'clrnotice';
@include_once(DISCUZ_ROOT.'./config/config_ucenter.php');
if(!defined('UC_DBUSER')) {
	cpmsg($toolslang['uc_config_no_exist'],'','error');
} elseif(UC_DBHOST != $_G[config][db][1][dbhost]) {
	cpmsg($toolslang['uc_config_no_db'],'','error');
}

$ppp = 100;
$page = max(1, intval($_GET['page']));
$startlimit = ($page - 1) * $ppp;

if(in_array($mod,array('synusername','clrnotice','synuid','clrfeed','avator'))) {
	showtipss($toolslang[$mod.'_tip']);
}

$step=intval($_GET['step']);

if($_GET[''.$mod.'_submit'] || $step>0){
	if($mod == 'synusername'){
		$step = intval($_GET['step']);
		$perpage = 1000;
		$count = isset($_GET['count']) ? $_GET['count'] : DB::result_first('SELECT count(uid) FROM '.UC_DBTABLEPRE.'members');

		$query = DB::query('SELECT uid,username FROM '.UC_DBTABLEPRE.'members limit '.($step*$perpage).','.$perpage);
		while($row = DB::fetch($query)){
			//print_r($row);exit;
			//DB::update('common_member',array('username' => daddslashes($row['username'])),'uid='.$row['uid']);
			//DB::update('forum_thread',array('author' => daddslashes($row['username'])),'authorid='.$row['uid']);
			$tables = array(
				'common_block' => array('id' => 'uid', 'name' => 'username'),
				'common_invite' => array('id' => 'fuid', 'name' => 'fusername'),
				'common_member' => array('id' => 'uid', 'name' => 'username'),
				'common_member_security' => array('id' => 'uid', 'name' => 'username'),
				'common_mytask' => array('id' => 'uid', 'name' => 'username'),
				'common_report' => array('id' => 'uid', 'name' => 'username'),
	
				'forum_thread' => array('id' => 'authorid', 'name' => 'author'),
				'forum_post' => array('id' => 'authorid', 'name' => 'author'),
				'forum_activityapply' => array('id' => 'uid', 'name' => 'username'),
				'forum_groupuser' => array('id' => 'uid', 'name' => 'username'),
				'forum_pollvoter' => array('id' => 'uid', 'name' => 'username'),
				'forum_postcomment' => array('id' => 'authorid', 'name' => 'author'),
				'forum_ratelog' => array('id' => 'uid', 'name' => 'username'),
	
				'home_album' => array('id' => 'uid', 'name' => 'username'),
				'home_blog' => array('id' => 'uid', 'name' => 'username'),
				'home_clickuser' => array('id' => 'uid', 'name' => 'username'),
				'home_docomment' => array('id' => 'uid', 'name' => 'username'),
				'home_doing' => array('id' => 'uid', 'name' => 'username'),
				'home_feed' => array('id' => 'uid', 'name' => 'username'),
				'home_feed_app' => array('id' => 'uid', 'name' => 'username'),
				'home_friend' => array('id' => 'fuid', 'name' => 'fusername'),
				'home_friend_request' => array('id' => 'fuid', 'name' => 'fusername'),
				'home_notification' => array('id' => 'authorid', 'name' => 'author'),
				'home_pic' => array('id' => 'uid', 'name' => 'username'),
				'home_poke' => array('id' => 'fromuid', 'name' => 'fromusername'),
				'home_share' => array('id' => 'uid', 'name' => 'username'),
				'home_show' => array('id' => 'uid', 'name' => 'username'),
				'home_specialuser' => array('id' => 'uid', 'name' => 'username'),
				'home_visitor' => array('id' => 'vuid', 'name' => 'vusername'),
	
				'portal_article_title' => array('id' => 'uid', 'name' => 'username'),
				'portal_comment' => array('id' => 'uid', 'name' => 'username'),
				'portal_topic' => array('id' => 'uid', 'name' => 'username'),
				'portal_topic_pic' => array('id' => 'uid', 'name' => 'username'),
			);
	
			foreach($tables as $table => $conf) {
				DB::query("UPDATE ".DB::table($table)." SET `$conf[name]`='".daddslashes($row['username'])."' WHERE `$conf[id]`='$row[uid]'");
			}
			$i++;
		}
		if(($step*$perpage) <= $count){
			cpmsg($step*$perpage,"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&count=".$count.'&step='.($step+1).'&mod='.$mod,'loading');
		}else{
			cpmsg($toolslang['success'],"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=".$mod,'succeed');
		}
	} elseif($mod == 'synuid') {
		$frow=DB::result_first('SELECT MAX(uid) muid FROM '.DB::table('common_member'));
		$urow=DB::result_first('SELECT MAX(uid) muid FROM '.UC_DBTABLEPRE.'members');

		if($frow > $urow){
			$frow = $frow +1;
			DB::query("ALTER TABLE ".UC_DBTABLEPRE."members AUTO_INCREMENT = '$frow'");
		}
		cpmsg($toolslang['success'],"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=".$mod,'succeed');
	} elseif($mod == 'clrnotice') {
		DB::query('delete from '.UC_DBTABLEPRE.'notelist');
		cpmsg($toolslang['success'],"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=".$mod,'succeed');
	} elseif($mod == 'clrfeed') {
		$time = $_G['timestamp'] - 3*30*24*3600;
		DB::query("delete from ".UC_DBTABLEPRE."feeds WHERE dateline < $time");
		cpmsg($toolslang['success'],"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=".$mod,'succeed');
	} elseif($mod == 'avator') {
		$start = $_GET['start'] ? $_GET['start'] : 0;
		$limit = 500;
		if($_GET['count']){
			$max = $_GET['count'];	
		} else {
			$max = DB::fetch_first("SELECT uid FROM ".DB::table('common_member')." ORDER BY uid DESC");
			$max = $max['uid'];
		}

		if($start >= $max){
			cpmsg($toolslang['uc_avatar_done'],"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=$mod",'succeed');	
		}
		$query = DB::query("SELECT uid FROM ".DB::table('common_member')." WHERE avatarstatus = '0' AND uid > '$start' LIMIT $limit");
		while($userinfo = DB::fetch($query)){
			$userinfos[] = $userinfo['uid'];	
		}
		foreach($userinfos as $value){
			loaducenter();
			$hasavatar = uc_check_avatar($value);
			DB::query("UPDATE ".DB::table('common_member')." SET avatarstatus = '$hasavatar' WHERE uid='$value'");
		}
		$nextstart = $start + $limit;
		cpmsg($toolslang['uc_avatar_jump'],"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=$mod&start=$nextstart&max=$max&avator_submit=yes",'loading',array('start' => $start,'limit' => $limit));
	}
} elseif(in_array($mod,array('synusername','clrnotice','synuid','clrfeed','avator'))) {
	showformheader("plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=".$mod);
	showtableheaders();
	showsubmit($mod.'_submit','submit');
	showtablefooter();
	showformfooter();
} elseif($mod == 'pm') {
	loaducenter();
	$uc_version = uc_check_version();
	if($uc_version[file] >='1.6' ){
		if($_GET['username']){
			$msgfromid = DB::result_first("SELECT uid FROM ".DB::table('common_member')." WHERE username = '$_GET[username]'");
		} else {
			$msgfromid = $_GET['msgfromid'] ? $_GET['msgfromid'] : '';	
		}
		
		
		if($_GET['clearpms']){
			$clearpms = DB::result_first("SELECT uid FROM ".DB::table('common_member')." WHERE username = '$_GET[clearpms]'");
		} else {
			$clearpms = $_GET['clearpms'] ? $_GET['clearpms'] : '';	
		}
		
		showformheader("plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm",'submit');
		showtableheaders($toolslang['uc_viewpm']);
		showtablerow('', array('class="td21"'), array(
			$toolslang['uc_viewusername'],
			'<input type="text" class="txt" name="username" value="'.dstripslashes($_GET['username']).'" /><input type="submit" class="btn" name="submit" value="'.$lang['submit'].'" />'
		));
		showtablerow('', array('class="td21"'), array(
			$toolslang['uc_clearpm'],
			'<input type="text" class="txt" name="clearpms" value="" /><input type="submit" class="btn" name="submit" value="'.$lang['submit'].'" />'
		));
		showtablefooter();
		showformfooter();
		//清理短消息
		if($clearpms) {
			DB::query("DELETE FROM ".UC_DBTABLEPRE."pm_lists WHERE authorid = $clearpms");
			$query = DB::query("SELECT plid FROM ".UC_DBTABLEPRE."pm_lists");
			while($plid = DB::fetch($query)){
				$plids[] = $plid['plid'];
			}
			DB::query("DELETE FROM ".UC_DBTABLEPRE."pm_indexes WHERE plid NOT IN (".dimplode($plids).")");
			DB::query("DELETE FROM ".UC_DBTABLEPRE."pm_members WHERE uid = $clearpms");
			
			$rows = 0;
			for($i=0;$i<=9;$i++){
				DB::query("DELETE FROM ".UC_DBTABLEPRE."pm_messages_".$i." WHERE authorid = $clearpms");
				$rows += DB::affected_rows();
			}
			cpmsg($rows,"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm");
		}
		
		if($_GET['submit']){
			
			//echo "SELECT * FROM ".UC_DBTABLEPRE."pms $sqlplus GROUP BY dateline, msgtoid ORDER BY dateline DESC LIMIT $startlimit,$ppp";exit;
			if($_GET['uid'] && $_GET['touid']){
				$data = uc_pm_view($_GET['uid'],0,$_GET['touid'],5);
				//print_r($data);
				$data = array_reverse($data);
				showtableheaders(dstripslashes($_GET['username']).$toolslang['uc_pmhis']);
				showsubtitle(array($toolslang['uc_pmfrom'],$toolslang['uc_pmtoer'],$toolslang['uc_pmcontent'],$toolslang['uc_pmtime']));
				foreach($data as $key => $value){
					$showdata[1] =  DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid = $value[authorid]");
					$showdata[2] = DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid = $value[touid]");
					$showdata[3] =  $value['message'];
					$showdata[4] =  date('Y-m-d H:i',$value['dateline']);
					showtablerow('', array(), $showdata);
				}
				showtablerow('',array('class="td25"'),array('','','','',$multipage));
				showtablefooter();
			} else {
				$data = uc_pm_list($msgfromid,$page,$ppp,'inbox','privatepm','',100);
				$count = $data['count'];
				$multipage = multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm&username=$_GET[username]&submit=yes");

				$data = $data[data];
				//print_r($data);
				showtableheaders(dstripslashes($_GET['username']).$toolslang['uc_pmhli']);
				showsubtitle(array($toolslang['uc_pmusername'],$toolslang['uc_relausername'],$toolslang['uc_pmlastcontent'],$toolslang['uc_pmtime']));
				if(is_array($data)){
					foreach($data as $key => $value){
						$showdata[1] =  DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid = $value[uid]");
						$showdata[2] = $value['tousername'];
						$showdata[3] =  $value['message']."<a href=".ADMINSCRIPT."?action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm&uid=$value[uid]&touid=$value[touid]&submit=yes> (detail)</a>";
						$showdata[4] =  date('Y-m-d H:i',$value['dateline']);
						showtablerow('', array(), $showdata);
					}
				}

				showtablerow('',array('class="td25"'),array('','','',$multipage));
				showtablefooter();
			}
			

		}

	} else {
		if($_GET['msgttoname']){
			$msgtoid = DB::result_first("SELECT uid FROM ".DB::table('common_member')." WHERE username = '$_GET[msgttoname]'");
		} else {
			$msgtoid = $_GET['msgtoid'] ? $_GET['msgtoid'] : '';	
		}
		$sqlplus = "WHERE 1 ";
		$msgtoid && $username1 = DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid = $msgtoid");
		$msgtoid && $sqlplus = "WHERE msgfrom != '$username1' ";
		if($_GET['username'] && $msgtoid){
			$sqlplus .= "AND msgfrom = '$_GET[username]' AND msgtoid = $msgtoid";
		} elseif($_GET['username']) {
			$fromuid = DB::result_first("SELECT uid FROM ".DB::table('common_member')." WHERE username = '$_GET[username]'");
			$sqlplus .= "AND msgfrom = '$_GET[username]' AND msgtoid != $fromuid";
		} elseif($msgtoid) {
			$tousername = DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid = $msgtoid");
			$sqlplus .= "AND msgtoid = $msgtoid";	
		} else {
			$sqlplus .= "";
		}
		
		if($_GET['clearpms']){
			$clearpms = DB::result_first("SELECT uid FROM ".DB::table('common_member')." WHERE username = '$_GET[clearpms]'");
		} else {
			$clearpms = $_GET['clearpms'] ? $_GET['clearpms'] : '';	
		}
		
		showformheader("plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm",'submit');
		showtableheaders($toolslang['uc_viewpm']);
		showtablerow('', array('class="td21"'), array(
			$toolslang['uc_viewsend'],
			'<input type="text" class="txt" name="username" value="'.dstripslashes($_GET['username']).'" /><input type="submit" class="btn" name="submit" value="'.$lang['submit'].'" />'
		));
		showtablerow('', array('class="td21"'), array(
			$toolslang['uc_viewto'],
			'<input type="text" class="txt" name="msgttoname" value="'.dstripslashes($_GET['msgttoname']).'" /><input type="submit" class="btn" name="submit" value="'.$lang['submit'].'" />'
		));
		showtablerow('', array('class="td21"'), array(
			$toolslang['uc_clearpm'],
			'<input type="text" class="txt" name="clearpms" value="" /><input type="submit" class="btn" name="submit" value="'.$lang['submit'].'" />'
		));
		showtablefooter();
		showformfooter();
		
		if($clearpms) {
			DB::query("DELETE FROM ".UC_DBTABLEPRE."pms WHERE msgfromid = $clearpms OR msgtoid = $clearpms");
			$rows = DB::affected_rows();
			cpmsg($rows,"action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm");
		}
		
		$count = DB::result_first("SELECT count(*) FROM ".UC_DBTABLEPRE."pms $sqlplus");	
		$multipage = multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm&username=$_GET[username]");
		//echo "SELECT * FROM ".UC_DBTABLEPRE."pms $sqlplus GROUP BY dateline, msgtoid ORDER BY dateline DESC LIMIT $startlimit,$ppp";exit;
		$pmss = DB::query("SELECT * FROM ".UC_DBTABLEPRE."pms $sqlplus AND related = 1 ORDER BY msgtoid DESC LIMIT $startlimit,$ppp");
		showtableheaders(dstripslashes($_GET['username']).$toolslang['uc_pmhis']);
		showsubtitle(array($toolslang['uc_pmfrom'],$toolslang['uc_pmtoer'],$toolslang['uc_pmcontent'],$toolslang['uc_pmtime']));
		while($data = DB::fetch($pmss)){
			//$showdata[] =  $data['pmid'];
			$showdata[1] =  "<a href=".ADMINSCRIPT."?action=plugins&pmod=maintain&cp=aboutucenter&operation=$operation&do=$do&mod=pm&username=$data[msgfrom]>".$data['msgfrom']."</a>";
			if(!$data['msgfrom']) {$showdata[1] = 'SYSTEM';}
			$showdata[2] = DB::result_first("SELECT username FROM ".DB::table('common_member')." WHERE uid = $data[msgtoid]");
			$showdata[3] =  $data['message'];
			$showdata[4] =  date('Y-m-d H:i',$data['dateline']);
			showtablerow('', array(), $showdata);
		}
		showtablerow('',array('class="td25"'),array('','','','',$multipage));
		showtablefooter();
	}
}	
?>