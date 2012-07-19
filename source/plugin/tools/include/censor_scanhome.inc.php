<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: censor_scanhome.inc.php 80 2012-04-16 10:07:05Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied'); 

if(submitcheck('blogscansubmit',1) || submitcheck('commentscansubmit',1) || submitcheck('doingscansubmit',1) || submitcheck('docommentsubmit',1)) {
	$rpp = '500';
	$convertedrows = isset($_GET['convertedrows']) ? $_GET['convertedrows'] : 0;
	$start = isset($_GET['start']) && $_GET['start'] > 0 ? $_GET['start'] : 0;

	$end = $start + $rpp - 1;

	
	$wordstart = isset($_GET['wordstart']) && $_GET['wordstart'] > 0 ? $_GET['wordstart'] : 0;
	$wordend =  $scaned ? ($scaned + $rpp -1) : ($wordstart + $rpp - 1);
	$maxid = isset($_GET['maxid']) ? $_GET['maxid'] : 0;
	$wordmaxid = isset($_GET['wordmaxid']) ? $_GET['wordmaxid'] : 0;
	
	$array_find = $array_replace = $array_findmod = $array_findbanned = array();
	if($wordmaxid == 0) {
		$result = DB::fetch_first("SELECT MIN(id) AS wordminid, MAX(id) AS wordmaxid FROM ".DB::table('common_word'));
		$wordstart = $result['wordminid'] ? $result['wordminid'] - 1 : 0;
		$wordmaxid = $result['wordmaxid'];
	}

	
	$wordextsql = "where id >= $wordstart AND id <= $wordend";
	$query = DB::query("SELECT find,replacement from ".DB::table('common_word')." $wordextsql");//获得现有规则{BANNED}放回收站 {MOD}放进审核列表
	while($row = DB::fetch($query)) {
		$find = preg_quote($row['find'], '/');
		$replacement = $row['replacement'];
		if($replacement == '{BANNED}') {
			$array_findbanned[] = $find;
		} elseif($replacement == '{MOD}') {
			$array_findmod[] = $find;
		} else {
			$array_find[] = $find;
			$array_replace[] = $replacement;
		}
	}

	$array_find = topattern_array($array_find);
	$array_findmod = topattern_array($array_findmod);
	$array_findbanned = topattern_array($array_findbanned);	
	
	if($_GET['blogscansubmit']){
		$table = array('home_blog','home_blogfield','blogid','message');
		$action = 'blogscansubmit';
		$type = 'blog';
	} elseif($_GET['commentscansubmit']) {
		$table = array('home_comment','','cid','message');
		$action = 'commentscansubmit';
		$type = 'comment';
	} elseif($_GET['doingscansubmit']) {
		$table = array('home_doing','','doid','message');
		$action = 'doingscansubmit';
		$type = 'doing';
	} elseif($_GET['docommentsubmit']) {
		$table = array('home_docomment','','id','message');
		$action = 'docommentsubmit';
		$type = 'docomment';
	}
	if($start == 0){
		DB::query("DELETE FROM ".DB::table('tools_censorhome')." WHERE type = '$type'");	
	}
	list($table1,$table2,$id,$content) = $table;
	$table1 = DB::table($table1);
	$table2 = $table2 ? DB::table($table2) : $table2;
	if($maxid == 0) {
		$result = DB::fetch_first("SELECT MIN($id) AS minid, MAX($id) AS maxid FROM ".$table1);
		$start = $result['minid'] ? $result['minid'] - 1 : 0;
		$maxid = $result['maxid'];
	}
	if($table2){
		$sql = "SELECT {$table1}.{$id},{$table2}.{$content} FROM ".$table1.",".$table2." WHERE {$table1}.{$id}={$table2}.{$id} AND {$table1}.{$id} >= $start and {$table1}.{$id} <= $end"; 	
	} else {
		$sql = "SELECT {$id},{$content} FROM ".$table1." WHERE {$id} >= $start and {$id} <= $end"; 	
	}
	$query = DB::query($sql);
	
	while($row =  DB::fetch($query)) {
		
		$id2 = $row[$id];
		$content2 = $row[$content];

		$displayorder = 0;//  -2 MOD -1 Banned
		if(count($array_findmod) > 0) {
			foreach($array_findmod as $value) {
				if(preg_match($value,$content2)) {
					$displayorder = '-2';
					break;
				}
			}
		}
		if(count($array_findbanned) > 0) {
			foreach($array_findbanned as $value) {
				if(preg_match($value,$content2)) {
					$displayorder = '-1';
					break;
				}
			}
		}
		
		if($displayorder < 0) {
			if(in_array($type,array('blog','comment'))){
				DB::query("REPLACE INTO ".DB::table('tools_censorhome')." (`itemid`, `type`) VALUES ('$id2','$type')");
			} else {
				DB::query("DELETE FROM ".$table1." WHERE $id = $id2");
			}
			$convertedrows ++;
		}

		$content2 = preg_replace($array_find,$array_replace,addslashes($content2));
		if($content2 != addslashes($row[$content])) {
			$table = $table2 ? $table2 : $table1;
			if(DB::query("UPDATE ".$table." SET {$content} = '$content2' WHERE {$id} = $id2")) {
				$convertedrows ++;
			}
		}
		$converted = 1;
	}

	if($converted  || $end < $maxid) {
		$nextlink = "action=plugins&cp=censor_scanhome&pmod=safe&operation=$operation&do=$do&identifier=$identifier&start=$end&maxid=$maxid&convertedrows=$convertedrows&wordstart=$wordstart&wordmaxid=$wordmaxid&{$action}=yes";
		cpmsg($toolslang['censor_homescanstart'], $nextlink, 'loading', array('start' => $start,'end' => $end,'wordstart' => $wordstart,'wordend' => $wordend,'posttableid' => $posttableid));
	} elseif($wordend < $wordmaxid) {
		$nextlink = "action=plugins&cp=censor_scanhome&pmod=safe&operation=$operation&do=$do&identifier=$identifier&start=0&maxid=$maxid&convertedrows=$convertedrows&wordstart=$wordend&wordmaxid=$wordmaxid&{$action}=yes";
		cpmsg($toolslang['censor_homescanstart'], $nextlink, 'loading',array('start' => $start,'end' => $end,'wordstart' => $wordstart,'wordend' => $wordend,'posttableid' => $posttableid));
	} elseif($end >= $maxid || $wordend >= $wordmaxid) {
		cpmsg($toolslang['censor_scanresult'], "action=plugins&cp=censor_scanhome&pmod=safe&operation=$operation&do=$do&identifier=$identifier", 'succeed',array('count' => $convertedrows));
	}
}

showformheader("plugins&cp=censor_scanhome&pmod=safe&operation=$operation&do=$do&identifier=$identifier");
showtableheaders($toolslang['censor_homeinfo'],'censor');
$totalblogcount = DB::result_first("SELECT count(blogid) FROM ".DB::table('home_blog'));
$totalcommontcount = DB::result_first("SELECT count(cid) FROM ".DB::table('home_comment'));
$totaldocommentcount = DB::result_first("SELECT count(id) FROM ".DB::table('home_docomment'));
$totaldoingcount = DB::result_first("SELECT count(doid) FROM ".DB::table('home_doing'));

showtablerow('', array('class="td21"'), array($toolslang['censor_blogcount'],$totalblogcount,"<input type='submit' value=$toolslang[censor_beginscan] title=$toolslang[censor_beginscan] name='blogscansubmit' class='btn'>"));
showtablerow('', array('class="td21"'), array($toolslang['censor_commontcount'],$totalcommontcount,"<input type='submit' value=$toolslang[censor_beginscan] title=$toolslang[censor_beginscan] name='commentscansubmit' class='btn'>"));	
showtablerow('', array('class="td21"'), array($toolslang['censor_doingcount'],$totaldoingcount,"<input type='submit' value=$toolslang[censor_beginscan] title=$toolslang[censor_beginscan] name='doingscansubmit' class='btn'>"));
showtablerow('', array('class="td21"'), array($toolslang['censor_docommentcount'],$totaldocommentcount,"<input type='submit' value=$toolslang[censor_beginscan] title=$toolslang[censor_beginscan] name='docommentsubmit' class='btn'>"));
showtablefooter();
$count = DB::result_first("SELECT COUNT(*) FROM ".DB::table('tools_censorhome')." WHERE type IN ('blog','comment')");
$multipage = multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&cp=censor_scanhome&pmod=safe&operation=$operation&do=$do&identifier=$identifier&a=scanhome");

$query = DB::query("SELECT * FROM ".DB::table('tools_censorhome')." WHERE type IN ('blog','comment') LIMIT $startlimit, $ppp");
showtableheader($toolslang['censor_homemod'],'censor');
showtablerow('', array('class="td21"'), array($toolslang[censor_hometype],$toolslang[censor_homelink]));
while($data = DB::fetch($query)){
	$datas['type'] = convtype($data['type'],$toolslang);	
	if($data['type'] == 'blog'){
		$datas['links'] = "<a href=$_G[siteurl]home.php?mod=space&do=blog&id={$data[itemid]} target=_blank>$_G[siteurl]home.php?mod=space&do=blog&id={$data[itemid]}</a>";
	} elseif($data['type'] == 'comment') {
		$datas['links'] = "<a href=$_G[siteurl]home.php?mod=spacecp&ac=comment&op=edit&cid={$data[itemid]} target=_blank>$_G[siteurl]home.php?mod=spacecp&ac=comment&op=edit&cid={$data[itemid]}</a>";
	}
	showtablerow('', '', $datas);
}
showtablerow('', '', array($multipage,''));
showtablefooter();

?>