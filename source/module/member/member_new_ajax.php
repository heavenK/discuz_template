<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: new_ajax.php 30465 2012-05-30 04:10:03Z zh $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('NOROBOT', TRUE);
if($_GET['action']=='chknickname'){
	$nickname = trim($_GET['nickname']);
	$pap=new passport();
	$status=$pap->nickname_check($nickname);
	if($status==false){
		showmessage('nickname_exist', '', array(), array('handle' => false));
	}
}
showmessage('succeed', '', array(), array('handle' => false));
?>