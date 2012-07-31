<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: qunfasms.inc.php 18582 2010-06-08 16:09:55Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('plugin');

$Plang = $scriptlang['smstong'];

if (!$_G['cache']['plugin']['smstong']) {
	cpmsg($Plang['smstong_plugin_closed'], "action=plugins", 'error');
}

$_G['setting']['memberperpage'] = 20;
$page = max(1, $_G['page']);
$start_limit = ($page - 1) * $_G['setting']['memberperpage'];
$search_condition = array_merge($_GET, $_POST);

foreach($search_condition as $k => $v) {
	if(in_array($k, array('action', 'operation', 'formhash', 'submit', 'page')) || $v === '') {
		unset($search_condition[$k]);
	}
}

if(!submitcheck('qunfasmssubmit', 1)) {

	showsubmenusteps($Plang['smstong_nav_members_qunfasms'], array(
		array($Plang['smstong_nav_members_select'], !$_G['gp_submit'])
	));

	showtagheader('div', 'qunfasms', TRUE);
	showformheader('plugins&operation=config&do='.$_G['gp_do'].'&identifier=smstong&pmod=qunfasms', 'qunfasmssubmit');
	showhiddenfields(array('notifymember' => 1));
	echo '<table class="tb tb1">';

	if($_G['gp_getmobile']) {
		$query = DB::query("SELECT mobile FROM ".DB::table('common_member_profile')." WHERE mobile<>''");
		require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

		while($v = DB::fetch($query)) {
			foreach($v as $key => $value) {
				$value = preg_replace('/\s+/', ' ', $value);
				if (ismobile($value))
				$mobile .= strlen($value) > 11 && is_numeric($value) ? '['.$value.'],' : $value.',';
			}
		}
		$_G['gp_mobile'] = trim($mobile, ",");
	}

	showtablerow('', array('class="th12"', ''), array(
		$Plang['smstong_members_qunfasms_mobile'],
		'<textarea name="mobile" cols="100" rows="25">'.$_G['gp_mobile'].'</textarea>'
	));

	showtagheader('tbody', 'messagebody', TRUE);
	showsendsms();
	showtagfooter('tbody');

	$search_condition = serialize($search_condition);
	showsubmit('qunfasmssubmit', 'submit', 'td', '<input type="hidden" name="conditions" value=\''.$search_condition.'\' />');

	showtablefooter();
	showformfooter();
	showtagfooter('div');

} else {
	notifymembers('qunfasms', 'qunfasms');
}

function showsendsms() {
	global $_G;
	global $lang;
	global $Plang;

	showtablerow('', array('class="th12"', ''), array(
		$Plang['smstong_members_qunfasms_content'],
		'<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=qunfasms&getmobile=true">'.$Plang['smstong_members_qunfasms_getmobile'].'</a><br /><br /><textarea name="message" cols="100" rows="10">'.$_G['gp_content'].'</textarea>'
	));

	showtablerow('', array('class="th12"', ''), array(
		'',
		'<strong>'.$Plang['smstong_members_qunfasms_notice'].'</strong>'
	));
}

function notifymembers($operation, $variable) {
	global $_G, $lang, $Plang, $urladd;

	$mobile = $message = '';
	$mobile = $_G['gp_mobile'];
	$message = $_G['gp_message'];

	if(empty($mobile))
	{
		cpmsg($Plang['smstong_members_qunfasms_mobile_invalid'], '', 'error');
	}

	if(empty($message))
	{
		cpmsg($Plang['smstong_members_qunfasms_content_invalid'], '', 'error');
	}

	$mobile = str_replace(' ',',',$mobile);
	$mobile = str_replace('£¬',',',$mobile);

	$mobiles = preg_split('/[\s,]+/', $mobile, -1, PREG_SPLIT_NO_EMPTY);
	$mobile = implode(',', $mobiles);

	require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

	$count = count($mobiles);
	$seed = 100;
	
	if ($count <= $seed) {

		$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $message, false);
	
		if($ret === true)
		{
			cpmsg($Plang['smstong_members_sendsms_notify_succeed'], "action=plugins&operation=config&do=$_G[gp_do]&identifier=smstong&pmod=qunfasms", 'succeed');
		}
		else
		{
			cpmsg($Plang['smstong_sendsms_failured'].$ret, '', 'error');
		}

	} else {
		
		$error = false;
		$counts = ceil($count/$seed);
		
		for ($i = 0; $i < $counts; $i++) {

			$m = '';

			for ($n = $i*$seed; $n < $i*$seed+$seed; $n++) {
				if (!isset($mobiles[$n])) {
					break;
				} else {
					$m .= $mobiles[$n].',';
				}
			}

			$m = trim($m, ",");
			
			$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $m, $message, false);
			
			if($ret === true)
			{
				$error = false;
			}
			else
			{
				$error = true;
				cpmsg($Plang['smstong_sendsms_failured'].$ret, '', 'error');
			}

		}
		
		if (!$error)
		cpmsg($Plang['smstong_members_sendsms_notify_succeed'], "action=plugins&operation=config&do=$_G[gp_do]&identifier=smstong&pmod=qunfasms", 'succeed');
	}
}

?>