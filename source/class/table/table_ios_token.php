<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: table_ios_token.php 30071 2012-05-09 02:22:31Z zhengqingpeng $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_ios_token extends discuz_table_archive
{
	public function __construct() {

		$this->_table = 'ios_token';

		parent::__construct();
	}

	public function update_token($old_token, $new_token) {
		if($old_token) {
			$data = array('token'=>$new_token);
			DB::update($this->_table, $data, array('token' => $old_token), 'UNBUFFERED');
		}
	}

	public function fetch_by_token($token) {
		$token_list = array();
		if($token) {
			$token_list = DB::fetch_first('SELECT * FROM %t WHERE token=%s', array($this->_table, $token));
		}
		return $token_list;
	}


	public function insert($token, $other = '') {
		if($token) {
			$base = array(
				'token' => $token,
				'other' => (string)$other,
			);
			parent::insert($base, false, true);
		}
	}

	
}

?>