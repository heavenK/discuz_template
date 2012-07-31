<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: checkenv.php 18582 2010-07-16 16:53:06Z 呀呀个呸 $
 */

define('IN_DISCUZ', TRUE);

if (function_exists("curl_init")) {
    $c = true;
    $c_res = "<font color='green'>支持</font>";
} else {
    $c = false;
    $c_res = "<font color='red'>不支持</font>";
}

if (function_exists("fsockopen")) {
    $s = true;
    $s_res = "<font color='green'>支持</font>";
} else {
    $s = false;
    $s_res = "<font color='red'>不支持</font";
}

if (function_exists("simplexml_load_string")) {
    $x = true;
    $x_res = "<font color='green'>支持</font>";
} else {
    $x = false;
    $x_res = "<font color='red'>不支持</font>";
}

if (function_exists("mb_strlen")) {
    $l = true;
    $l_res = "<font color='green'>支持</font>";
} else {
    $l = false;
    $l_res = "<font color='red'>不支持</font>";
}

$smsapi = "a1.chanyoo.cn";
$charset = "utf8";

$url = "http://".$smsapi."/".$charset."/interface/user_info.aspx";

require_once('smstong.func.php');

$ret = httprequest($url);

if ($ret && stristr($ret, '<?xml')) {
    $h = true;
    $h_res = "<font color='green'>支持</font>";
} else {
    $h = false;
    $h_res = "<font color='red'>不支持</font>";
}

$result = '';

if(!$c) {
    $result="<br /><font color='red'>空间不支持短信接口请求的函数curl_init，curl_init和fsockopen只需支持其中一个即可。</font>";
}

if(!$s) {
    $result.="<br /><font color='red'>空间不支持短信接口请求的函数fsockopen，curl_init和fsockopen只需支持其中一个即可。</font>";
}

if(!$x) {
    $result.="<br /><font color='red'>空间不支持短信接口返回XML信息解析的函数simplexml，必须配置PHP执行环境支持此函数。</font>";
}

if(!$h) {
    $result.="<br /><font color='red'>空间不支持访问短信网关，必须配置空间所在服务器防火强设置允许访问短信网关。</font>";
}

if(!$l) {
    $result.="<br /><font color='red'>空间不支持短信内容字符计算的函数mb_strlen。</font>";
}

$test1 = ($c || $s);

$test2 = ($x && $h);

$test3 = ($test1 && $test2);

$test4 = ($test3 && $l);

if ($test4) {
	$result .= "<br /><font color='green'>您的空间可以正常的使用本插件发送短信。</font>";
}

$operation = '';
$installtype = '';

if(array_key_exists("operation", $_GET) && array_key_exists("installtype", $_GET))
	$operation = $_GET['operation'];
	$installtype = $_GET['installtype'];
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<meta name="Copyright" content="" />
<meta name="description" content="" />
<meta name="keywords" content="" />
<title>环境检测</title>
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <th colspan="2" class="title">环境检测</th>
  </tr>
  <tr>
    <td width="13%" align="right"><strong>curl_init：</strong></td>
    <td width="87%"><?php echo $c_res ?></td>
  </tr>
  <tr>
    <td align="right"><strong>fsockopen：</strong></td>
    <td><?php echo $s_res?></td>
  </tr>
  <tr>
    <td align="right"><strong>simplexml：</strong></td>
    <td><?php echo $x_res?></td>
  </tr>
  <tr>
    <td align="right"><strong>mb_strlen：</strong></td>
    <td><?php echo $l_res?></td>
  </tr>
  <tr>
    <td align="right"><strong>访问短信网关：</strong></td>
    <td><?php echo $h_res?></td>
  </tr>
  <tr>
    <td align="right"><strong><br />检测结果：</strong></td>
    <td><span class="result"><?php echo $result?></span></td>
  </tr>
</table>
<?php
if($operation == 'plugininstall'){
?>

<?php
if($test3){
?>
<br /><a href="../../../admin.php?action=plugins&operation=plugininstall&dir=smstong&finish=1&installtype=<?php echo $installtype?>" target="_parent" class="btn">环境检测通过继续安装插件</a>
<?php } else { ?>
<br /><a href="http://www.chanyoo.cn/mod_article-article_content-article_id-42.html" target="_blank" class="btn">环境检测不通过查看解决方法</a>

<?php }} ?>
</body>
</html>
