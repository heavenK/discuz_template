<?php
define('APPTYPEID', 2);
define('CURSCRIPT', 'forum');

require '../source/class/class_core.php';
require '../source/function/function_forum.php';

C::app()->init();

$sql = 'SELECT uid,username FROM '.DB::table('common_member').' WHERE extgroupids=22';

$query = DB::query($sql);

while($result = DB::fetch($query)) {
	
	$sql_p = "SELECT truename,telnum FROM passport_user WHERE username='".$result['username']."'";
	$query_p = DB::query($sql_p);
	$res_p = DB::fetch($query_p);
	
	if($res_p){
		if(!$res_p['truename'])	$res_p['truename'] = '';
		if(!$res_p['telnum'])	$res_p['telnum'] = '';
		$data = DB::query("UPDATE pre_common_member_profile SET realname='".$res_p['truename']."',mobile='".$res_p['telnum']."' WHERE uid=".$result['uid']);
		echo $result['username']."  ".$res_p['truename']."   ".$res_p['telnum']."--------";
	}
}


?>