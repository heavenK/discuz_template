<?php
If(!defined('IN_DISCUZ')) {
    Exit('Access Deined');
}

@include_once DISCUZ_ROOT . '/data/cache/bshare.inc';
@include_once DISCUZ_ROOT . '/data/cache/bshare.show.inc';

global $_G;
$defaultTab = '0';
$defaultOption = 'C1:false:x';
$defaultStyles = '<div class="bshare-custom"><a title="分享到QQ空间" class="bshare-qzone"></a><a title="分享到新浪微博" class="bshare-sinaminiblog"></a><a title="分享到人人网" class="bshare-renren"></a><a title="分享到腾讯微博" class="bshare-qqmb"></a><a title="分享到豆瓣" class="bshare-douban"></a><a title="更多平台" class="bshare-more bshare-more-icon"></a><span class="BSHARE_COUNT bshare-share-count">0</span></div><script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/buttonLite.js#style=-1&uuid=&pophcol=2&lang=zh"></script><script type="text/javascript" charset="utf-8" src="http://static.bshare.cn/b/bshareC0.js"></script>';

// Init the style variables
if (!isset($cfg_bshare['tab']) || $cfg_bshare['tab'] == '') {
    writebShareCache($cfg_bshare['uuid'], $cfg_bshare['user'], $cfg_bshare['pwd'], $cfg_bshare['sk'], $defaultTab, $defaultOption, $defaultStyles);
}
if (!isset($cfg_bshare['option']) || $cfg_bshare['option'] == '') {
    writebShareCache($cfg_bshare['uuid'], $cfg_bshare['user'], $cfg_bshare['pwd'], $cfg_bshare['sk'], $cfg_bshare['tab'], $defaultOption, $cfg_bshare['styles']);
}
if (!isset($cfg_bshare['styles']) || $cfg_bshare['styles'] == '') {
    writebShareCache($cfg_bshare['uuid'], $cfg_bshare['user'], $cfg_bshare['pwd'], $cfg_bshare['sk'], $cfg_bshare['tab'], $cfg_bshare['option'], $defaultStyles);
}

if ($_G['gp_mod'] == 'openShare') {
	$user = $_G['gp_user'];
	$pwd = $_G['gp_pwd'];
	$submode = $_G['gp_submode'];
	$submode = isset($submode) ? $submode : 0;
	if (empty($user)) {
		showmessage(lang('plugin/bshare', 'message1'), HTTP_REFERER);
		exit;
	}
	if (empty($pwd)) {
		showmessage(lang('plugin/bshare', 'message2'), HTTP_REFERER);
		exit;
	}

	$openUrl = "http://api.bshare.cn/analytics/reguuid.json?email={$user}&password={$pwd}&domain={$_SERVER['HTTP_HOST']}&source=discuz";;

	if (!function_exists('curl_init')) {
		cpmsg(lang('plugin/bshare', 'message3'), "action=plugins&operation=config&do=$pluginid");
	}

	$result = doGet($openUrl);

	$json = json_decode($result['response'], true);
	$uuid = $json['uuid'];
	$sk = $json['secret'];
	if (!isset($json) || $json == '') {
		// Error processing
		if ($result['code'] == 400) {
			cpmsg(lang('plugin/bshare', 'message4'), "action=plugins&operation=config&do=$pluginid");
			exit;
		} else if ($result['code'] == 401) {
			if ($submode == 0)
				cpmsg(lang('plugin/bshare', 'message5'), "action=plugins&operation=config&do=$pluginid");
			cpmsg(lang('plugin/bshare', 'message6'), "action=plugins&operation=config&do=$pluginid");
			exit;
		}
	}

	if (!isset($uuid) || $uuid == ' ') {
		cpmsg(lang('plugin/bshare', 'message7'), "action=plugins&operation=config&do=$pluginid");
		exit;
	}

	$styles = insertUuid($uuid, $cfg_bshare['styles']);

	// Save cache file
	writebShareCache($uuid, $user, $pwd, $sk, $cfg_bshare['tab'], $cfg_bshare['option'], $styles);
	writebShareShows('1', '1');

	if (!isset($cfg_bshare) || $cfg_bshare == '')
		cpmsg(lang('plugin/bshare', 'message8'), "action=plugins&operation=config&do=$pluginid");

	cpmsg(lang('plugin/bshare', 'message9'), "action=plugins&operation=config&do=$pluginid");
	exit;

} else if ($_G['gp_mod'] == 'switchShare') {
	writebShareCache('', '', '', '', $cfg_bshare['tab'], $cfg_bshare['option'], $cfg_bshare['styles']);
	cpmsg(lang('plugin/bshare', 'message10'), "action=plugins&operation=config&do=$pluginid");

} else if ($_G['gp_mod'] == 'setStyles') {
	$tab = stripslashes($_G['gp_tab']);
	$option = stripslashes($_G['gp_option']);
	$styles = stripslashes($_G['gp_bscode']);

	if (!isset($styles) || $styles == '') {
		showmessage(lang('plugin/bshare', 'message11'), HTTP_REFERER);
		exit;
	}

	writebShareCache($cfg_bshare['uuid'], $cfg_bshare['user'], $cfg_bshare['pwd'], $cfg_bshare['sk'], $tab, $option, $styles);
	$styles = $cfg_bshare['styles'];
	if (!isset($styles) || $styles == '') {
		showmessage(lang('plugin/bshare', 'message12'), HTTP_REFERER);
	}
    $temp_position = stripslashes($cfg_show['position']) ;
	$temp_blog = stripslashes($cfg_show['blog']) ;
	writebShareShows($temp_position, $temp_blog);
	
	cpmsg(lang('plugin/bshare', 'message13'), "action=plugins&operation=config&do=$pluginid");
} else if ($_G['gp_mod'] == 'setShow') {
	$position = stripslashes($_G['gp_position']) ;
	$blog = stripslashes($_G['gp_blog']) ;
	writebShareShows($position, $blog);
	cpmsg(lang('plugin/bshare', 'message14'), "action=plugins&operation=config&do=$pluginid");
	exit;
}
include template('bshare:bshare');

function insertUuid($uuid, $styles) {
	// Insert UUID
	// 1. No action if no uuid cached in session
	// 2. When there's uuid in session, and the uuid in style code is empty, assgin the value in session to the style code
	if (isset($uuid) && $uuid != '') {
		if (strpos($styles, 'uuid=') === false) {
			$styles = str_replace('js#', 'js#uuid=' . $uuid . '&amp;', $styles);
		} else if (strpos($styles, 'uuid=&') !== false) {
			$styles = str_replace('uuid=&', 'uuid=' . $uuid . '&', $styles);
		} else if (strpos($styles, 'uuid="') !== false) {
			$styles = str_replace('uuid="', 'uuid=' . $uuid . '"', $styles);
		}
	}
	return $styles;
}

function writebShareCache($uuid, $user, $pwd, $sk, $tab, $option, $styles) {
	// Write bShare cache file
    $cacheFile = DISCUZ_ROOT . '/data/cache/bshare.inc';
    $cacheStr = <<<EOT
<?php 
If (!defined('IN_DISCUZ')) {
    Exit('Access Deined');
}
global \$cfg_bshare;
\$cfg_bshare = array();
\$cfg_bshare['uuid'] = '$uuid';
\$cfg_bshare['user'] = '$user';
\$cfg_bshare['pwd'] = '$pwd';
\$cfg_bshare['sk'] = '$sk';
\$cfg_bshare['tab'] = '$tab';
\$cfg_bshare['option'] = '$option';
\$cfg_bshare['styles'] = '$styles';
?>
EOT;
    return file_put_contents($cacheFile, $cacheStr);
}

function writebShareShows($bshare_position = '', $bshare_blog = '') {
	// Write bShare show cache file
    $cacheFile = DISCUZ_ROOT . '/data/cache/bshare.show.inc';
    $cacheStr = <<<EOT
<?php 
If(!defined('IN_DISCUZ')) {
        Exit('Access Deined');
}
global \$cfg_show;
\$cfg_show = array();
\$cfg_show['position'] = '$bshare_position';
\$cfg_show['blog'] = '$bshare_blog';
?>
EOT;
    file_put_contents($cacheFile, $cacheStr);
}

function doGet($url) {
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLINFO_HEADER_OUT, true);
    curl_setopt($handle, CURLOPT_VERBOSE, true);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($handle, CURLOPT_UNRESTRICTED_AUTH, true);
    $response = curl_exec($handle);
    $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    return Array('code' => $code, 'response' => $response);
}
?>