<?php
/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: safe.inc.php 77 2012-04-16 09:59:38Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');

if(file_exists(DISCUZ_ROOT.'./data/plugindata/tools.lang.php')){
	include DISCUZ_ROOT.'./data/plugindata/tools.lang.php';
} else {
	loadcache('pluginlanguage_template');
	loadcache('pluginlanguage_script');
	$scriptlang['tools'] = $_G['cache']['pluginlanguage_script']['tools'];
}

$toolslang = $scriptlang['tools'];
require_once DISCUZ_ROOT.'./source/plugin/tools/function/tools.func.php';
include_once(DISCUZ_ROOT.'./source/discuz_version.php');

$_GET['cp'] == '' ? $_GET['cp'] =  'aboutdb' : $_GET['cp'];
$xver = preg_replace('/(X|R|C)/im','',DISCUZ_VERSION);

$ppp = 20;
$page = max(1, intval($_GET['page']));
$startlimit = ($page - 1) * $ppp;
$deletes = '';
$extrasql = '';

$filter = $_GET['filter'];
if($filter == 'banned') {
	$extrasql = "AND replacement LIKE '%BANNED%'";
} elseif($filter == 'mod') {
	$extrasql = "AND replacement LIKE '%MOD%'";	
} elseif($filter == 'replace') {
	$extrasql = "AND replacement NOT LIKE '%MOD%' AND replacement NOT LIKE '%BANNED%'";
} else {
	$extrasql = '';	
}
$rule = get_rule();
$identifier = $_GET['identifier'];
$urls = '&pmod=safe&identifier='.$identifier.'&operation='.$operation.'&do='.$do;
showsubmenus($toolslang['aboutsafe'],array(
	array(array('menu' => $toolslang['info_sec'], 'submenu' => array(
			array($toolslang['censor_admin'], 'plugins&cp=censor_admin'.$urls),
            array($toolslang['censor_scanbbs'], 'plugins&cp=censor_scanbbs'.$urls),
			array($toolslang['censor_scanhome'], 'plugins&cp=censor_scanhome'.$urls),
            array($toolslang['censor_scanprotal'], 'plugins&cp=censor_scanprotal'.$urls),
		))),
    array(array('menu' => $toolslang['site_sec'], 'submenu' => array(
			array($toolslang['file_php'], 'plugins&cp=file_php'.$urls),
			array($toolslang['file_hack'], 'plugins&cp=file_hack'.$urls),
            array($toolslang['file_search'], 'plugins&cp=file_search'.$urls),
            array($toolslang['changekey'], 'plugins&cp=changekey'.$urls),
		))),
        ));
$cparray = array('censor_admin', 'censor_scanbbs', 'censor_scanhome', 'censor_scanprotal', 'file_php', 'file_hack', 'file_search', 'changekey');
$cp = !in_array($_GET['cp'], $cparray) ? 'censor_admin' : $_GET['cp'];
define(TOOLS_ROOT, dirname(__FILE__).'/');

require TOOLS_ROOT.'./include/'.$cp.'.inc.php';
showformfooter();
?>