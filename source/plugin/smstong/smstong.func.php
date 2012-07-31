<?php

/*
	[Discuz!] (C)2001-2009 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: sendsms.func.php 20894 2011-06-07 16:34:59Z 呀呀个呸 $
*/

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

function ismobile($mobile)
{
	return (strlen($mobile) == 11 || strlen($mobile) == 12) && (preg_match("/^13\d{9}$/", $mobile) || preg_match("/^15\d{9}$/", $mobile) || 
preg_match("/^18\d{9}$/", $mobile) || preg_match("/^14\d{9}$/", $mobile) || preg_match("/^0\d{10}$/", $mobile) || preg_match("/^0\d{11}$/", $mobile));
}

function sendsms($user, $pass, $mobile, $content, $checkmobile=true, $refno='', $creditchange=true)
{
	global $_G;

	$ret = true;

	$content = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $content);

	$content = str_replace(array('[', ']', 'url=', '/url', 'img', '/img'), array('', '', '', '', '', ''), $content);

	$content = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content);

	if ($checkmobile) {

		$count = DB::result_first("SELECT count(id) FROM ".DB::table('common_verifycode')." WHERE getip='$_G[clientip]' AND dateline>'$_G[timestamp]'-86400");

		if ($count >= $_G['cache']['plugin']['smstong']['deniedip'])
		{
			return lang('plugin/smstong','smstong_ipcheck_access_denied');
		}
	}

	if ($creditchange) {
		$user_id = DB::result_first("SELECT uid FROM ".DB::table('common_member_profile')." WHERE mobile='$mobile'");
		$user_id = $user_id > 0 ? $user_id : $_G['uid'];
		updatemembercount($user_id, array("extcredits{$_G['cache']['plugin']['smstong']['extcredittype']}" => - $_G['cache']['plugin']['smstong']['extcreditamount']));
	}

	if ($checkmobile)
		$ret = chackmobile($mobile);

	if($ret === true) {
		$content = str_replace('1989','1 9 8 9',$content);
		$content = str_replace('1259','1 2 5 9',$content);
		$content = str_replace('12590','1 2 5 9 0',$content);
		$content = str_replace('10086','1 0 0 8 6',$content);

	//	$smsapi = "api.chanyoo.cn";
		$smsapi = "202.165.181.81:8021";	//add by zh
		$charset = "gbk";

		if ($_G['charset'] != "gbk") {
			$charset = "utf8";
		}
		
		if (empty($_G['cache']['plugin']['smstong']['smstongsign'])) {
			$content = $content.lang('plugin/smstong','smstong_function_sign_left').$_G['setting']['bbname'].lang('plugin/smstong','smstong_function_sign_right');
		} else {
			$content = $content.lang('plugin/smstong','smstong_function_sign_left').$_G['cache']['plugin']['smstong']['smstongsign'].lang('plugin/smstong','smstong_function_sign_right');
		}

	//	$sendurl = "http://".$smsapi."/".$charset."/interface/send_sms.aspx?username=".urlencode($user)."&password=".urlencode($pass)."&receiver=".urlencode($mobile)."&content=".urlencode($content)."";
//----------------add by zh-----------------------------------------------------------------
		$sendurl = "http://".$smsapi."/HttpInterface/SendSms.php";

		$argv = array( 
			 'uname'=>$user, //提供的账号
			 'pwd'=>md5($pass), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
			 'numbers'=>$mobile,//手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
			 'content'=>$content,//短信内容
			 'smsid'=>'',
			 'act'=>'send',//默认空 如果空返回系统生成的标识串 如果传值保证值唯一 成功则返回传入的值
			 'ext'=>'5454'
		 );
		 $result = httprequest($sendurl,$argv);
//--------------------------------------------------------------------------------------------
	//	$result = httprequest($sendurl);

		if (empty($result)) return lang('plugin/smstong','smstong_notice_failured');

		$res=explode('|',$result);

		if($res[1]<0){
			return lang('plugin/smstong',$res[1]);
		}else{
			$length = mb_strlen($content, $_G['charset']);
			$count = ceil($length/64);
			$addtime = date('Y-m-d H:i:s', TIMESTAMP);

			$mobiles = explode(',', $mobile);

			foreach($mobiles as $k => $v) {
				if(empty($refno)) {
					DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'feixin')");
				}
				else if(strpos($refno, ',')) {
					DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,remark) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'feixin', '$refno')");
				} else {
					DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,refno) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'feixin', $refno)");
				}
			}

			return true;
		}
	/*	$xml = simplexml_load_string($result);

		if ($xml->result >= 0)
		{
			$length = mb_strlen($content, $_G['charset']);
			$count = ceil($length/64);
			$addtime = date('Y-m-d H:i:s', TIMESTAMP);

			$mobiles = explode(',', $mobile);

			foreach($mobiles as $k => $v) {
				if(empty($refno)) {
					DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'chanyoo')");
				}
				else if(strpos($refno, ',')) {
					DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,remark) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'chanyoo', '$refno')");
				} else {
					DB::query("INSERT INTO sms_send (mobile,content,addtime,senttime,count,status,port,refno) values('$v', '$content', '$addtime', '$addtime', $count, 1, 'chanyoo', $refno)");
				}
			}

			return true;
		}
		else
		{
			if ($_G['charset'] == "gbk")
				return iconv("utf-8", "gbk", $xml->message);
			else
				return $xml->message;
		}*/
	}
	 else {
		return $ret;
	}
}

function chackmobile($mobile) {

	global $_G;

	$mobile_array = explode(",", $_G['cache']['plugin']['smstong']['blackmobile']);

	if(in_array($mobile, $mobile_array))
		return lang('plugin/smstong','smstong_blackmobile_existed');

	if ($_G['cache']['plugin']['smstong']['areacons'] == "0") return true;

	if (empty($_G['cache']['plugin']['smstong']['areavalue'])) return true;

	$checkmobile = false;

	if(!empty($_G['cache']['plugin']['smstong']['areaconstime'])) {
		$now = dgmdate(TIMESTAMP, 'G.i');
		foreach(explode("\r\n", str_replace(':', '.', $_G['cache']['plugin']['smstong']['areaconstime'])) as $period) {
			list($periodbegin, $periodend) = explode('-', $period);
			if(($periodbegin > $periodend && ($now >= $periodbegin || $now < $periodend)) || ($periodbegin < $periodend && $now >= $periodbegin && $now < $periodend)) {
				$checkmobile = true;
			}
		}
	} else {
		$checkmobile = true;
	}

	if($_G['cache']['plugin']['smstong']['areacons'] == "1" && $checkmobile) {

		$checkurl = "http://www.ip138.com:8080/search.asp?action=mobile&mobile=".$mobile;

		$result = httprequest($checkurl);

		$checkresult = "";
		$errormsg = lang('plugin/smstong','smstong_checkmobile_error');

		$result = strip_tags($result);
		$result = preg_replace('/\s/', '', $result);
		
		switch ($_G['cache']['plugin']['smstong']['areatype'])
		{
			case 1 :
			{
				if ($_G['charset'] == "gbk") {
					preg_match(lang('plugin/smstong','smstong_mobilearea_ip138'), $result, $area);
					$checkresult = $area[2];
				}
				else {
					preg_match(lang('plugin/smstong','smstong_mobilearea_ip138'), iconv("gbk", "utf-8", $result), $area);
					$checkresult =  $area[2];
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'].lang('plugin/smstong','smstong_checkmobile_areatype_city');
			}
			break;
			case 2 :
			{
				if ($_G['charset'] == "gbk") {
					preg_match(lang('plugin/smstong','smstong_mobilearea_ip138'), $result, $area);
					$checkresult = $area[1];
				}
				else {
					preg_match(lang('plugin/smstong','smstong_mobilearea_ip138'), iconv("gbk", "utf-8", $result), $area);
					$checkresult =  $area[1];
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'].lang('plugin/smstong','smstong_checkmobile_areatype_province');
			}
			break;
			default :
			{
				if ($_G['charset'] == "gbk") {
					preg_match(lang('plugin/smstong','smstong_mobilearea_ip138'), $result, $area);
					$checkresult = $area[2];
				}
				else {
					preg_match(lang('plugin/smstong','smstong_mobilearea_ip138'), iconv("gbk", "utf-8", $result), $area);
					$checkresult =  $area[2];
				}

				$errormsg = lang('plugin/smstong','smstong_checkmobile_default').$_G['cache']['plugin']['smstong']['areavalue'].lang('plugin/smstong','smstong_checkmobile_areatype_city');
			}
			break;
			
		}

		$area_array = explode("|", $_G['cache']['plugin']['smstong']['areavalue']);

		$flag1 = false;
		$flag2 = false;

		if(in_array($checkresult, $area_array))
			$flag1 = true;

		if($_G['cache']['plugin']['smstong']['ipareacons'] == "1") {
			require_once libfile('function/misc');
			$iparea = trim(trim(convertip($_G['clientip']),'-'));
			
			foreach($area_array as $k => $v) {
				if(strstr($iparea, $v)) {
					$flag2 = true;
					break;
				}
			}
		} else {
			$flag2 = true;
		}

		if($flag1 && $flag2) return true;
	} else {
		return true;
	}

	return $errormsg;
}

function httprequest($url, $data=array(), $abort=false) {
	if ( !function_exists('curl_init') ) { return empty($data) ? doget($url) : dopost($url, $data); }
	$timeout = $abort ? 1 : 2;
	$ch = curl_init();
	if (is_array($data) && $data) {
		$formdata = http_build_query($data);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	$result = curl_exec($ch);
	return (false===$result && false==$abort)? ( empty($data) ?  doget($url) : dopost($url, $data) ) : $result;
}

function doget($url){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	if(array_key_exists("port", $url2))
		$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	else
		$url2["port"] = 80;
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2;  //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	if(array_key_exists("query", $url2))
		$request =  $url2["path"] .($url2["query"] ? "?".$url2["query"] : "");
	else
		$request =  $url2["path"];
	$in  = "GET " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "User-Agent: Payb-Agent\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function dopost($url,$post_data=array()){
	$url2 = parse_url($url);
	$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
	$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
	$host_ip = @gethostbyname($url2["host"]);
	$fsock_timeout = 2; //2 second
	if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
		return false;
	}
	$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
	$post_data2 = http_build_query($post_data);
	$in  = "POST " . $request . " HTTP/1.0\r\n";
	$in .= "Accept: */*\r\n";
	$in .= "Host: " . $url2["host"] . "\r\n";
	$in .= "User-Agent: Lowell-Agent\r\n";
	$in .= "Content-type: application/x-www-form-urlencoded\r\n";
	$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
	$in .= "Connection: Close\r\n\r\n";
	$in .= $post_data2 . "\r\n\r\n";
	unset($post_data2);
	if(!@fwrite($fsock, $in, strlen($in))){
		fclose($fsock);
		return false;
	}
	return gethttpcontent($fsock);
}

function gethttpcontent($fsock=null) {
	$out = null;
	while($buff = @fgets($fsock, 2048)){
		$out .= $buff;
	}
	fclose($fsock);
	$pos = strpos($out, "\r\n\r\n");
	$head = substr($out, 0, $pos);    //http head
	$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
	$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
	if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
		if(intval($matches[1]) / 100 == 2){
			return $body;  
		}else{
			return false;
		}
	}else{
		return false;
	}
}