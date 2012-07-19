<?php

	$curprg = basename(__FILE__);
	$table_source = $db_source->tablepre;
	$table_target = $db_target->tablepre;
	error_reporting(0);
	@set_magic_quotes_runtime(0);

	$start = getgpc('start') ? getgpc('start') : 0;
	$limit = 1000;
	$next = 0;

		$query = $db_target->query("SELECT * FROM ".$table_target."ucenter_pm_lists WHERE plid>'$start' ORDER BY plid LIMIT $limit");
		while($data = $db_target->fetch_array($query)) {
			
			$next = $data['plid'];
			$users = explode('_', $data['min_max']);
			$pmsarr = $db_source->fetch_first("SELECT * FROM ".$table_source."pms WHERE msgfromid IN ('$users[0]','$users[1]') AND msgtoid IN ('$users[0]', '$users[1]') ORDER BY dateline DESC LIMIT 1");
			$pmsarr['msgfrom'] = addslashes($pmsarr['msgfrom']);
			$pmsarr['subject'] = addslashes($pmsarr['subject']);
			$pmsarr['message'] = addslashes($pmsarr['message']);
			if($pmsarr['subject'] && strcmp($pmsarr['subject'], $pmsarr['message'])) {
				$pmsarr['message'] = $pmsarr['subject']."\r\n".$pmsarr['message'];
			}
			if($users[0] == $data['authorid']) {
				$touid = $users[1];
			} else {
				$touid = $users[0];
			}
			
			$lastsummary = removecode(trim($pmsarr['message']), 150);
			$lastmessage = array('lastauthorid' => $pmsarr['msgfromid'], 'lastauthor' => $pmsarr['msgfrom'], 'lastsummary' => $lastsummary);
			$lastmessage = addslashes(serialize($lastmessage));
			$db_target->query("UPDATE ".$table_target."ucenter_pm_lists SET lastmessage='$lastmessage' WHERE plid='$data[plid]'");
			$db_target->query("UPDATE ".$table_target."ucenter_pm_members SET lastdateline='$pmsarr[dateline]' WHERE plid='$data[plid]'");

			if($count = $db_target->result_first("SELECT COUNT(*) FROM ".$table_target.getposttablename($data['plid'])." WHERE plid='$data[plid]' AND delstatus IN (0, 1)")) {
				$db_target->query("UPDATE ".$table_target."ucenter_pm_members SET pmnum='$count' WHERE plid='$data[plid]' AND uid='$touid'");
			} else {
				$db_target->query("DELETE FROM ".$table_target."ucenter_pm_members WHERE plid='$data[plid]' AND uid='$touid'");
			}
			if($count = $db_target->result_first("SELECT COUNT(*) FROM ".$table_target.getposttablename($data['plid'])." WHERE plid='$data[plid]' AND delstatus IN (0, 2)")) {
				$db_target->query("UPDATE ".$table_target."ucenter_pm_members SET pmnum='$count' WHERE plid='$data[plid]' AND uid='$data[authorid]'");
			} else {
				$db_target->query("DELETE FROM ".$table_target."ucenter_pm_members WHERE plid='$data[plid]' AND uid='$data[authorid]'");
			}
		}
	
	if($next) {
	
	showmessage("正在处理短消息表 "." $start 至 ".($start+$limit)." 行", "index.php?a=$action&source=$source&prg=$curprg&start=".($start+$limit));
	}

function removecode($str, $length) {
		$uccode = new uccode();
	$str = $uccode->complie($str);
	return trim(cutstr(strip_tags($str), $length));
}

function getposttablename($plid) {
	$id = substr((string)$plid, -1, 1);
	return 'ucenter_pm_messages_'.$id;
}

?>