<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: notify.inc.php 18582 2012-03-12 12:13:25Z Ñ½Ñ½¸öÅÞ $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
define('NOROBOT', TRUE);

$sender = $_G['gp_sender'];
$content = $_G['gp_content'];
$receivetime = $_G['gp_receivetime'];
$key = $_G['gp_key'];
$type = $_G['gp_type'];

loadcache('plugin');

if ($_G['cache']['plugin']['smstong']['key'] == $key) {

	require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

	DB::query("INSERT INTO sms_recv (mobile,content,recvtime,port,senttime,remark) values('$sender', '$content', '".date('Y-m-d H:i:s', TIMESTAMP)."', '$type', '".$receivetime."','')");

	if ($type == "1") {

		$record = DB::fetch_first("SELECT id,remark FROM sms_send WHERE mobile='$sender' and remark <> '' ORDER BY id ASC LIMIT 1");

		if(!empty($record['remark'])) {
			$need = explode(",", $record['remark']);

			$reply = DB::fetch_first('select fid,tid,subject,authorid from '.DB::table('forum_post')." where pid=$need[0]");
			if(!$reply){
				echo '';
				exit();
			}
			$fid = $reply['fid'];
			$tid = $reply['tid'];
			
			$post = DB::fetch_first('select fid,tid,subject from '.DB::table('forum_thread')." where tid='$tid'");
			if(!$post){
				echo '';
				exit();
			}
			
			$user = DB::fetch(DB::query("SELECT username FROM ".DB::table('common_member'). " WHERE uid=$need[1]"));
			if(!$user){
				echo '';
				exit();
			}

			require_once libfile('function/post');
			require_once libfile('function/forum');
			$pid = insertpost(array(
				'fid' => $fid,
				'tid' => $tid,
				'first' => 0,
				'author' => $user['username'],
				'authorid' => $need[1],
				'subject' => '',
				'dateline' => TIMESTAMP,
				'message' => $content,
				'useip' => $_G['clientip'],
				'invisible' => 0,
				'anonymous' => 0,
				'usesig' => 1,
				'htmlon' => 0,
				'bbcodeoff' => 0,
				'smileyoff' => 0,
				'parseurloff' => 0,
				'attachment' => '0',
				'replycredit' => 0,
				'status' => 0)
			);	
			
			$expiration = $_G['timestamp'] + 86400;
			DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$user[username]', lastpost='$_G[timestamp]', replies=replies+1 WHERE tid='$tid' AND fid='$fid'", 'UNBUFFERED');
			$lastpost = "$tid\t".addslashes(cutstr($post['subject'], 60, ''))."\t$_G[timestamp]\t$user[username]";
			DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost' $update, posts=posts+1, todayposts=todayposts+1 WHERE fid='$fid'", 'UNBUFFERED');
			updatepostcredits('+', $need[1], 'reply', $fid);
			
			$exists = DB::fetch(DB::query("SELECT mobile FROM ".DB::table('common_member_profile')." WHERE uid=$reply[authorid]"));

			if($_G['cache']['plugin']['smstong']['replynotify'] == 1 && ismobile($exists['mobile'])) {
					
				$contents = $_G['cache']['plugin']['smstong']['replynotifymsg'];
				$rp = array('$username', '$subject', '$content');
				$sm = array($user['username'], $post['subject'], htmlspecialchars(messagecutstr($content, 100)));
				$contents = str_replace($rp, $sm, $contents);

				$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $exists['mobile'], $contents, false, $pid.",".$reply['authorid']);
				
				if($ret === TRUE)
				{ }
				else
				{
					showmessage('smstong:smstong_activitymessage_sendsms_failured', '', array('ret' => $ret));
				}
			}

			DB::query("UPDATE sms_send SET remark='' WHERE id = ".$record['id']."");
		} else {
			echo '';
			exit();
		}
	} elseif ($type == "2") {
		$record = DB::fetch_first("SELECT id,remark FROM sms_send WHERE mobile='$sender' and remark <> '' ORDER BY id ASC LIMIT 1");

		if(!empty($record['remark'])) {
			$need = explode(",", $record['remark']);
			sendpm($need[0], '', $content, $need[1]);

			DB::query("UPDATE sms_send SET remark='' WHERE id = ".$record['id']."");
		}
		else {
			echo '';
			exit();
		}
	}

	echo 'ok';
}
else {
	echo '';
}

?>