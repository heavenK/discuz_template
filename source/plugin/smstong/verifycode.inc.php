<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: verifycode.inc.php 18582 2010-06-25 16:01:10Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('NOROBOT', TRUE);

require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

loadcache('plugin');

if($_GET['action'] == 'checkmobile') {

	$mobile = trim($_GET['mobile']);
	
	if(!ismobile($mobile)) {
		showmessage('smstong:smstong_mobilereg_mobile_invalid', '', array(), array('handle' => false));
	}

	$result = array();
	$query = DB::query("SELECT * FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");

	if($member = DB::fetch($query)) {
		if(!empty($member['uid'])) {
			$result = $member;
		}
	}
	if(!empty($result)) {
		showmessage('smstong:smstong_mobilereg_mobile_exists', '', array(), array('handle' => false));
	}

} elseif($_GET['action'] == 'getregverifycode') {

	if($_GET['formhash'] != $_G['formhash']) {
		showmessage('submit_invalid', '', array(), array('handle' => false));
	}

	if($_G['cache']['plugin']['smstong']['openverifycode'] == 1) {
		require_once libfile('function/seccode');
		if(!$_G['uid'] && !check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
			showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
		}
	}

	$mobile = trim($_GET['mobile']);

	if($_G['cache']['plugin']['smstong']['mobilereg'] == 0) {
		showmessage('smstong:smstong_mobilereg_closed');
	}

	if(!ismobile($mobile)) {
		showmessage('smstong:smstong_mobilereg_mobile_invalid');
	}
	else {
		$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
		$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE status=1 AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
		
		if ($sended)
		{
			showmessage('smstong:smstong_mobilereg_mobile_sended', '', array('mobilegap' => $mobilegap));
		}
		else
		{
			$exists = DB::result_first("SELECT mobile FROM ".DB::table('common_member_profile')." WHERE mobile='".trim($mobile)."'");
			if($exists) {
				showmessage('smstong:smstong_mobilereg_mobile_exists');
			} else {
				$verifycode = rand(100000,999999);

				$verifycode = str_replace('1989','9819',$verifycode);
				$verifycode = str_replace('1259','9521',$verifycode);
				$verifycode = str_replace('12590','09521',$verifycode);
				$verifycode = str_replace('10086','68001',$verifycode);

				$content = $_G['cache']['plugin']['smstong']['mobileregmsg'];
				$rp = array('$mobile', '$verifycode');
				$sm = array($mobile, $verifycode);
				$content = str_replace($rp, $sm, $content);

				$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);
				
				if($ret === true)
				{
					$verifycode_data = array(
					'mobile' => $mobile,
					'getip' => $_G['clientip'],
					'verifycode' => $verifycode,
					'dateline' => TIMESTAMP,
					);
					DB::insert('common_verifycode', $verifycode_data);
					
					showmessage('smstong:smstong_mobilereg_getverifycode_succeed', '', array('mobile' => $mobile));
				}
				else
				{
					showmessage('smstong:smstong_mobilereg_getverifycode_failured', '', array('ret' => $ret));
				}
			}
		}
	}
} elseif($_GET['action'] == 'checkregverifycode') {

	$mobile = trim($_GET['mobile']);
	$verifycode = trim($_GET['verifycode']);

	if(!ismobile($mobile)) {
		showmessage('smstong:smstong_mobilereg_mobile_invalid');
	}

	if(!$verifycode) {
		showmessage('smstong:smstong_mobilereg_verifycode_empty', '', array(), array('handle' => false));
	}

	$result = array();
	$query = DB::query("SELECT * FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' and verifycode='$verifycode' and getip='$_G[clientip]' and status=1");

	if($verify = DB::fetch($query)) {
		if(!empty($verify['id']) && ($_G['timestamp'] < $verify['dateline']+$_G['cache']['plugin']['smstong']['periodofvalidity'])) {
			$result = $verify;
		}
	}
	if(empty($result)) {
		showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid', '', array(), array('handle' => false));
	}

} elseif($_GET['action'] == 'bindmobile') {

	if(submitcheck('bindmobilesubmit', 0, 0, 0)) {

		if($_GET['formhash'] != $_G['formhash']) {
			showmessage('submit_invalid', '', array(), array('handle' => false));
		}

		if($_G['cache']['plugin']['smstong']['openverifycode'] == 1) {
			require_once libfile('function/seccode');
			if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
				showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
			}
		}
		
		$mobile = trim($_GET['newmobile']);
		$verifycode = trim($_GET['verifycode']);

		if($_G['cache']['plugin']['smstong']['mobilebind'] == 0) {
			showmessage('smstong:smstong_mobilebind_closed');
		}

		if(empty($mobile)) {
			showmessage('smstong:smstong_mobilebind_mobile_empty');
		}
		
		if(!ismobile($mobile)) {
			showmessage('smstong:smstong_mobilereg_mobile_invalid');
		}
		elseif ($_POST['flag'] == "2") {

			$query = DB::query("SELECT mobile FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
			if($value = DB::fetch($query)) 
			{
				if(!empty($value['mobile']))
				{
					showmessage('smstong:smstong_mobilebind_mobile_exists');
				}
			}

			$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
			$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE (status=3 or status=4) AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
			
			if ($sended)
			{
				showmessage('smstong:smstong_mobilereg_mobile_sended', '', array('mobilegap' => $mobilegap));
			}
			else
			{
				$verifycode = rand(100000,999999);

				$verifycode = str_replace('1989','9819',$verifycode);
				$verifycode = str_replace('1259','9521',$verifycode);
				$verifycode = str_replace('12590','09521',$verifycode);
				$verifycode = str_replace('10086','68001',$verifycode);

				$content = $_G['cache']['plugin']['smstong']['mobilebindmsg'];
				$rp = array('$mobile', '$verifycode');
				$sm = array($mobile, $verifycode);
				$content = str_replace($rp, $sm, $content);

				$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);

				if($ret === true)
				{
					$verifycode_data = array(
					'mobile' => $mobile,
					'getip' => $_G['clientip'],
					'verifycode' => $verifycode,
					'dateline' => TIMESTAMP,
					'reguid' => $_G['uid'],
					'status' => 3,
					);
					DB::insert('common_verifycode', $verifycode_data);

					showmessage('smstong:smstong_mobilebind_sendsms_succeed');
				}
				else
				{
					showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
				}
			}
		}
		elseif ($_POST['flag'] == "1") {
			if(empty($verifycode)) {
				showmessage('smstong:smstong_mobilereg_verifycode_empty');
			}

			$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];
			$verify = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity");
				
			if (!$verify)
			{
				showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid');
			}
			else
			{
				DB::query("UPDATE ".DB::table('common_member_profile')." SET mobile='$mobile' WHERE uid=$_G[uid]");

				DB::query("UPDATE ".DB::table('common_verifycode')." SET reguid=$_G[uid],regdateline='$_G[timestamp]',status=4 WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity");

				DB::query("UPDATE ".DB::table('common_member')." SET mobilestatus=1 WHERE uid=$_G[uid]");

				$usergroup = DB::result_first("SELECT type FROM ".DB::table('common_usergroup')." WHERE groupid='$_G[groupid]'");
				$groupid = $_G['cache']['plugin']['smstong']['mobilegroup'];

				if ($usergroup['type'] == 'm' && !empty($groupid)) {
					DB::query("UPDATE ".DB::table('common_member')." SET groupid='$groupid' WHERE uid=$_G[uid]");
				}
			}

			showmessage('smstong:smstong_mobilebind_succeed', 'home.php?mod=spacecp&ac=profile&op=contact');
		}
	} else {

		$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];

		$verifycodes = DB::fetch_first("SELECT mobile,getip,dateline FROM ".DB::table('common_verifycode')." WHERE getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity order by id desc");

		$bindsendtime = intval($verifycodes['dateline']);

		$mobilegap = intval($_G['cache']['plugin']['smstong']['mobilegap']);
		$interval = time() - $bindsendtime;
		$lastsecond = $mobilegap - $interval;

		$sendedmobile = substr($verifycodes['mobile'], 0, 4).'****'.substr($verifycodes['mobile'], 8, 3);

		$_G['sechashi'] = !empty($_G['cookie']['sechashi']) ? $_G['sechash'] + 1 : 0;
		$sechash = 'S'.($_G['inajax'] ? 'A' : '').$_G['sid'].$_G['sechashi'];

		include template('../../source/plugin/smstong/template/bindmobile');
		exit();
	}

} elseif($_GET['action'] == 'exportmobile') {
	$query = DB::query("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE status=2 or status=4");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
	}

	$detail = trim($detail, ","); 
	
	$filename = "mobile_".date('Ymd', TIMESTAMP).'.txt';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exportverify') {
	$query = DB::query("SELECT * FROM ".DB::table('common_verifycode')."");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,getip,verifycode,dateline,reguid,regdateline,status\n".$detail;
	
	$filename = "verify_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exportsended') {
	$query = DB::query("SELECT * FROM sms_send");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,content,addtime,senttime,count,status,remark,refno,port\n".$detail;
	
	$filename = "sended_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exportrecved') {
	$query = DB::query("SELECT * FROM sms_recv");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,content,recvtime,port,senttime,remark\n".$detail;
	
	$filename = "recved_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();

} elseif($_GET['action'] == 'exporthistory') {
	$query = DB::query("SELECT * FROM sms_history");

	while($v = DB::fetch($query)) {
		foreach($v as $key => $value) {
			$value = preg_replace('/\s+/', ' ', $value);
			$detail .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
		}
		$detail = $detail."\n";
	}

	$detail = "id,mobile,content,addtime,senttime,count,status,remark,refno,port\n".$detail;
	
	$filename = "history_".date('Ymd', TIMESTAMP).'.csv';

	ob_end_clean();

	header('Content-Encoding: none');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$filename);
	header('Pragma: no-cache');
	header('Expires: 0');

	if($_G['charset'] != 'gbk') {
		$detail = diconv($detail, $_G['charset'], 'GBK');
	}

	echo $detail;
	exit();
}

showmessage('succeed', '', array(), array('handle' => false));

?>