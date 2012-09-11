<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

$num = rand(1,10);

$res['imgURL'] = "http://bbs-test.we54.com/iosapi/welcome" . $num%4 . ".jpg";

echo json_encode($res);
?>