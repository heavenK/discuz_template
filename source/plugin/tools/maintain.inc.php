<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: maintain.inc.php 78 2012-04-16 10:02:02Z wangbin $
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
require_once TOOLS_ROOT.'./function/tools.func.php';
include_once(DISCUZ_ROOT.'./source/discuz_version.php');
$identifier = $_GET['identifier'];
$urls = '&pmod=maintain&identifier='.$identifier.'&operation='.$operation.'&do='.$do;
$xver = preg_replace('/(X|R|C)/im','',DISCUZ_VERSION);
showsubmenus($toolslang['aboutmaintain'],array(
    array(array('menu' => $toolslang['aboutdb'], 'submenu' => array(
        array($toolslang['cleardb'], 'plugins&cp=aboutdb'.$urls),
        //array($toolslang['amend'], 'plugins&cp=amend'.$urls),
//        array($toolslang['cleannote'], 'plugins&cp=cleannote'.$urls),
//        array($toolslang['repauto'], 'plugins&cp=repauto'.$urls),
        ))),
    array(array('menu' => $toolslang['aboutfiles'], 'submenu' => array(
        array($toolslang['clean_att'], 'plugins&cp=clean_att'.$urls),
        //array($toolslang['check_file'], 'plugins&cp=check_file'.$urls),
        ))),
    array(array('menu' => $toolslang['aboutucenter'], 'submenu' => array(
        array($toolslang['clrnotice'], 'plugins&cp=aboutucenter&mod=clrnotice'.$urls),
        array($toolslang['clrfeed'], 'plugins&cp=aboutucenter&mod=clrfeed'.$urls),
        array($toolslang['synusername'], 'plugins&cp=aboutucenter&mod=synusername'.$urls),
        array($toolslang['synuid'], 'plugins&cp=aboutucenter&mod=synuid'.$urls),
        array($toolslang['uc_pm'], 'plugins&cp=aboutucenter&mod=uc_pm'.$urls),
        array($toolslang['uc_avator'], 'plugins&cp=aboutucenter&mod=uc_avator'.$urls),
        ))),
));
$cparray = array('aboutdb', 'clean_att', 'aboutucenter', 'amend', 'cleannote', 'repauto', 'check_file',);
$cp = !in_array($_GET['cp'], $cparray) ? 'aboutdb' : $_GET['cp'];
require TOOLS_ROOT.'./include/'.$cp.'.inc.php';

?>