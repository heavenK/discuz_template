<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();





$xmlstr =  $GLOBALS[HTTP_RAW_POST_DATA];
if(empty($xmlstr)) $xmlstr = file_get_contents('php://input');

	file_put_contents('testupload.jpg', $xmlstr);



?>
<img src="testupload.jpg"  />