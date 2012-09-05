<?php

/**
 * 模块：远程API各类操作
 * @author xionghui<xionghui1@staff.sina.com.cn>
 * @since 2010-06-08
 * @copyright Xweibo (C)1996-2099 SINA Inc.
 * @version $Id: xwbSiteInterface.mod.php 614 2011-02-10 09:52:40Z yaoying $
 *
 */
class xwbApiInterface
{
	
	var $tokehash = 'xwbApiInterface';
	
	function xwbApiInterface() {
		if(!defined('XWB_S_IS_ADMIN') || !XWB_S_IS_ADMIN) {
			XWB_plugin::deny('');
		}
	}

	function default_action() {
		echo 'OK!';
	}

    /**
	 * api通信设置
	 */
	function apiCfg()
    {
    	$tokenhash = xwb_token::make($this->tokehash, true);
		include XWB_P_ROOT.'/tpl/api_cfg_app_set.tpl.php';
	}

    /**
	 * 开启远程API
	 */
    function openApi()
    {
        if(!xwb_token::checkInput('p', $this->tokehash, true)){
        	exit(json_encode(array('errno' => 1, 'err' => '令牌验证失败，请返回重试')));
		}	
        $url = trim(XWB_plugin::V('p:url', ''));
        if( !$url || strpos($url, 'http') !== 0) {
            exit(json_encode(array('errno' => 1, 'err' => '请输入远程API地址')));
        }
        if( !defined('XWB_LOCAL_API') || '' == XWB_LOCAL_API) {
            exit(json_encode(array('errno' => 2, 'err' => '请设置本地API地址')));
        }
        
        $stx = XWB_plugin::pCfg('switch_to_xweibo');
        $utx = XWB_plugin::pCfg('url_to_xweibo');

        if(XWB_plugin::setPCfg(array('switch_to_xweibo' => 1, 'url_to_xweibo' => $url)))
        {
            $api = XWB_plugin::N('apixwb', $url);
            $response = $api->setNotice(1, XWB_LOCAL_API, FALSE);      
            if( !is_array($response) || 0 != $response['errno']) {
                XWB_plugin::setPCfg(array('switch_to_xweibo' => $stx, 'url_to_xweibo' => $utx));
            } elseif( !empty($response['rst']['baseurl'])) {
                XWB_plugin::setPCfg(array('baseurl_to_xweibo' => $response['rst']['baseurl']));
            }
            exit(json_encode($response));
        } else {
            exit(json_encode(array('errno' => 1, 'err' => '配置文件无法写入')));
        }
    }

    /**
	 * 关闭远程API
	 */
    function closeApi()
    {
        if(!xwb_token::checkInput('p', $this->tokehash, true)){
        	exit(json_encode(array('errno' => 1, 'err' => '令牌验证失败，请返回重试')));
		}	
        $stx = XWB_plugin::pCfg('switch_to_xweibo');

        if(XWB_plugin::setPCfg(array('switch_to_xweibo' => 0)))
        {
            $api = XWB_plugin::N('apixwb');
            $response = $api->setNotice(0, '', FALSE);

//            if( !is_array($response) || 0 != $response['errno']) {
//                XWB_plugin::setPCfg(array('switch_to_xweibo' => $stx));
//            }
            
            exit(json_encode($response));
        } else {
            exit(json_encode(array('errno' => 1, 'err' => '配置文件无法写入')));
        }
    }


}
?>
