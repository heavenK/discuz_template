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
.part1 div:nth-child(2n+2)	{ float:right; width:500px;}

.part2	{ float:left; width:1000px;}
.part2 div:nth-child(2n+1)	{ float:left; width:500px;}
.part2 div:nth-child(2n+2)	{ float:right; width:500px;}

</style>
<body>
<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="type" value="dosubmit" />
<h1>修改登录随机图片</h1>
<div class="part1">
    <div>
        <img src="welcome0.jpg" width="150" />
        图片1:<input type="file" name="pic0"  />
    </div>
    <div>
        <img src="welcome1.jpg" width="150" />
        图片2:<input type="file" name="pic1"  />
    </div>
    <div>
        <img src="welcome2.jpg" width="150" />
        图片3:<input type="file" name="pic2"  />
    </div>
    <div>
        <img src="welcome3.jpg" width="150" />
        图片4:<input type="file" name="pic3"  />
    </div>
</div>
<p></p>
<h1>修改广告</h1>
<div class="part2">
    <div>
        <img src="ad00.jpg" width="150" />
        图片1:<input type="file" name="ad0"  />
        类型1:<select name="types0">
        		<option value="1">新闻</option>
                <option value="2">网页链接</option>
                <option value="3">帖子</option>
                <option value="4" selected="selected">照相</option>
        	  </select>
        内容1:<input type="text" name="content0"  />
    </div>
    <div>
        <img src="ad01.jpg" width="150" />
        图片2:<input type="file" name="ad1"  />
        类型2:<select name="types1">
        		<option value="1">新闻</option>
                <option value="2">网页链接</option>
                <option value="3">帖子</option>
                <option value="4" selected="selected">照相</option>
        	  </select>
        内容2:<input type="text" name="content1"  />
    </div>
    <div>
        <img src="ad02.jpg" width="150" />
        图片3:<input type="file" name="ad2"  />
        类型3:<select name="types2">
        		<option value="1">新闻</option>
                <option value="2">网页链接</option>
                <option value="3">帖子</option>
                <option value="4" selected="selected">照相</option>
        	  </select>
        内容3:<input type="text" name="content2"  />
    </div>
    <div>
        <img src="ad03.jpg" width="150" />
        图片4:<input type="file" name="ad3"  />
        类型4:<select name="types3">
        		<option value="1">新闻</option>
                <option value="2">网页链接</option>
                <option value="3">帖子</option>
                <option value="4" selected="selected">照相</option>
        	  </select>
        内容4:<input type="text" name="content3"  />
    </div>
</div>
<input type="submit" value="submit" />
</form>
</body>