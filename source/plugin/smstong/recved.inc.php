<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: recved.inc.php 18582 2011-10-04 11:36:40Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

loadcache('plugin');

$Plang = $scriptlang['smstong'];

if (!$_G['cache']['plugin']['smstong']) {
	cpmsg($Plang['smstong_plugin_closed'], "action=plugins", 'error');
}

if($_G['gp_op'] == 'delete') {
	DB::query("DELETE FROM sms_recv WHERE id='$_G[gp_id]'");
	ajaxshowheader();
	echo $Plang['smstong_deleted'];
	ajaxshowfooter();
}

$ppp = 15;
$resultempty = FALSE;
$srchadd = $searchtext = $extra = '';
$page = max(1, intval($_G['gp_page']));

$searchtext = '<font color="red">'.$Plang['smstong_recved_notice'].'</font>';

if(!empty($_G['gp_srchmobile'])) {
	$srchadd = "AND mobile='$_G[gp_srchmobile]'";
	$searchtext = '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=recved">'.$Plang['smstong_viewall'].'</a>&nbsp';
	$extra = '&srchmobile='.$_G['gp_srchmobile'];
}

$statary = array(-1 => $Plang['smstong_status_all'], 0 => $Plang['smstong_recved_type_one'], 1 => $Plang['smstong_recved_type_two'], 2 => $Plang['smstong_recved_type_three']);

$status = isset($_G['gp_status']) ? $_G['gp_status'] : -1;

if(isset($status) && $status >= 0) {
	$srchadd .= " AND port='$status'";
	$searchtext = $Plang['smstong_search'].$statary[$status].$Plang['smstong_status'];;
}

if($status >= 0) {
	$searchtext = $searchtext.' <a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=recved">'.$Plang['smstong_viewall'].'</a>&nbsp';
}

showtableheader();

showformheader('plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=recved', 'recvedsubmit');

showsubmit('recvedsubmit', $Plang['smstong_search'], ''.$lang['mobile'].': <input name="srchmobile" value="'.htmlspecialchars(stripslashes($_G['gp_srchmobile'])).'" class="txt" maxlength="12" />', $searchtext);


$statselect = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=recved'.$extra.'&status=\' + this.value">';
foreach($statary as $k => $v) {
	$statselect .= '<option value="'.$k.'"'.($k == $status ? ' selected' : '').'>'.$v.'</option>';
}
$statselect .= '</select>';

echo '<tr class="header"><th width="5%">'.$Plang['smstong_record_id'].'</th><th width="8%">'.$Plang['smstong_recved_mobile'].'</th><th>'.$Plang['smstong_sended_content'].'</th><th width="13%">'.$Plang['smstong_recved_recvedtime'].'</th><th width="13%">'.$Plang['smstong_sended_senttime'].'</th><th width="6%">'.$Plang['smstong_recved_type'].'</th><th width="10%">'.$Plang['smstong_sended_remark'].'</th><th>'.$statselect.'</th></tr>';

if(!$resultempty) {
	$count = DB::result_first("SELECT COUNT(*) FROM sms_recv mr WHERE 1 $srchadd");
	$query = DB::query("SELECT * FROM sms_recv WHERE 1 $srchadd ORDER BY id DESC LIMIT ".(($page - 1) * $ppp).",$ppp");
	$i = 0;
	while($recved = DB::fetch($query)) {

		$types = $recved['port'] == 0 ? $Plang['smstong_recved_type_one'] : ($recved['port'] == 1 ? $Plang['smstong_recved_type_two'] : ($recved['port'] == 2 ? $Plang['smstong_recved_type_three'] :  $Plang['smstong_unknown_option'])) ;

		$i++;

		echo '<tr><td>'.$recved['id'].'</td>'.
			'<td><a href="http://www.baidu.com/baidu?wd='.$recved['mobile'].'&q=3" target="_blank">'.$recved['mobile'].'</a></td>'.
			'<td>'.htmlspecialchars($recved['content']).'</td>'.
			'<td>'.$recved['recvtime'].'</td>'.
			'<td>'.$recved['senttime'].'</td>'.
			'<td>'.$types.'</td>'.
			'<td>'.(empty($recved['remark'])?'ÎÞ':$recved['remark']).'</td>'.
			'<td><a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=qunfasms&mobile='.$recved['mobile'].'&content='.urlencode($recved['content']).'">['.$Plang['smstong_recved_reply'].']</a>&nbsp;<a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=recved&id='.$recved['id'].'&op=delete">['.$lang['delete'].']</a></td></tr>';
	}
}
showtablefooter();

echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=smstong&pmod=recved&status=$status$extra");

echo "<input type=\"submit\" class=\"btn\" name=\"exportrecvedubmit\" value=\"".$Plang['smstong_recved_exportrecved']."\" />&nbsp;&nbsp;";

echo "<input type=\"submit\" class=\"btn\" name=\"cleardatasubmit\" value=\"".$Plang['smstong_verifycode_cleardata']."\" />";

showformfooter();

if(!empty($_G['gp_exportrecvedubmit'])) {
	header("Location: plugin.php?id=smstong:verifycode&action=exportrecved");
}

if(!empty($_G['gp_cleardatasubmit'])) {
	$query = DB::query("TRUNCATE TABLE sms_recv");
	header("Location: ".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=smstong&pmod=recved");
}

?>