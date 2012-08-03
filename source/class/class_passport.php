<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class passport{
	var $version = '';
	var $querynum = 0;
	var $link;

	function passport(){
		$dbhost = '192.168.0.9';			// 数据库服务器
		$dbuser = 'user1';			// 数据库用户名
		$dbpw = '123';				// 数据库密码
		$dbname = 'bbsnew';			// 数据库名
		$pconnect = 0;				// 数据库持久连接 0=关闭, 1=打开
		$tablepre = 'passport';   		// 表名前缀, 同一数据库安装多个论坛请修改此处
		$dbcharset = 'gbk';			// MySQL 字符集, 可选 'gbk', 'big5', 'utf8', 'latin1', 留空为按照论坛字符集设定
	    self::connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
	}
	function connect($dbhost, $dbuser, $dbpw, $dbname = '', $pconnect = 0, $halt = TRUE) {
		$func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
		if(!$this->link = @$func($dbhost, $dbuser, $dbpw)) {
			$halt && self::halt('Can not connect to MySQL server');
		} else {
			if(self::version() > '4.1') {
				global $charset, $dbcharset;
				$dbcharset = !$dbcharset && in_array(strtolower($charset), array('gbk', 'big5', 'utf-8')) ? str_replace('-', '', $charset) : $dbcharset;
				$serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client='.$dbcharset : '';
				$serverset .= self::version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
				$serverset && mysql_query("SET $serverset", $this->link);
			}
			mysql_query("set names gbk");

			$dbname && @mysql_select_db($dbname, $this->link);
		}

	}

	function useradd($uid, $username, $password, $email, $ip, $groupid, $extdata, $adminid = 0){
		self::passport();
		$searchsql="select * from passport_user where username='".$username."'";
		$searchrs=mysql_query($searchsql);
		$row=mysql_fetch_array($searchrs);
		if(!empty($row)){
			return "userisexist";
			
		}else{
			$profile = isset($extdata['profile']) ? $extdata['profile'] : array();
			$year=!empty($profile['birthyear'])?$profile['birthyear']:0000;
			$month=!empty($profile['birthmonth'])?$profile['birthmonth']:00;
			if($month<0){
				$month=sprintf('%02d',$month);
			}
			$day=!empty($profile['birthday'])?$profile['birthday']:00;
			if($day<0){
				$day=sprintf('%02d',$day);
			}
			$userdata = array("uid"=>$uid,
							"username"=>trim((string)$username),
							"nickname"=>!empty($profile['field1'])?$profile['field1']:(string)$username,
							"password"=>md5((string)$password),
							"gender"=>!empty($profile['gender'])?$profile['gender']:0,				//性别
							"adminid"=>$adminid,
							"regip"=>(string)$ip,
							"regdate"=>TIMESTAMP,
							"lastip"=>(string)$ip,
							"lastvisit"=>TIMESTAMP,
							"oltime"=>0,
							"xingzuo"=>!empty($profile['constellation'])?$profile['constellation']:null,
							"email"=>(string)$email,
							"zhiye"=>!empty($profile['occupation'])?$profile['occupation']:null,
							"biaoqian"=>!empty($profile['field2'])?$profile['field2']:null,
							"bday"=>$year."-".$month."-".$day,
							"fin_account"=>0,
							"trade_password"=>md5((string)$password)
				);
		//	$sql="INSERT INTO passport_user(`uid`,`username`,`nickname`,`password`,`gender`,`adminid`,`regip`,`regdate`,`lastip`,`lastvisit`,`oltime`,`xingzuo`,`email`,`zhiye`,`biaoqian`,`bday`,`fin_account`,`trade_password`) VALUES('".$userdata['uid']."','".$userdata['username']."','".$userdata['nickname']."','".$userdata['password']."','".$userdata['gender']."','".$userdata['adminid']."','".$userdata['regip']."','".$userdata['regdate']."','".$userdata['lastip']."','".$userdata['lastvisit']."','".$userdata['oltime']."','".$userdata['xingzuo']."','".$userdata['email']."','".$userdata['zhiye']."','".$userdata['biaoqian']."','".$userdata['bday']."','".$userdata['fin_account']."','".$userdata['trade_password']."')";
			$sql="INSERT INTO passport_user(`username`,`nickname`,`password`,`gender`,`adminid`,`regip`,`regdate`,`lastip`,`lastvisit`,`oltime`,`xingzuo`,`email`,`zhiye`,`biaoqian`,`bday`,`fin_account`,`trade_password`) VALUES('".$userdata['username']."','".$userdata['nickname']."','".$userdata['password']."','".$userdata['gender']."','".$userdata['adminid']."','".$userdata['regip']."','".$userdata['regdate']."','".$userdata['lastip']."','".$userdata['lastvisit']."','".$userdata['oltime']."','".$userdata['xingzuo']."','".$userdata['email']."','".$userdata['zhiye']."','".$userdata['biaoqian']."','".$userdata['bday']."','".$userdata['fin_account']."','".$userdata['trade_password']."')";
			$rs=mysql_query($sql);
			if($rs){
				return $userdata;
			}else{
				return "datawrong";
				
			}
		}
		
		
	}
	function passport_setsession($DZuser,$cookietime,$lastip){
		self::passport();
		$searchsql="select * from passport_user where username='".$DZuser['username']."' limit 1";
		$rs=mysql_query($searchsql);
		$passportdata=mysql_fetch_array($rs);
		if(!empty($passportdata)){
			$theuser=new stdClass();
			$theuser->uid=$passportdata['uid'];
			$theuser->username=$passportdata['username'];
			$theuser->telnum=$passportdata['telnum'];
			$theuser->nickname=$passportdata['nickname'];
			$theuser->password='';
			$theuser->gender=$passportdata['gender'];
			$theuser->adminid=$passportdata['adminid'];
			$theuser->regip=$passportdata['regip'];
			$theuser->regdate=$passportdata['regdate'];
			$theuser->lastip=$lastip;
			$theuser->lastvisit=TIMESTAMP;
			$theuser->oltime=$passportdata['oltime'];
			$theuser->fin_account=$passportdata['fin_account'];
			$theuser->email=$passportdata['email'];
			$theuser->bday=$passportdata['bday'];
			$theuser->trade_password=$passportdata['trade_password'];
			$theuser->groupid=$DZuser['groupid'];
			$theuser->extgroupids=$DZuser['extgroupids'];
			$theuser->cookietime=$cookietime;
			$theuser->time=TIMESTAMP;
			
			$_SESSION['Zend_Auth']=array('storage'=>$theuser);
			return true;
		}else{
			return false;
		}
	}
	function logout(){
		if(isset($_SESSION['Zend_Auth'])){
			$_SESSION['Zend_Auth']['storage'] = '';
			unset($_SESSION['Zend_Auth']);
		}
	    
	}

	function version() {
		if(empty($this->version)) {
			$this->version = mysql_get_server_info($this->link);
		}
		return $this->version;
	}

	function halt($message = '', $sql = '') {
		echo 'SQL Error:<br />'.$message.'<br />'.$sql;
	}
	function url_sms($url=''){
		global $_G;
		$_G['referer'] = !empty($_GET['referer']) ? $_GET['referer'] : $_SERVER['HTTP_REFERER'];
		$_G['referer'] = dhtmlspecialchars($_G['referer'], ENT_QUOTES);
		$_G['referer'] = str_replace('&amp;', '&', $_G['referer']);

		$reurl = parse_url($_G['referer']);
		if(!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.'.$_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.'.$reurl['host']))) {
			if(empty($url)){
				$_G['referer']='http://'.$reurl['host']."/member.php?mod=sms";
			}
		} elseif(empty($reurl['host'])) {
			$_G['referer'] = $_G['siteurl'].'./'.$_G['referer'];
		}
		if(empty($url)){
			$_G['referer']='http://'.$reurl['host']."/member.php?mod=sms&action=bindmobile";
		}else{
			$_G['referer']='http://'.$reurl['host']."/".$url;
		}
		return strip_tags($_G['referer']);
	}
	function p_allowverify(){
	    global $_G;

		if(empty($_G['setting']['verify'])) {
			loadcache('setting');
		}
		$allow = false;
		$vid = 0 < $vid && $vid < 8 ? intval($vid) : 0;
		if($vid) {
			$setting = $_G['setting']['verify'][$vid];
			if($setting['available'] && (empty($setting['groupid']) || in_array($_G['groupid'], $setting['groupid']))) {
				$allow = true;
			}
		} else {
			foreach($_G['setting']['verify'] as $key => $setting) {
				if($setting['available'] && (empty($setting['groupid']) || in_array($_G['groupid'], $setting['groupid']))) {
					$allow = true;
					break;
				}
			}
		}
		return $allow;
	}
	

}


class sms_send{
		
	function sms_send() {
		global $_G;
		if($_G['setting']['bbclosed']) {
			if(($_GET['action'] != 'activation' && !$_GET['activationauth']) || !$_G['setting']['closedallowactivation'] ) {
				showmessage('register_disable', NULL, array(), array('login' => 1));
			}
		}

		loadcache(array('modreasons', 'stamptypeid', 'fields_required', 'fields_optional', 'fields_register', 'ipctrl', 'plugin'));
		require_once libfile('function/misc');
		require_once libfile('function/profile');
		if(!function_exists('sendmail')) {
			include libfile('function/mail');
		}
		loaducenter();
	}
	function on_sms(){
		global $_G;
		
		require_once(DISCUZ_ROOT.'./source/plugin/smstong/smstong.func.php');

		if(empty($_G['uid'])){
			showmessage('to_login', '', array(), array('showmsg' => true, 'login' => 1));
		}elseif(!$this->setting['regclosed'] && (!$this->setting['regstatus'] || !$this->setting['ucactivation'])) {
			if($_GET['action'] == 'activation' || $_GET['activationauth']) {
				if(!$this->setting['ucactivation'] && !$this->setting['closedallowactivation']) {
					showmessage('register_disable_activation');
				}
			} elseif(!$this->setting['regstatus']) {
				showmessage(!$this->setting['regclosemessage'] ? 'register_disable' : str_replace(array("\r", "\n"), '', $this->setting['regclosemessage']));
			}
		}elseif($_G['uid']) {
			$ucsynlogin = $this->setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';
			$exists = DB::result_first("SELECT mobile FROM ".DB::table('common_member_profile')." WHERE uid='".trim($_G['uid'])."'");
			if($exists){
				$url_forward = dreferer();
				$url_forward = 'forum.php';
				showmessage('login_succeed', $url_forward ? $url_forward : './', array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle'], 'uid' => $_G['uid']), array('extrajs' => $ucsynlogin));
			}
			
		}

		$regname = 'sms';

		$groupinfo = array();
		if($this->setting['regverify']) {
			$groupinfo['groupid'] = 8;
		} else {
			$groupinfo['groupid'] = $this->setting['newusergroupid'];
		}
		$seccodecheck = $this->setting['seccodestatus'] & 1;
		$secqaacheck = $this->setting['secqaa']['status'] & 1;

		if($_GET['action'] == 'bindmobile') {

			if(submitcheck('bindmobilesubmit', 0, 0, 0)) {

				if($_GET['formhash'] != $_G['formhash']) {
					showmessage('submit_invalid', '', array(), array('handle' => false));
				}

				if($_G['cache']['plugin']['smstong']['openverifycode'] == 1) {
					require_once libfile('function/seccode');
					if(!check_seccode($_GET['seccodeverify'], $_GET['idhash'])){
						showmessage('smstong:smstong_mobilereg_verifycode_seccode', '', array(), array('handle' => false));
					}
				}
				
				$mobile = trim($_GET['newmobile']);
				$verifycode = trim($_GET['verifycode']);

				if($_G['cache']['plugin']['smstong']['mobilebind'] == 0) {
					showmessage('smstong:smstong_mobilebind_closed');
				}

				if(empty($mobile)) {
					showmessage('smstong:smstong_mobilebind_mobile_empty');
				}
				
				if(!ismobile($mobile)) {
					showmessage('smstong:smstong_mobilereg_mobile_invalid');
				}
				elseif ($_POST['flag'] == "2") {

					$query = DB::query("SELECT mobile FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
					if($value = DB::fetch($query)) 
					{
						if(!empty($value['mobile']))
						{
							showmessage('smstong:smstong_mobilebind_mobile_exists');
						}
					}

					$mobilegap = $_G['cache']['plugin']['smstong']['mobilegap'];
					$sended = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE (status=3 or status=4) AND getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-$mobilegap");
					
					if ($sended)
					{
						showmessage('smstong:smstong_mobilereg_mobile_sended', '', array('mobilegap' => $mobilegap));
					}
					else
					{
						$verifycode = rand(100000,999999);

						$verifycode = str_replace('1989','9819',$verifycode);
						$verifycode = str_replace('1259','9521',$verifycode);
						$verifycode = str_replace('12590','09521',$verifycode);
						$verifycode = str_replace('10086','68001',$verifycode);

						$content = $_G['cache']['plugin']['smstong']['mobilebindmsg'];
						$rp = array('$mobile', '$verifycode');
						$sm = array($mobile, $verifycode);
						$content = str_replace($rp, $sm, $content);

						$ret = sendsms($_G['cache']['plugin']['smstong']['smsusername'], $_G['cache']['plugin']['smstong']['smspassword'], $mobile, $content);

						if($ret === true)
						{
							$verifycode_data = array(
							'mobile' => $mobile,
							'getip' => $_G['clientip'],
							'verifycode' => $verifycode,
							'dateline' => TIMESTAMP,
							'reguid' => $_G['uid'],
							'status' => 3,
							);
							DB::insert('common_verifycode', $verifycode_data);

							showmessage('smstong:smstong_mobilebind_sendsms_succeed');
						}
						else
						{
							showmessage('smstong:smstong_mobilebind_sendsms_failured', '', array('ret' => $ret));
						}
					}
				}
				elseif ($_POST['flag'] == "1") {
					if(empty($verifycode)) {
						showmessage('smstong:smstong_mobilereg_verifycode_empty');
					}

					$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];
					$verify = DB::result_first("SELECT mobile FROM ".DB::table('common_verifycode')." WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity");
						
					if (!$verify)
					{
						showmessage('smstong:smstong_mobilereg_mobile_verifycode_invalid');
					}
					else
					{
						$gid=C::t('common_usergroup')->findgroupid_by('认证会员');

						DB::query("UPDATE ".DB::table('common_member')." SET extgroupids=".$gid['groupid']." WHERE uid=$_G[uid]");

						DB::query("UPDATE ".DB::table('common_member_profile')." SET mobile='$mobile' WHERE uid=$_G[uid]");

						DB::query("UPDATE ".DB::table('common_verifycode')." SET reguid=$_G[uid],regdateline='$_G[timestamp]',status=4 WHERE mobile='$mobile' AND verifycode='$verifycode' AND getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity");

						DB::query("UPDATE ".DB::table('common_member')." SET mobilestatus=1 WHERE uid=$_G[uid]");
						

						$usergroup = DB::result_first("SELECT type FROM ".DB::table('common_usergroup')." WHERE groupid='$_G[groupid]'");
						$groupid = $_G['cache']['plugin']['smstong']['mobilegroup'];

						if ($usergroup['type'] == 'm' && !empty($groupid)) {
							DB::query("UPDATE ".DB::table('common_member')." SET groupid='$groupid' WHERE uid=$_G[uid]");
						}
					}

					$url_forward = dreferer();
					$url_forward = 'forum.php';
					showmessage('smstong:smstong_mobilebind_succeed', $url_forward);
				//	showmessage('smstong:smstong_mobilebind_succeed', 'home.php?mod=spacecp&ac=profile&op=contact');
				}
			} else {

				$periodofvalidity = $_G['cache']['plugin']['smstong']['periodofvalidity'];

				$verifycodes = DB::fetch_first("SELECT mobile,getip,dateline FROM ".DB::table('common_verifycode')." WHERE getip='$_G[clientip]' AND status=3 AND dateline>'$_G[timestamp]'-$periodofvalidity order by id desc");

				$bindsendtime = intval($verifycodes['dateline']);

				$mobilegap = intval($_G['cache']['plugin']['smstong']['mobilegap']);
				$interval = time() - $bindsendtime;
				$lastsecond = $mobilegap - $interval;

				$sendedmobile = substr($verifycodes['mobile'], 0, 4).'****'.substr($verifycodes['mobile'], 8, 3);

				$_G['sechashi'] = !empty($_G['cookie']['sechashi']) ? $_G['sechash'] + 1 : 0;
				$sechash = 'S'.($_G['inajax'] ? 'A' : '').$_G['sid'].$_G['sechashi'];

				include template($this->template);
				exit();
			}

		}
		
	}
	    
}
?>