<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

$pic[0]['imgURL'] = "http://bbs-test.we54.com/iosapi/ad01.jpg";
$pic[0]['type'] = "4";
$pic[0]['content'] = "";

$pic[1]['imgURL'] = "http://bbs-test.we54.com/iosapi/ad02.jpg";
$pic[1]['type'] = "4";
$pic[1]['content'] = "";


$pic[2]['imgURL'] = "http://bbs-test.we54.com/iosapi/ad03.jpg";
$pic[2]['type'] = "4";
$pic[2]['content'] = "";

$pic[3]['imgURL'] = "http://bbs-test.we54.com/iosapi/ad04.jpg";
$pic[3]['type'] = "4";
$pic[3]['content'] = "";

$res['pics'] = $pic;

echo json_encode($res);
?>