<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: changekey.inc.php 79 2012-04-16 10:06:12Z wangbin $
 */

(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) && exit('Access Denied');
if(submitcheck('keysubmit')){
    $cpmessage = '';
    $localuc = 0;
    if(file_exists(DISCUZ_ROOT.'./uc_server/data/config.inc.php')){
    	require_once DISCUZ_ROOT.'./uc_server/data/config.inc.php';
    	$localuc = 1;
    }
    @loaducenter();
    //require_once DISCUZ_ROOT.'./uc_client/client.php';
    
    $key = array('uc_key','config_authkey','setting_authkey','my_sitekey'); // UCenter通信KEY   Discuz! 安全KEY  Discuz!加密解密key  漫游KEY
    foreach($key as $value){
    	if($value == 'uc_key'){
    		//echo $localuc;exit;
    		if(strexists(UC_API,$_G['siteurl']) && $localuc == 1){ //local ucenter
    			$newuc_mykey = UC_MYKEY;              //更新到UCenter配置文件
    			$newuc_uckey = UC_KEY;            //更新到UCenter配置文件
    			$newapp_authkey = generate_key();           //更新到 Discuz! UC配置文件
    			$newapp_appkey = authcode($newapp_authkey,'ENCODE',$newuc_mykey);   //更新到UCenter数据库
    			$newapp_appkey = daddslashes($newapp_appkey);
    			//echo $newcu_mykey;exit;
    			$ucdb = new db_mysql();
    			$ucdblink = $ucdb->_dbconnect(UC_DBHOST,UC_DBUSER,UC_DBPW,UC_DBCHARSET,UC_DBNAME);
    			$apptablename = UC_DBTABLEPRE.'applications';
    //					$a = $ucdb->query("SELECT appid,authkey FROM $apptablename");
    //					$apparray = array();
    //					while($data = $ucdb->fetch_array($a)){
    //						$apparray[] = $data;
    //					}
    			//echo UC_DBTABLEPRE;exit;
    			$uc_dbtablepre = UC_DBTABLEPRE;
    			$ucconfig = array($newapp_authkey,UC_APPID,UC_DBHOST,UC_DBNAME,UC_DBUSER,UC_DBPW,UC_DBCHARSET,$uc_dbtablepre,UC_CHARSET,UC_API,UC_IP);
    			$ucconfig = @implode('|',$ucconfig);
    			save_uc_config($ucconfig,DISCUZ_ROOT.'./config/config_ucenter.php');
    			$ucdb->query("UPDATE $apptablename SET authkey = '$newapp_appkey' WHERE appid = ".UC_APPID);
    			//note
    		} else {
    			$cpmessage .= $toolslang['nlocaluc'];
    		}
    		// $authkey = substr(md5($_SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT'].$dbhost.$dbuser.$dbpw.$dbname.$username.$password.$pconnect.substr($timestamp, 0, 6)), 8, 6).random(10);	
    	} elseif($value == 'config_authkey') {
    		$default_config = $_config;
    		$authkey = substr(md5($_SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT'].$dbhost.$dbuser.$dbpw.$dbname.$username.$password.$pconnect.substr($timestamp, 0, 8)), 8, 6).random(10);
    		$_config['security']['authkey'] = $authkey;
    		$cpmessage .= $toolslang['resetauthkey'];
    		save_config_file('./config/config_global.php', $_config, $default_config);
    	} elseif($value == 'setting_authkey') {
    		$authkey = substr(md5($_SERVER['SERVER_ADDR'].$_SERVER['HTTP_USER_AGENT'].$dbhost.$dbuser.$dbpw.$dbname.$username.$password.$pconnect.substr($timestamp, 0, 8)), 8, 6).random(10);
    		DB::update('common_setting',array('svalue' => $authkey),"skey = 'authkey'");
    	} elseif($value == 'my_sitekey' && $xver >= 2) {
    		require_once DISCUZ_ROOT.'/api/manyou/Manyou.php';
    		$cloudClient = new Discuz_Cloud_Client();
    		$res = $cloudClient->resetKey();
    		if(!$res) {
    			$cpmessage .= $toolslang['mykeyerror'];
    		} else {
    			$sId = $res['sId'];
    			$sKey = $res['sKey'];
    			DB::query("REPLACE INTO ".DB::table('common_setting')." (`skey`, `svalue`)
    						VALUES ('my_siteid', '$sId'), ('my_sitekey', '$sKey'), ('cloud_status', '1')");
    		}
    	}
    }
    updatecache('setting');
    cpmsg($toolslang['changekey_update'].$cpmessage,"action=plugins&cp=file_changekey&pmod=safe&operation=$operation&do=$do&identifier=$identifier",'succeed');
}
loaducenter();
showformheader("plugins&cp=file_changekey&pmod=safe&operation=$operation&do=$do&identifier=$identifier");
showtipss($toolslang['changekey_tips']);
showtableheaders($toolslang['changekey']);
$uckey = substr(UC_KEY,0,5).'**********';
$config_authkey = substr($_config['security']['authkey'],0,5).'**********';
$setting_authkey = substr($_G[setting][authkey],0,5).'**********';
$my_sitekey = substr($_G[setting][my_sitekey],0,5).'**********';
showtablerow('','',$toolslang['nowuc_key'].' : '.$uckey);
showtablerow('','',$toolslang['nowconfig_authkey'].' : '.$config_authkey);
showtablerow('','',$toolslang['nowmy_sitekey'].' : '.$my_sitekey);
showsubmit('keysubmit',$toolslang['changekey']);
showtablefooter();
?>