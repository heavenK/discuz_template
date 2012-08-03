<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: acountinfo.php 18582 2010-07-17 11:12:11Z 呀呀个呸 $
 */

define('IN_DISCUZ', TRUE);

//$smsapi = "a1.chanyoo.cn";
$smsapi = "202.165.181.81:8021";		//add by zh
$charset = "utf8";
$username = $_GET['username'];
$password = $_GET['password'];

//$url = "http://".$smsapi."/".$charset."/interface/user_info.aspx?username=".$username."&password=".$password."";
$url = "http://".$smsapi."/HttpInterface/GetMyFree.php?uname=".$username."&pwd=".$password."&balance=1";	//add by zh

require_once('smstong.func.php');

$ret = httprequest($url);

if(!empty($ret)){
	$sms_left = $ret;
}


/*$xml = simplexml_load_string($ret);

$uid = intval($xml->result);

if ($uid > 0)
{
	$result = $xml->result;
	$user_balance = $xml->user_balance;
	$user_amount = $xml->user_amount;
	$sms_left = $xml->sms_left;
	$sms_send = $xml->sms_send;
	$sms_receive = $xml->sms_receive;
	$expired_date = $xml->expired_date;
}*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta name="Copyright" content="" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<title>帐号信息</title>
<style>
html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td {margin: 0;padding: 0;border: 0;outline: 0;font-size: 100%;background: transparent;}
ol, ul {list-style: none;}
body{ background:#FFF; margin:0 20px;}
body,table,td{ font-size:12px; color:#666;}
table{ border-collapse:collapse; border-spacing: 0;}
td{ border-top:1px dotted #DEEFFB; padding:5px; font-size:14px}
.title{color:#0099CC; font-weight:700; height:25px; text-align:left; padding:5px; background:#e5f1fb}
a{ color:#f8505c; text-decoration:none;}
.btn{ padding:5px; display:block; width:200px; background:#e5f1fb; text-align:center; height:20px; line-height:20px; text-decoration:none; color:#666; border: 1px solid #c7e1f6; margin-left:80px; font-size:14px; cursor:pointer;}
</style>
</head>
<body>

<?php
//if($uid > 0){
if(!empty($ret)){
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th colspan="2" class="title">帐号信息</th>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>当前帐号：</strong></td>
    <td width="87%"><?php echo $_GET['username'] ?></td>
  </tr>
  <tr>
    <td align="right"><strong>剩余条数：</strong></td>
    <td><?php echo $sms_left ?> 条</td>
  </tr>
  <!-- <tr>
    <td width="13%" align="right"><strong>已发条数：</strong></td>
    <td width="87%"><?php echo $sms_send ?> 条</td>
  </tr>

  <tr>
    <td align="right"><strong>联系方式：</strong></td>
    <td>如有任何问题请联系QQ：320266361，320266362，320266363，320266364，320266365（工作时间：周一至周五9：00~18：00，周六周日节假日休息 ）。
	</td>
  </tr>

   <tr>
    <td align="right"><strong>奖励短信：</strong></td>
    <td>安装插件注册平台帐号免费赠送10条测试短信，页面右上角分享到腾讯微博，QQ空间，朋友网，新浪微博各得10条短信，评五星赠送10条一共50条短信，安装插件开启评五星并分享后联系我们核实发放短信。
	</td>
  </tr>

  <tr>
    <td align="right"><strong><br />购买短信：</strong></td>
    <td><span class="result"><br /><a href="http://www.chanyoo.cn/mod_static-view-sc_id-1111115.html" target="_blank" title="通过在线充值的方式购买短信条数：在新打开的页面中选择您要充值的短信条数，根据提示完成在线支付操作，之后刷新本帐号信息页面剩余条数就会加上您充值购买的短信条数，目前支持支付宝，财付通，以及各个常用的网银在线充值。">点击此处在线购买短信</a></span></td>
  </tr>

  <tr>
    <td align="right"><strong><br />免费短信：</strong></td>
    <td><span class="result"><br /><a href="http://app.offer99.com/?pid=z3f0c289df9b385b90383dbdefdbe461&userid=<?php echo $uid ?>&order=time_delay&asc_desc=asc" target="_blank" title="通过完成广告任务获取免费短信条数：请先登录开源软件增值服务平台到短信管理-短信提醒里面设置自己的真实手机号，然后在新打开的页面选择即时认证的广告，按照提示完成广告，当广告商审核通过后会给你的帐号加短信并发送提醒到您设置的手机号上。">点击此处获取免费短信</a></span></td>
  </tr> -->
  
</table>

<?php } else { ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th colspan="2" class="title">帐号信息</th>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>当前帐号：</strong></td>
    <td width="87%"><?php echo $_GET['username'] ?></td>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>返回信息：</strong></td>
    <!-- <td width="87%"><?php echo $xml->message ?></td> -->
	<td width="87%">账户信息错误！</td>
  </tr>
</table>

<?php } ?>

</body>
</html>
