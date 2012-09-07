<?php
if( is_file( 'xwb/index.php' ) ){
	require 'source/class/class_passport.php';		//add by zh
	require 'xwb/index.php';
}else{
	exit('CAN NOT RUN THE PLUGIN!');
}
