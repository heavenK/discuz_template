<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: sended.inc.php 18582 2011-10-04 11:36:40Z Ñ½Ñ½¸öÅÞ $
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
	DB::query("DELETE FROM sms_send WHERE id='$_G[gp_id]'");
	ajaxshowheader();
	echo $Plang['smstong_deleted'];
	ajaxshowfooter();
}

$ppp = 15;
$resultempty = FALSE;
$srchadd = $searchtext = $extra = '';
$page = max(1, intval($_G['gp_page']));

$searchtext = '<font color="red">'.$Plang['smstong_sended_notice'].'</font>';

if(!empty($_G['gp_srchmobile'])) {
	$srchadd = "AND mobile='$_G[gp_srchmobile]'";
	$searchtext = '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=sended">'.$Plang['smstong_viewall'].'</a>&nbsp';
	$extra = '&srchmobile='.$_G['gp_srchmobile'];
}

$statary = array(-1 => $Plang['smstong_status_all'], 0 => $Plang['smstong_sended_wait'], 1 => $Plang['smstong_sended_success'], 2 => $Plang['smstong_sended_failure'], 3 => $Plang['smstong_sended_recved'], 4 => $Plang['smstong_sended_blackphone'], 5 => $Plang['smstong_sended_blackdict']);

$status = isset($_G['gp_status']) ? $_G['gp_status'] : -1;

if(isset($status) && $status >= 0) {
	$srchadd .= " AND status='$status'";
	$searchtext = $Plang['smstong_search'].$statary[$status].$Plang['smstong_status'];;
}

if($status >= 0) {
	$searchtext = $searchtext.' <a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=sended">'.$Plang['smstong_viewall'].'</a>&nbsp';
}

showtableheader();

showformheader('plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=sended', 'sendedsubmit');

showsubmit('sendedsubmit', $Plang['smstong_search'], ''.$lang['mobile'].': <input name="srchmobile" value="'.htmlspecialchars(stripslashes($_G['gp_srchmobile'])).'" class="txt" maxlength="12" />', $searchtext);


$statselect = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=sended'.$extra.'&status=\' + this.value">';
foreach($statary as $k => $v) {
	$statselect .= '<option value="'.$k.'"'.($k == $status ? ' selected' : '').'>'.$v.'</option>';
}
$statselect .= '</select>';

echo '<tr class="header"><th width="5%">'.$Plang['smstong_record_id'].'</th><th width="8%">'.$Plang['smstong_record_mobile'].'</th><th>'.$Plang['smstong_sended_content'].'</th><th width="13%">'.$Plang['smstong_sended_addtime'].'</th><th width="13%">'.$Plang['smstong_sended_senttime'].'</th><th width="6%">'.$Plang['smstong_sended_count'].'</th><th width="6%">'.$Plang['smstong_sended_status'].'</th><th width="10%">'.$Plang['smstong_sended_port'].'</th><th width="6%">'.$Plang['smstong_sended_remark'].'</th><th>'.$statselect.'</th><th></th></tr>';

if(!$resultempty) {
	$count = DB::result_first("SELECT COUNT(*) FROM sms_send mr WHERE 1 $srchadd");
	$query = DB::query("SELECT * FROM sms_send WHERE 1 $srchadd ORDER BY id DESC LIMIT ".(($page - 1) * $ppp).",$ppp");
	$i = 0;
	while($sended = DB::fetch($query)) {

		$statuss = $sended['status'] == 0 ? $Plang['smstong_sended_wait'] : ($sended['status'] == 1 ? $Plang['smstong_sended_success'] : ($sended['status'] == 2 ? $Plang['smstong_sended_failure'] : ($sended['status'] == 3 ? $Plang['smstong_sended_recved'] : ($sended['status'] == 4 ? $Plang['smstong_sended_blackphone'] : ($sended['status'] == 5 ? $Plang['smstong_sended_blackdict'] : $Plang['smstong_unknown_option']))))) ;

		$i++;

		echo '<tr><td>'.$sended['id'].'</td>'.
			'<td><a href="http://www.baidu.com/baidu?wd='.$sended['mobile'].'&q=3" target="_blank">'.$sended['mobile'].'</a></td>'.
			'<td>'.htmlspecialchars($sended['content']).'</td>'.
			'<td>'.$sended['addtime'].'</td>'.
			'<td>'.$sended['senttime'].'</td>'.
			'<td>'.$sended['count'].'</td>'.
			'<td>'.$statuss.'</td>'.
			'<td>'.$sended['port'].'</td>'.
			'<td>'.(empty($sended['remark'])?''.$Plang['smstong_sended_none'].'':$sended['remark']).'</td>'.
			'<td><a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=qunfasms&mobile='.$sended['mobile'].'&content='.urlencode($sended['content']).'">['.$Plang['smstong_sended_resend'].']</a>&nbsp;<a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=sended&id='.$sended['id'].'&op=delete">['.$lang['delete'].']</a></td></tr>';
	}
}
showtablefooter();

echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=smstong&pmod=sended&status=$status$extra");

echo "<input type=\"submit\" class=\"btn\" name=\"exportsendedubmit\" value=\"".$Plang['smstong_sended_exportsended']."\" />&nbsp;&nbsp;";

echo "<input type=\"submit\" class=\"btn\" name=\"cleardatasubmit\" value=\"".$Plang['smstong_verifycode_cleardata']."\" />";

showformfooter();

if(!empty($_G['gp_exportsendedubmit'])) {
	header("Location: plugin.php?id=smstong:verifycode&action=exportsended");
}

if(!empty($_G['gp_cleardatasubmit'])) {
	$query = DB::query("TRUNCATE TABLE sms_send");
	header("Location: ".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=smstong&pmod=sended");
}

?>