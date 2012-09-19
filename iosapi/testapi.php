<?php


if(isset($_FILES['fileField'])){
	move_uploaded_file($_FILES["fileField"]["tmp_name"], $_FILES["fileField"]["name"]);
	echo 'ok';
}
else{
	echo 'not found field:[fileField]';
}


/*
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();


isset($_REQUEST['pic']) ? $s = $_REQUEST['pic'] : $res['changeCheck'] = 0;



	file_put_contents('testupload.jpg', $s);
	var_dump($s);
	*/
/*	 $file = fopen("testupload1.jpg","w");//打开文件准备写入
  fwrite($file,$s);//写入
  fclose($file);//关闭*/



?>
