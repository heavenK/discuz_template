<?php
/**
 * 插件程序常量配置文件，由svn41经过删减和重写而来
 * @author xionghui<xionghui1@staff.sina.com.cn>
 * @modifier yaoying <yaoying@staff.sina.com.cn>
 * @copyright SINA INC.
 * @version $Id: common.cfg.php 995 2012-06-09 08:42:47Z yaoying $
 *
 */

//-----------------------------------------------------------------------
// 是否在插件程序中的标识
define('IS_IN_XWB_PLUGIN',		true);
define('XWB_P_PROJECT', 'xwb4dx');
define('XWB_P_VERSION',		'2.1.5');
define('XWB_P_INFO_API',	'http://x.weibo.com/service/stdVersion.php?p='. XWB_P_PROJECT. '&v='. XWB_P_VERSION );
//-----------------------------------------------------------------------

// 路径配置相关
define('XWB_P_ROOT',			dirname(__FILE__) );
define('XWB_P_DIR_NAME',		basename(XWB_P_ROOT) );
define('XWB_P_DATA',		XWB_P_ROOT. DIRECTORY_SEPARATOR. 'log' );
//-----------------------------------------------------------------------
// XWB 所用的SESSION数据存储变量名
define('XWB_CLIENT_SESSION',	'XWB_P_SESSION');

//-----------------------------------------------------------------------
//获取模块路由的变量名
define('XWB_R_GET_VAR_NAME',	'm');
//默认路由
define('XWB_R_DEF_MOD',			'xwbSiteInterface');
//默认路由方法
define('XWB_R_DEF_MOD_FUNC',	'default_action');

//XWB全局数据存储变量名
define('XWB_SITE_GLOBAL_V_NAME','XWB_SITE_GLOBAL_V_NAME');

//-----------------------------------------------------------------------
// 微博 api url
define('XWB_API_URL', 	'http://api.t.sina.com.cn/');
//define('XWB_API_VERSION', '1.0a');
/*oauth版本*/
define('XWB_API_VERSION', '2.0');
/*接口*/
define('XWB_API_URL_20',  'https://api.weibo.com/');
/*认证*/
define('XWB_API_AUTH_URL',  'http://api.weibo.com/');
/*微博域名*/
define('XWB_API_WEB_URL',  'http://weibo.com/');
// 微博API使用的字符集，大写 如果是UTF-8 则表示为  UTF-8
define('XWB_API_CHARSET',		'UTF8');
//微博评论回推地址
define('XWB_PUSHBACK_URL', 'http://service.x.weibo.com/pb/');

//-----------------------------------------------------------------------
//插件所服务的站点根目录。这是本文件唯一出现"S"类别的常量
define('XWB_S_ROOT',	dirname(XWB_P_ROOT));

//表单验证token
define('XWB_TOKEN_NAME', 'tokenhash');