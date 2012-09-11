<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: home_spacecp.php 28214 2012-02-24 06:38:56Z zhengqingpeng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once libfile('function/spacecp');
require_once libfile('function/magic');

$acs = array('space', 'doing', 'upload', 'comment', 'blog', 'album', 'relatekw', 'common', 'class',
	'swfupload', 'poke', 'friend', 'eccredit', 'favorite', 'follow',
	'avatar', 'profile', 'theme', 'feed', 'privacy', 'pm', 'share', 'invite','sendmail',
	'credit', 'usergroup', 'domain', 'click','magic', 'top', 'videophoto', 'index', 'plugin', 'search', 'promotion');

$_GET['ac'] = $ac = (empty($_GET['ac']) || !in_array($_GET['ac'], $acs))?'profile':$_GET['ac'];
$op = empty($_GET['op'])?'':$_GET['op'];
$_G['mnid'] = 'mn_common';


if(empty($_G['uid'])) {
	if($_SERVER['REQUEST_METHOD'] == 'GET') {
		dsetcookie('_refer', rawurlencode($_SERVER['REQUEST_URI']));
	} else {
		dsetcookie('_refer', rawurlencode('home.php?mod=spacecp&ac='.$ac));
	}
	showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
}

$space = getuserbyuid($_G['uid']);
if(empty($space)) {
	showmessage('space_does_not_exist');
}
space_merge($space, 'field_home');

if(($space['status'] == -1 || in_array($space['groupid'], array(4, 5, 6))) && $ac != 'usergroup') {
	showmessage('space_has_been_locked');
}

$actives = array($ac => ' class="a"');

$seccodecheck = $_G['group']['seccode'] ? $_G['setting']['seccodestatus'] & 4 : 0;
$secqaacheck = $_G['group']['seccode'] ? $_G['setting']['secqaa']['status'] & 2 : 0;

$navtitle = lang('core', 'title_setup');
if(lang('core', 'title_memcp_'.$ac)) {
	$navtitle = lang('core', 'title_memcp_'.$ac);
}

$_G['disabledwidthauto'] = 0;

//add by kaiser
$kaiser_user = C::t('common_member')->fetch_all_by_username($_G['username']);
foreach($kaiser_user as $val){
	$renzheng = $val['extgroupids'];
}
//end add
//add by zh
space_merge($space, 'profile');
space_merge($space, 'count');
//$data=C::t('common_usergroup')->findgroupid_by('认证会员','','',0,0,'icon');
if($_G['member']['groupid']==22){
	$space['vertifyico']='static/image/common/kaiser_ext.png';
}

//add
$followerlist = C::t('home_follow')->fetch_all_following_by_uid($_G['uid']);
foreach($followerlist as $k=>$val){
	if($val['followuid']==$uid){
		$gzflag=true;
		break;
	}
}
//end add
require_once libfile('spacecp/'.$ac, 'include');

?>