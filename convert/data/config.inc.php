<?php


$_config = array();

// ----------------  CONFIG SOURCE  ----------------- //
$_config['source']['dbhost'] = '192.168.0.9';
$_config['source']['dbuser'] = 'user1';
$_config['source']['dbpw'] = 123;
$_config['source']['dbname'] = 'bbsnew';
$_config['source']['tablepre'] = 'cdb_';
$_config['source']['dbcharset'] = '';
$_config['source']['pconnect'] = 1;

// ----------------  CONFIG TARGET  ----------------- //
$_config['target']['dbhost'] = '192.168.0.9';
$_config['target']['dbuser'] = 'user1';
$_config['target']['dbpw'] = 123;
$_config['target']['dbname'] = 'bbs_update';
$_config['target']['tablepre'] = 'pre_';
$_config['target']['dbcharset'] = '';
$_config['target']['pconnect'] = 1;

// ----------------  CONFIG UCENTER  ---------------- //
$_config['ucenter']['dbhost'] = '192.168.0.9';
$_config['ucenter']['dbuser'] = 'user1';
$_config['ucenter']['dbpw'] = 123;
$_config['ucenter']['dbname'] = 'bbs_update';
$_config['ucenter']['tablepre'] = 'pre_ucenter_';
$_config['ucenter']['dbcharset'] = '';
$_config['ucenter']['pconnect'] = 1;


// -------------------  THE END  -------------------- //

?>