<?php

$curprg = basename(__FILE__);
$table_source = $db_source->tablepre;
$table_target = $db_target->tablepre;
error_reporting(0);
@set_magic_quotes_runtime(0);

	$total = getgpc('total');
	$start = getgpc('start') ? getgpc('start') : 0;
	$limit =5000;
	$next = 0;
	$total = $db_source->result_first("SELECT MAX(pmid) FROM ".$table_source."pms");
	
	if($start == 0){
		$db_target->query("truncate table ".$table_target."ucenter_pm_indexes");
		$db_target->query("truncate table ".$table_target."ucenter_pm_members");
		for($i=0; $i<10; $i++) {
			$db_target->query("truncate table ".$table_target."ucenter_pm_messages_$i");
		}
		$db_target->query("truncate table ".$table_target."ucenter_pm_lists");
	}
	$query = $db_source->query("SELECT * FROM ".$table_source."pms WHERE pmid>'$start' ORDER BY pmid LIMIT $limit");
		while($data = $db_source->fetch_array($query)) {
		
			$next = $data['pmid'];
			if(!$data['msgfromid'] || !$data['msgtoid'] || $data['msgfromid'] == $data['msgtoid']) {
				continue;
			}
			$plid = $founderid = 0;
			$data['msgfrom'] = addslashes($data['msgfrom']);
			$data['subject'] = addslashes($data['subject']);
			$data['message'] = addslashes($data['message']);
			$relationship = relationship($data['msgfromid'], $data['msgtoid']);
			$querythread = $db_target->query("SELECT plid, authorid FROM ".$table_target."ucenter_pm_lists WHERE min_max='$relationship'");
			if($thread = $db_target->fetch_array($querythread)) {
				$plid = $thread['plid'];
				$founderid = $thread['authorid'];				
			}
			
			if(!$plid) {
				$s = $db_target->query("INSERT INTO ".$table_target."ucenter_pm_lists(authorid, pmtype, subject, members, min_max, dateline) VALUES('$data[msgfromid]', 1, '', 2, '$relationship', '$data[dateline]')");
				$plid = $db_target->insert_id();//echo 123424;exit();
				$db_target->query("INSERT INTO ".$table_target."ucenter_pm_members(plid, uid, isnew, lastupdate) VALUES('$plid', '$data[msgfromid]', '$data[new]', 0)");
				$db_target->query("INSERT INTO ".$table_target."ucenter_pm_members(plid, uid, isnew, lastupdate) VALUES('$plid', '$data[msgtoid]', '$data[new]', 0)");
			}
			$db_target->query("INSERT INTO ".$table_target."ucenter_pm_indexes(plid) VALUES('$plid')");
			$pmid = $db_target->insert_id();
			if($founderid == $data['msgfromid']) {
				$delstatus = $data['delstatus'];
			} else {
				$delstatus = ($data['delstatus'] == 1) ? 2 : ($data['delstatus'] == 2 ? 1 : 0);
			}
			if($data['subject'] && strcmp($data['subject'], $data['message'])) {
				$data['message'] = $data['subject']."\r\n".$data['message'];
			}
			
			$db_target->query("INSERT INTO ".$table_target.getposttablename($plid)."(pmid, plid, authorid, message, delstatus, dateline) VALUES('$pmid', '$plid', '$data[msgfromid]', '".$data['message']."', '$delstatus', '$data[dateline]')");
		}
	
		if($next) {
		
			showmessage("继续转换数据表 ".$table_source."pms"." $start 至 ".($start+$limit)." 行/".$total, "index.php?a=$action&source=$source&prg=$curprg&start=".($start+$limit));
			
		} 

function relationship($fromuid, $touid) {
	if($fromuid < $touid) {
		return $fromuid.'_'.$touid;
	} elseif($fromuid > $touid) {
		return $touid.'_'.$fromuid;
	} else {
		return '';
	}
}

function getposttablename($plid) {
	$id = substr((string)$plid, -1, 1);
	return 'ucenter_pm_messages_'.$id;
}

?>