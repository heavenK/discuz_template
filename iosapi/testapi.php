<?php


if(isset($_FILES['fileField'])){
	move_uploaded_file($_FILES["fileField"]["tmp_name"], $_FILES["fileField"]["name"]);
	echo 'ok';
}
else{
	echo 'not found field:[fileField]';
}

cut_pic($_FILES["fileField"]["name"],'a11.jpg');

function cut_pic($src_img ,$filename){
	list($src_w,$src_h)=getimagesize($src_img);  // 获取原图尺寸

	$dst_h = 580;
	$dst_w = 580;

	$dst_scale = $dst_h/$dst_w; //目标图像长宽比
	$src_scale = $src_h/$src_w; // 原图长宽比
	
	if($src_scale>=$dst_scale){  // 过高
		$w = intval($src_w);
		$h = intval($dst_scale*$w);
		
		$x = 0;
		$y = ($src_h - $h)/3;
	}
	else{ // 过宽
		$h = intval($src_h);
		$w = intval($h/$dst_scale);
		
		$x = ($src_w - $w)/2;
		$y = 0;
	}
	
	// 剪裁
	$source=imagecreatefromjpeg($src_img);
	$croped=imagecreatetruecolor($w, $h);
	imagecopy($croped,$source,0,0,$x,$y,$src_w,$src_h);
	
	// 缩放
	$scale = $dst_w/$w;
	$target = imagecreatetruecolor($dst_w, $dst_h);
	$final_w = intval($w*$scale);
	$final_h = intval($h*$scale);
	imagecopyresampled($target,$croped,0,0,0,0,$final_w,$final_h,$w,$h);
	
	
	// 保存
	imagejpeg($target, $filename);
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
