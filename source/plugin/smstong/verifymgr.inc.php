<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: verifymgr.inc.php 18582 2010-06-02 15:31:40Z Ñ½Ñ½¸öÅÞ $
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
	DB::query("DELETE FROM ".DB::table('common_verifycode')." WHERE id='$_G[gp_id]'");
	ajaxshowheader();
	echo $Plang['smstong_deleted'];
	ajaxshowfooter();
}

$ppp = 16;
$resultempty = FALSE;
$srchadd = $searchtext = $extra = '';
$page = max(1, intval($_G['gp_page']));

$searchtext = '<font color="red">'.$Plang['smstong_verifycode_notice'].'</font>';

if(!empty($_G['gp_srchmobile'])) {
	$srchadd = "AND mobile='$_G[gp_srchmobile]'";
	$searchtext = '<a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=verifymgr">'.$Plang['smstong_viewall'].'</a>&nbsp';
	$extra = '&srchmobile='.$_G['gp_srchmobile'];
}

$statary = array(-1 => $Plang['smstong_status_all'], 1 => $Plang['smstong_get_register_verifycode'], 2 => $Plang['smstong_register_completed'], 3 => $Plang['smstong_get_bindmobile_verifycode'], 4 => $Plang['smstong_bindmobile_completed'], 5 => $Plang['smstong_get_password']);

$status = isset($_G['gp_status']) ? $_G['gp_status'] : -1;

if(isset($status) && $status >= 0) {
	$srchadd .= " AND status='$status'";
	$searchtext = $Plang['smstong_search'].$statary[$status].$Plang['smstong_status'];;
}

if($status >= 0) {
	$searchtext = $searchtext.' <a href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=verifymgr">'.$Plang['smstong_viewall'].'</a>&nbsp';
}

echo '<script type="text/javascript">
		function jsonp36(json) {
			if (json["success"] != true)
			{
				document.write("'.$Plang['smstong_record_mobileerror'].'");
			}
			else
			{
				document.write(json["province_name"]+"/"+json["city"]+"/"+json["cat_name"]);
			}
		}
	 </script>';

showtableheader();

showformheader('plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=verifymgr', 'verifycodesubmit');

showsubmit('verifycodesubmit', $Plang['smstong_search'], ''.$lang['mobile'].': <input name="srchmobile" value="'.htmlspecialchars(stripslashes($_G['gp_srchmobile'])).'" class="txt" maxlength="12" />', $searchtext);


$statselect = '<select onchange="location.href=\''.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=verifymgr'.$extra.'&status=\' + this.value">';
foreach($statary as $k => $v) {
	$statselect .= '<option value="'.$k.'"'.($k == $status ? ' selected' : '').'>'.$v.'</option>';
}
$statselect .= '</select>';

echo '<tr class="header" ><th>'.$Plang['smstong_record_id'].'</th><th>'.$Plang['smstong_record_mobile'].'</th><th>'.$Plang['smstong_record_mobileaddr'].'</th><th>'.$Plang['smstong_record_getip'].'</th><th>'.$Plang['smstong_record_getipaddr'].'</th><th>'.$Plang['smstong_record_verifycode'].'</th><th>'.$Plang['smstong_record_dateline'].'</th><th>'.$Plang['smstong_record_regdateline'].'</th><th>'.$Plang['smstong_record_reguid'].'</th><th>'.$Plang['smstong_record_status'].'</th><th>'.$statselect.'</th><th></th></tr>';
if(!$resultempty) {
	$count = DB::result_first("SELECT COUNT(*) FROM ".DB::table('common_verifycode')." mr WHERE 1 $srchadd");
	$query = DB::query("SELECT * FROM ".DB::table('common_verifycode')." WHERE 1 $srchadd ORDER BY id DESC LIMIT ".(($page - 1) * $ppp).",$ppp");
	$i = 0;
	while($smstong = DB::fetch($query)) {

		$dateline = date('Y-m-d H:i:s',$smstong['dateline']);
		$regdateline = $smstong['regdateline']==0 ? $Plang['smstong_register_uncommitted'] : date('Y-m-d H:i:s',$smstong['regdateline']);
		
		$statuss = $smstong['status']==1 ? $Plang['smstong_get_register_verifycode'] : ($smstong['status']==2 ? $Plang['smstong_register_completed'] : ($smstong['status']==3 ? $Plang['smstong_get_bindmobile_verifycode'] : ($smstong['status']==4 ? $Plang['smstong_bindmobile_completed'] : ($smstong['status']==5 ? $Plang['smstong_get_password'] : $Plang['smstong_unknown_option']))));

		$i++;

		require_once(DISCUZ_ROOT.'./source/function/function_misc.php');

		if ($smstong['reguid'] > 0) {
			$memeber = DB::fetch_first("SELECT username,groupid FROM ".DB::table('common_member')." WHERE uid=$smstong[reguid]");
			if ($memeber['groupid']) {
				$group = DB::fetch_first("SELECT grouptitle FROM ".DB::table('common_usergroup')." WHERE groupid=$memeber[groupid]");
			}
		}

		$reguid = $smstong['reguid']==0 ? $Plang['smstong_register_uncertainty'] : '<a href="'.ADMINSCRIPT.'?action=members&operation=edit&uid='.$smstong['reguid'].'">'.$memeber['username'].'</a> ('.$group['grouptitle'].')';

		echo '<tr><td>'.$smstong['id'].'</td>'.
			'<td><a href="http://www.baidu.com/baidu?wd='.$smstong['mobile'].'&q=3" target="_blank">'.$smstong['mobile'].'</a></td>'.
			'<td id="mobile_'.$smstong['id'].'"><script type="text/javascript" src="http://www.alimama.com/chongzhi/phone_search.do?phone='.$smstong['mobile'].'&callback=jsonp36" charset="gbk"></script></td>'.
			'<td><a href="http://www.baidu.com/baidu?wd='.$smstong['getip'].'&q=3" target="_blank">'.$smstong['getip'].'</a></td>'.
			'<td id="getip_'.$smstong['id'].'">'.str_replace('-','',convertip($smstong['getip'])).'</td>'.
			'<td>'.$smstong['verifycode'].'</td>'.
			'<td>'.$dateline.'</td>'.
			'<td>'.$regdateline.'</td>'.
			'<td>'.$reguid.'</td>'.
			'<td>'.$statuss.'</td>'.
			'<td><a id="p'.$i.'" onclick="ajaxget(this.href, this.id, \'\');return false" href="'.ADMINSCRIPT.'?action=plugins&operation=config&do='.$pluginid.'&identifier=smstong&pmod=verifymgr&id='.$smstong['id'].'&op=delete">['.$lang['delete'].']</a></td></tr>';
	}
}
showtablefooter();

echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=smstong&pmod=verifymgr&status=$status$extra");

echo "<input type=\"submit\" class=\"btn\" name=\"exportmobilesubmit\" value=\"".$Plang['smstong_verifycode_exportmobile']."\" />&nbsp;&nbsp;";

echo "<input type=\"submit\" class=\"btn\" name=\"exportverifysubmit\" value=\"".$Plang['smstong_verifycode_exportverify']."\" />&nbsp;&nbsp;";

echo "<input type=\"submit\" class=\"btn\" name=\"cleardatasubmit\" value=\"".$Plang['smstong_verifycode_cleardata']."\" />";

showformfooter();

if(!empty($_G['gp_exportmobilesubmit'])) {
	header("Location: plugin.php?id=smstong:verifycode&action=exportmobile");
}

if(!empty($_G['gp_exportverifysubmit'])) {
	header("Location: plugin.php?id=smstong:verifycode&action=exportverify");
}

if(!empty($_G['gp_cleardatasubmit'])) {
	$query = DB::query("TRUNCATE TABLE ".DB::table('common_verifycode')."");
	header("Location: ".ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=smstong&pmod=verifymgr");
}

?>