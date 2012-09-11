<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();


isset($_GET['author']) ? $author = $_GET['author'] : $author = '';
$opinion = isset($_GET['opinion']) ? $_GET['opinion'] : '';

$sql = "INSERT INTO `pre_phone_message` VALUES('' ,'$author' ,'$opinion' ," . time() .")";
$res = DB::query($sql);


if($res) $r['opinionCheck'] = 1;
else	$r['opinionCheck'] = 0;

echo json_encode($r);

?>