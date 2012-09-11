<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

/*$username = isset($_GET['userName']) ? $_GET['userName'] : exit;
$passwd = isset($_GET['password']) ? $_GET['password'] : exit;
$nickname = isset($_GET['nickname']) ? $_GET['userName'] : exit;*/

$post_data = array();
$post_data['username'] = "test35xx";
$post_data['password'] = "123123";
$post_data['password2'] = "123123";
$post_data['email'] = "233451@aass.com";
$post_data['field1'] = "test35xx";
$post_data['regsubmit'] = "yes";
$post_data['phone_reg'] = 11;
$post_data['formhash'] = $_G['formhash'];
$post_data['referer'] = "http://bbs-test.we54.com/iosapi/reg_success.php";

$url='http://bbs-test.we54.com/member.php?mod=register';
$o="";
foreach ($post_data as $k=>$v)
{
    $o.= "$k=".urlencode($v)."&";
}
$post_data=substr($o,0,-1);
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
$result = curl_exec($ch);

?>