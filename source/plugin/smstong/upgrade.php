<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: upgrade.php 22778 2011-10-20 10:29:30Z я╫я╫╦Жеч $
 */

if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF

-- ----------------------------
-- Table structure for sms_send
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sms_send` (
`id` mediumint(8) NOT NULL AUTO_INCREMENT,
`mobile` char(12) NOT NULL,
`content` varchar(300) NOT NULL,
`addtime` timestamp NULL DEFAULT NULL,
`senttime` timestamp NULL DEFAULT NULL,
`count` smallint(6) NOT NULL DEFAULT '1',
`status` smallint(6) NOT NULL DEFAULT '0',
`remark` varchar(32) DEFAULT NULL,
`refno` smallint(6) NOT NULL DEFAULT '0',
`port` char(10) NOT NULL DEFAULT 'COM1',
PRIMARY KEY (`id`)
) ENGINE=MyISAM;

-- ----------------------------
-- Table structure for sms_recv
-- ----------------------------
CREATE TABLE IF NOT EXISTS `sms_recv` (
`id` mediumint(8) NOT NULL AUTO_INCREMENT,
`mobile` char(32) NOT NULL,
`content` varchar(300) NOT NULL,
`recvtime` timestamp NOT NULL,
`port` char(10) NOT NULL DEFAULT 'COM1',
`senttime` timestamp NOT NULL,
`remark` varchar(32) DEFAULT NULL,
PRIMARY KEY (`id`)
) ENGINE=MyISAM;

UPDATE `sms_send` SET remark = '';

EOF;

runquery($sql);


$finish = TRUE;

?>