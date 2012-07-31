<?php

/**
 *      [Discuz! X] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: smstong.class.php 291 2011-06-01 17:06:31Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_smstong {

	var $allow = false;

	function plugin_smstong() {
		global $_G;
		include_once template('smstong:module');
		if(!$_G['cache']['plugin']['smstong']) {
			return;
		}
		$this->allow = true;
	}

	function global_login_mobile_autoselect() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		if($_G['cache']['plugin']['smstong']['mobilelog']) {
			return tpl_global_login_mobile_autoselect();
		}

		return;
	}

	function global_footer(){
		global $_G;

		if(!$this->allow) {
			return;
		}
		
		require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');
		$data = DB::fetch_first("SELECT mobile FROM ".DB::table("common_member_profile")." WHERE uid = $_G[uid]");
		
		if($_G['cache']['plugin']['smstong']['reportmsgnotify'] && $_G['gp_mod'] == 'report' && $_G['gp_reportsubmit']){
			$content = $_G['cache']['plugin']['smstong']['reportmsgnotifymsg'];
			$rp = array('$username', '$tid', '$message');
			$sm = array($_G['username'], $_G['gp_rid'], $_G['gp_message']);
			$content = str_replace($rp, $sm, $content);

			$arraymobile = explode(',', $_G['cache']['plugin']['smstong']['reportmsgnotifymobile']);
			foreach($arraymobile as $mobile){
				$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);
			}
		}
		elseif($_G['cache']['plugin']['smstong']['loggingmsgnotify'] && ismobile($data['mobile']) && $_G['cookie']['loginmark'] <> $_G['cookie']['lastvisit']){
			dsetcookie('loginmark', $_G['cookie']['lastvisit']);

			require_once(DISCUZ_ROOT.'./source/function/function_misc.php');

			$content = $_G['cache']['plugin']['smstong']['loggingmsgnotifymsg'];
			$rp = array('$username', '$logtime', '$ipaddress', '$location');
			$sm = array($_G['username'], date('Y-m-d H:i:s', TIMESTAMP), $_G['clientip'], str_replace('-','',str_replace(' ','',convertip($_G['clientip']))));
			$content = str_replace($rp, $sm, $content);

			$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $data['mobile'], $content);
		}
	}
}

class plugin_smstong_member extends plugin_smstong {

	function register_input_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}
		
		if($_G['cache']['plugin']['smstong']['mobilereg']) {
			return tpl_register_input_output();
		}

		return;
	}

	function logging_input_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}
		
		if($_G['cache']['plugin']['smstong']['mobilelog']) {
			return tpl_logging_input();
		}

		return;
	}

}

class plugin_smstong_home extends plugin_smstong {

	function spacecp_profile_bottom_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		if($_G['cache']['plugin']['smstong']['mobilebind']) {
			return tpl_spacecp_profile_bottoms();
		}

		return;
	}

}

class plugin_smstong_forum extends plugin_smstong {

	function index_bottom_output() {
		global $_G;

		if(!$this->allow) {
			return;
		}

		$data = DB::fetch_first("SELECT mobile FROM ".DB::table("common_member_profile")." WHERE uid = $_G[uid]");
		
		require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

		if($_G['uid'] && $_G['cache']['plugin']['smstong']['displaymobilecons'] && !ismobile($data['mobile'])) {
			return tpl_index_bottom_output();
		}

		return;
	}

	function viewthread_avatar_output() {
		global $_G,$postlist;

		if(!$this->allow) {
			return;
		}

		if($_G['cache']['plugin']['smstong']['displaythreadmobile']) {

			foreach ($postlist as $id=>$post) {
				if ($post['authorid']) {
					$target .= $post['authorid'].',';
				}
			}

			$target = substr($target, 0, -1);

			if (!empty($target)) {
				$query = DB::query("SELECT * FROM ".DB::table("common_member_profile")." WHERE uid in ($target)");
					while ($data = DB::fetch($query)) {
						$user[$data['uid']] = $data;
				}

				include_once DISCUZ_ROOT . './data/plugindata/smstong.lang.php';
				require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

				foreach($user as $uid=>$ex) {
					if (ismobile($user[$uid]['mobile'])) {
						$mobile[$uid] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='source/plugin/smstong/mobile.gif' title='".lang('plugin/smstong', 'smstong_mobilebind_bindimage')."' />";
					}
				}

				foreach($postlist as $id=>$post) {
					$return[] = $mobile[$post['uid']]; 
				}

				return $return;
			}

			return;
		}

		return;
	}

}

?>