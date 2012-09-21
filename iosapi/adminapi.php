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
	echo "<script language='javascript'>window.location.reload();</script>";
}

?>
<style>
body	{ margin:0 auto; width:1000px;}

.part1	{ float:left; width:1000px;}
.part1 div:nth-child(2n+1)	{ float:left; width:500px;}
.part1 div:nth-child(2n+1)	{ float:right; width:500px;}

.part2	{ float:left; width:1000px;}
.part2 div:nth-child(2n+1)	{ float:left; width:500px;}
.part2 div:nth-child(2n+1)	{ float:right; width:500px;}

</style>
<body>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="type" value="dosubmit" />
<h1>�޸ĵ�¼���ͼƬ</h1>
<div class="part1">
    <div>
        <img src="welcome0.jpg" width="150" />
        ͼƬ1:<input type="file" name="pic0"  />
    </div>
    <div>
        <img src="welcome1.jpg" width="150" />
        ͼƬ2:<input type="file" name="pic1"  />
    </div>
    <div>
        <img src="welcome2.jpg" width="150" />
        ͼƬ3:<input type="file" name="pic2"  />
    </div>
    <div>
        <img src="welcome3.jpg" width="150" />
        ͼƬ4:<input type="file" name="pic3"  />
    </div>
</div>

<h1>�޸Ĺ��</h1>
<div class="part2">
    <div>
        <img src="ad00.jpg" width="150" />
        ͼƬ1:<input type="file" name="ad0"  />
    </div>
    <div>
        <img src="ad01.jpg" width="150" />
        ͼƬ2:<input type="file" name="ad1"  />
    </div>
    <div>
        <img src="ad02.jpg" width="150" />
        ͼƬ3:<input type="file" name="ad2"  />
    </div>
    <div>
        <img src="ad03.jpg" width="150" />
        ͼƬ4:<input type="file" name="ad3"  />
    </div>
</div>
<input type="submit" value="submit" />
</form>
</body>