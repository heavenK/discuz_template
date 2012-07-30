<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: sendsms.inc.php 18582 2010-06-02 16:20:47Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('plugin');

$Plang = $scriptlang['smstong'];

if (!$_G['cache']['plugin']['smstong']) {
	cpmsg($Plang['smstong_plugin_closed'], "action=plugins", 'error');
}

if (($_G['cache']['plugin']['smstong']['smsusername'] == 'demo') && ($_G['cache']['plugin']['smstong']['smspassword'] == 'demo')) {
	cpmsg($Plang['smstong_username_password_empty'], "action=plugins&operation=config&do=$_G[gp_do]", 'error');
}

if (empty($_G['cache']['plugin']['smstong']['smsusername']) || empty($_G['cache']['plugin']['smstong']['smspassword'])) {
	cpmsg($Plang['smstong_username_password_empty'], "action=plugins&operation=config&do=$_G[gp_do]", 'error');
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

if(!submitcheck('sendsmssubmit', 1)) {

showsubmenusteps($Plang['smstong_nav_members_sendsms'], array(
	array('nav_members_select', !$_G['gp_submit']),
	array($Plang['smstong_nav_members_smscontent'], $_G['gp_submit']),
));

showsearchform('sendsms');

if(submitcheck('submit', 1)) {
	
	$membernum = countmembers($search_condition);

	showtagheader('div', 'sendsms', TRUE);
	showformheader('plugins&operation=config&do='.$_G['gp_do'].'&identifier=smstong&pmod=sendsms', 'sendsmssubmit');
	showhiddenfields(array('notifymember' => 1));
	echo '<table class="tb tb1">';

	if(!$membernum) {
		showtablerow('', 'class="lineheight"', $lang['members_search_nonexistence']);
	} else {
		showtablerow('class="first"', array('class="th11"'), array(
			cplang($Plang['smstong_members_sendsms_members']),
			cplang('members_search_result', array('membernum' => $membernum))."<a href=\"###\" onclick=\"$('searchmembers').style.display='';$('sendsms').style.display='none';$('step1').className='current';$('step2').className='';\" class=\"act\">$lang[research]</a>&nbsp;<strong>$Plang[smstong_members_sendsms_notice1]</strong>"
		));

		showtagheader('tbody', 'messagebody', TRUE);
		showsendsms();
		showtagfooter('tbody');

		$search_condition = serialize($search_condition);
		showsubmit('sendsmssubmit', 'submit', 'td', '<input type="hidden" name="conditions" value=\''.$search_condition.'\' />');

	}

	showtablefooter();
	showformfooter();
	showtagfooter('div');

}

} else {

$search_condition = unserialize(stripslashes($_POST['conditions']));
$membernum = countmembers($search_condition);
notifymembers('sendsms', 'sendsms');

}

function showsearchform($operation = '') {
	global $_G, $lang;

	$groupselect = array();
	$usergroupid = isset($_G['gp_usergroupid']) && is_array($_G['gp_usergroupid']) ? $_G['gp_usergroupid'] : array();
	$query = DB::query("SELECT type, groupid, grouptitle, radminid FROM ".DB::table('common_usergroup')." WHERE groupid NOT IN ('6', '7') ORDER BY (creditshigher<>'0' || creditslower<>'0'), creditslower, groupid");
	while($group = DB::fetch($query)) {
		$group['type'] = $group['type'] == 'special' && $group['radminid'] ? 'specialadmin' : $group['type'];
		$groupselect[$group['type']] .= "<option value=\"$group[groupid]\" ".(in_array($group['groupid'], $usergroupid) ? 'selected' : '').">$group[grouptitle]</option>\n";
	}
	$groupselect = '<optgroup label="'.$lang['usergroups_member'].'">'.$groupselect['member'].'</optgroup>'.
		($groupselect['special'] ? '<optgroup label="'.$lang['usergroups_special'].'">'.$groupselect['special'].'</optgroup>' : '').
		($groupselect['specialadmin'] ? '<optgroup label="'.$lang['usergroups_specialadmin'].'">'.$groupselect['specialadmin'].'</optgroup>' : '').
		'<optgroup label="'.$lang['usergroups_system'].'">'.$groupselect['system'].'</optgroup>';

	showtagheader('div', 'searchmembers', !$_G['gp_submit']);
	echo '<script src="static/js/calendar.js" type="text/javascript"></script>';
	echo '<style type="text/css">#residedistrictbox select, #birthdistrictbox select{width: auto;}</style>';
	showformheader("plugins&operation=config&do=".$_G['gp_do']."&identifier=smstong&pmod=sendsms", "onSubmit=\"if($('updatecredittype1') && $('updatecredittype1').checked && !window.confirm('$lang[members_reward_clean_alarm]')){return false;} else {return true;}\"");
	showtableheader();
	showsetting('members_search_user', 'username', $_G['gp_username'], 'text');
	showsetting('members_search_uid', 'uid', $_G['gp_uid'], 'text');
	showsetting('members_search_group', '', '', '<select name="groupid[]" multiple="multiple" size="10">'.$groupselect.'</select>');
	showtablefooter();

	showtableheader();
	$_G['showsetting_multirow'] = 1;
	showtagheader('tbody', 'advanceoption');
	if(!empty($_G['setting']['connect']['allow'])) {
		showsetting('members_search_conisbind', array('conisbind', array(
			array(1, $lang['yes']),
			array(0, $lang['no']),
		), 1), $_G['gp_conisbind'], 'mradio');
		showsetting('members_search_uinblacklist', array('uin_low', array(
			array(1, $lang['yes']),
			array(0, $lang['no']),
		), 1), $_G['gp_uin_low'], 'mradio');
	}
	showsetting('members_search_online', array('sid_noempty', array(
		array(1, $lang['yes']),
		array(0, $lang['no']),
	), 1), $_G['gp_online'], 'mradio');
	showsetting('members_search_lockstatus', array('status', array(
		array(-1, $lang['yes']),
		array(0, $lang['no']),
	), 1), $_G['gp_status'], 'mradio');
	showsetting('members_search_emailstatus', array('emailstatus', array(
		array(1, $lang['yes']),
		array(0, $lang['no']),
	), 1), $_G['gp_emailstatus'], 'mradio');
	showsetting('members_search_avatarstatus', array('avatarstatus', array(
		array(1, $lang['yes']),
		array(0, $lang['no']),
	), 1), $_G['gp_avatarstatus'], 'mradio');
	showsetting('members_search_email', 'email', $_G['gp_email'], 'text');
	showsetting("$lang[credits] $lang[members_search_between]", array("credits_low", "credits_high"), array($_G['gp_credits_low'], $_G['gp_credtis_high']), 'range');

	if(!empty($_G['setting']['extcredits'])) {
		foreach($_G['setting']['extcredits'] as $id => $credit) {
			showsetting("$credit[title] $lang[members_search_between]", array("extcredits$id"."_low", "extcredits$id"."_high"), array($_G['gp_extcredits'.$id.'_low'], $_G['gp_extcredits'.$id.'_high']), 'range');
		}
	}

	showsetting('members_search_friendsrange', array('friends_low', 'friends_high'), array($_G['gp_friends_low'], $_G['gp_friends_high']), 'range');
	showsetting('members_search_postsrange', array('posts_low', 'posts_high'), array($_G['gp_posts_low'], $_G['gp_posts_high']), 'range');
	showsetting('members_search_regip', 'regip', $_G['gp_regip'], 'text');
	showsetting('members_search_lastip', 'lastip', $_G['gp_lastip'], 'text');
	showsetting('members_search_regdaterange', array('regdate_after', 'regdate_before'), array($_G['gp_regdate_after'], $_G['gp_regdate_before']), 'daterange');
	showsetting('members_search_lastvisitrange', array('lastvisit_after', 'lastvisit_before'), array($_G['gp_lastvisit_after'], $_G['gp_lastvisit_before']), 'daterange');
	showsetting('members_search_lastpostrange', array('lastpost_after', 'lastpost_before'), array($_G['gp_lastpost_after'], $_G['gp_lastpost_before']), 'daterange');
	showsetting('members_search_group_fid', 'fid', $_G['gp_fid'], 'text');
	if($_G['setting']['verify']) {
		$verifydata = array();
		foreach($_G['setting']['verify'] as $key => $value) {
			if($value['available']) {
				$verifydata[] = array('verify'.$key, $value['title']);
			}
		}
		if(!empty($verifydata)) {
			showsetting('members_search_verify', array('verify', $verifydata), $_G['gp_verify'], 'mcheckbox');
		}
	}
	$yearselect = $monthselect = $dayselect = "<option value=\"\">".cplang('nolimit')."</option>\n";
	$yy=dgmdate(TIMESTAMP, 'Y');
	for($y=$yy; $y>=$yy-100; $y--) {
		$y = sprintf("%04d", $y);
		$yearselect .= "<option value=\"$y\" ".($_G['gp_birthyear'] == $y ? 'selected' : '').">$y</option>\n";
	}
	for($m=1; $m<=12; $m++) {
		$m = sprintf("%02d", $m);
		$monthselect .= "<option value=\"$m\" ".($_G['gp_birthmonth'] == $m ? 'selected' : '').">$m</option>\n";
	}
	for($d=1; $d<=31; $d++) {
		$d = sprintf("%02d", $d);
		$dayselect .= "<option value=\"$d\" ".($_G['gp_birthday'] == $d ? 'selected' : '').">$d</option>\n";
	}
	showsetting('members_search_birthday', '', '', '<select class="txt" name="birthyear" style="width:75px; margin-right:0">'.$yearselect.'</select> '.$lang['year'].' <select class="txt" name="birthmonth" style="width:75px; margin-right:0">'.$monthselect.'</select> '.$lang['month'].' <select class="txt" name="birthday" style="width:75px; margin-right:0">'.$dayselect.'</select> '.$lang['day']);

	loadcache('profilesetting');
	unset($_G['cache']['profilesetting']['uid']);
	unset($_G['cache']['profilesetting']['birthyear']);
	unset($_G['cache']['profilesetting']['birthmonth']);
	unset($_G['cache']['profilesetting']['birthday']);
	require_once libfile('function/profile');
	foreach($_G['cache']['profilesetting'] as $fieldid=>$value) {
		if(!$value['available'] || in_array($fieldid, array('birthprovince', 'birthdist', 'birthcommunity', 'resideprovince', 'residedist', 'residecommunity'))) {
			continue;
		}
		if($fieldid == 'gender') {
			$select = "<option value=\"\">".cplang('nolimit')."</option>\n";
			$select .= "<option value=\"0\">".cplang('members_edit_gender_secret')."</option>\n";
			$select .= "<option value=\"1\">".cplang('members_edit_gender_male')."</option>\n";
			$select .= "<option value=\"2\">".cplang('members_edit_gender_female')."</option>\n";
			showsetting($value['title'], '', '', '<select class="txt" name="gender">'.$select.'</select>');
		} elseif($fieldid == 'birthcity') {
			$elems = array('birthprovince', 'birthcity', 'birthdist', 'birthcommunity');
			showsetting($value['title'], '', '', '<div id="birthdistrictbox">'.showdistrict(array(0,0,0,0), $elems, 'birthdistrictbox', 1).'</div>');
		} elseif($fieldid == 'residecity') {
			$elems = array('resideprovince', 'residecity', 'residedist', 'residecommunity');
			showsetting($value['title'], '', '', '<div id="residedistrictbox">'.showdistrict(array(0,0,0,0), $elems, 'residedistrictbox', 1).'</div>');
		} elseif($fieldid == 'constellation') {
			$select = "<option value=\"\">".cplang('nolimit')."</option>\n";
			for($i=1; $i<=12; $i++) {
				$name = lang('space', 'constellation_'.$i);
				$select .= "<option value=\"$name\">$name</option>\n";
			}
			showsetting($value['title'], '', '', '<select class="txt" name="constellation">'.$select.'</select>');
		} elseif($fieldid == 'zodiac') {
			$select = "<option value=\"\">".cplang('nolimit')."</option>\n";
			for($i=1; $i<=12; $i++) {
				$option = lang('space', 'zodiac_'.$i);
				$select .= "<option value=\"$option\">$option</option>\n";
			}
			showsetting($value['title'], '', '', '<select class="txt" name="zodiac">'.$select.'</select>');
		} elseif($value['formtype'] == 'select' || $value['formtype'] == 'list') {
			$select = "<option value=\"\">".cplang('nolimit')."</option>\n";
			$value['choices'] = explode("\n",$value['choices']);
			foreach($value['choices'] as $option) {
				$option = trim($option);
				$select .= "<option value=\"$option\">$option</option>\n";
			}
			showsetting($value['title'], '', '', '<select class="txt" name="'.$fieldid.'">'.$select.'</select>');
		} else {
			showsetting($value['title'], '', '', '<input class="txt" name="'.$fieldid.'" />');
		}
	}
	showtagfooter('tbody');
	$_G['showsetting_multirow'] = 0;
	showsubmit('submit', $operation == 'clean' ? 'members_delete' : 'search', '', 'more_options');
	showtablefooter();
	showformfooter();
	showtagfooter('div');
}

function searchmembers($condition, $limit=2000, $start=0) {
	include_once libfile('class/membersearch');
	$ms = new membersearch();
	return $ms->search($condition, $limit, $start);
}

function countmembers($condition, &$urladd) {
	$urladd = '';
	foreach($condition as $k => $v) {
		if(in_array($k, array('formhash', 'submit', 'page')) || $v === '') {
			continue;
		}
		if(is_array($v)) {
			foreach($v as $vk => $vv) {
				if($vv === '') {
					continue;
				}
				$urladd .= '&'.$k.'['.$vk.']='.rawurlencode($vv);
			}
		} else {
			$urladd .= '&'.$k.'='.rawurlencode($v);
		}
	}
	include_once libfile('class/membersearch');
	$ms = new membersearch();
	return $ms->getcount($condition);
}

function showsendsms() {
	global $lang;
	global $Plang;
	showtablerow('', array('class="th12"', ''), array(
		$Plang['smstong_members_sendsms_message'],
		'<textarea name="message" cols="100" rows="10"></textarea>'
	));
	showtablerow('', array('', 'class="td12"'), array(
		'',
		'<ul><li><input type="hidden" value="sendsms" name="notifymembers" id="viapm" /><font color="red">'.$Plang['smstong_notice_count'].'</font>&nbsp;<span class="diffcolor2">'.$lang['members_newsletter_num'].':</span><input type="text" class="txt" name="pertask" value="100" size="10" maxlength="3" onblur="if (isNaN(this.value*1)) {this.value = 100;} if (this.value*1>500) {this.value = 500;}"></li></ul>'
	));
	showtablerow('', array('class="th12"', ''), array(
		'',
		'<strong>'.$Plang['smstong_members_sendsms_notice2'].'</strong>'
	));
}

function notifymembers($operation, $variable) {
	global $_G, $lang, $Plang, $urladd, $conditions, $search_condition;

	if(!empty($_G['gp_current'])) {
		$message = $_G['gp_message'];
	} else {
		$current = 0;
		$message = trim($_G['gp_message']);
		$message = trim(str_replace("\t", ' ', $message));
		$message = stripslashes($message);
	}

	$pertask = intval($_G['gp_pertask']);
	$current = $_G['gp_current'] ? intval($_G['gp_current']) : 0;
	$next = $current + $pertask;
	$continue = FALSE;

	$uids = searchmembers($search_condition, $pertask, $current);
	$conditions = $uids ? 'uid IN ('.dimplode($uids).')' : '0';

	if($_G['gp_notifymember'] && in_array($_G['gp_notifymembers'], array('sendsms'))) {

		if(empty($message))
		{
			cpmsg($Plang['smstong_members_sendsms_sm_invalid'], '', 'error');
		}

		$sql = "SELECT uid, mobile FROM ".DB::table('common_member_profile')." WHERE $conditions AND mobile<>''";
		
		$query = DB::query($sql);

		require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

		while($member = DB::fetch($query)) {

			if(!ismobile($member['mobile'])) {
				continue;
			}

			$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $member['mobile'], $message, false);

			if($ret === true)
			{
				$continue = TRUE;
			}
			else
			{
				cpmsg($Plang['smstong_sendsms_failured'].$ret, '', 'error');
			}
		}

	}

	$newsletter_detail = array();
	if($continue) {
		cpmsg("$Plang[smstong_nav_members_sendsms]: ".cplang('members_newsletter_processing', array('current' => $current, 'next' => $next, 'search_condition' => serialize($search_condition))), "action=plugins&operation=config&do=$_G[gp_do]&identifier=smstong&pmod=sendsms&sendsmssubmit=yes&message=".rawurlencode($_G['gp_message'])."&current=$next&pertask=$pertask&notifymember={$_G['gp_notifymember']}&notifymembers=".rawurlencode($_G['gp_notifymembers']).$urladd, 'loadingform');
	} else {
		cpmsg($Plang['smstong_members_sendsms_notify_succeed'], "action=plugins&operation=config&do=$_G[gp_do]&identifier=smstong&pmod=sendsms", 'succeed');
	}

}

?>