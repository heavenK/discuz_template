<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: operation.inc.php 78 2012-04-16 10:02:02Z wangbin $
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
define(TOOLS_ROOT, dirname(__FILE__).'/');
require_once (TOOLS_ROOT.'./function/tools.func.php');
include_once(DISCUZ_ROOT.'/source/discuz_version.php');
$xver = preg_replace('/(X|R|C)/im','',DISCUZ_VERSION);
$identifier = $_GET['identifier'];
$urls = '&pmod=operation&identifier='.$identifier.'&operation='.$operation.'&do='.$do;
showsubmenus($toolslang['aboutmotion'],array(
    array($toolslang['motion'],'plugins&cp=motion'.$urls))
);
$cparray = array('motion');
$cp = !in_array($_GET['cp'], $cparray) ? 'motion' : $_GET['cp'];
require TOOLS_ROOT.'./include/'.$cp.'.inc.php';
?>