<?php
/**
 * 用于控制基于ip输入次数限制类(dx)
 * @author yaoying
 * @version $Id: xwbIpCount.class.php 985 2011-09-21 07:48:08Z yaoying $
 *
 */
class xwbIpCount{
	
	function xwbIpCount(){
		if(rand(1, 100) == 1){
			$this->gc();
		}
	}	
	
	function get($key){
		$db = XWB_plugin::getDB();
		$table = XWB_S_TBPRE. 'common_cache';
		$res = $db->fetch_first('SELECT `cachevalue`, `dateline` FROM '. $table. ' WHERE `cachekey` = \''. $this->_cachekey($key) .'\'');
		if(!isset($res['cachevalue']) || (isset($res['dateline']) && $res['dateline'] + 15 * 60 <= time())){
			return 0;
		}else{
			return intval($res['cachevalue']);
		}
	}
	
	function set($key, $count){
		$db = XWB_plugin::getDB();
		$table = XWB_S_TBPRE. 'common_cache';
		$cachename = $this->_cachekey($key);
		$count = intval($count);
		$timestamp = time();
		$sql = "REPLACE INTO `{$table}` (`cachekey`, `cachevalue`, `dateline`) VALUES ('{$cachename}', '{$count}', '{$timestamp}')";
		$db->query($sql);
	}
	
	function _cachekey($key){
		return 'xipct_' .substr(md5(XWB_Plugin::getIP(). $key), 0, 16);
	}
	
	function gc(){
		$db = XWB_plugin::getDB();
		$table = XWB_S_TBPRE. 'common_cache';
		$timestamp = time() - (15 * 60);
		$sql = "DELETE FROM `{$table}` WHERE `cachekey` LIKE 'xipct_%' AND `dateline` < '{$timestamp}' ";
		$db->query($sql);
	}
	
}