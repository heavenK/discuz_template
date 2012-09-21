<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

if($_POST['type'] == 'dosubmit'){
	for($i=0 ;$i<4 ;$i++){
		$pic = 'pic'.$i;
		$ad = 'ad'.$i;
		if(isset($_FILES[$pic])){
			move_uploaded_file($_FILES[$pic]["tmp_name"], "welcome".$i.".jpg");
		}
		if(isset($_FILES[$ad])){
			move_uploaded_file($_FILES[$ad]["tmp_name"], "ad0".$i.".jpg");
		}	
	}
}

?>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="type" value="dosubmit" />
<h1>�޸ĵ�¼���ͼƬ</h1>
<div>
<img src="welcome0.jpg" width="200" />
ͼƬ1:<input type="file" name="pic0"  />
<img src="welcome1.jpg" width="200" />
ͼƬ2:<input type="file" name="pic1"  />
<img src="welcome2.jpg" width="200" />
ͼƬ3:<input type="file" name="pic2"  />
<img src="welcome3.jpg" width="200" />
ͼƬ4:<input type="file" name="pic3"  />
</div>

<h1>�޸Ĺ��</h1>
<div>
<img src="ad00.jpg" width="200" />
ͼƬ1:<input type="file" name="ad0"  />
<img src="ad01.jpg" width="200" />
ͼƬ2:<input type="file" name="ad1"  />
<img src="ad02.jpg" width="200" />
ͼƬ3:<input type="file" name="ad2"  />
<img src="ad03.jpg" width="200" />
ͼƬ4:<input type="file" name="ad3"  />
</div>
<input type="submit" value="submit" />
</form>