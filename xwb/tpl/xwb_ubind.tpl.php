<?php if (!defined('IS_IN_XWB_PLUGIN')) {die('access deny!');}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>绑定插件 - 新浪微博插件</title>
<link href="<?php echo XWB_plugin::getPluginUrl('images/xwb_admin.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo XWB_plugin::getPluginUrl('images/xwb.js');?>"></script>
<style>
body{ background:#FFF;}
#unbound	{ padding-left:6px; padding-top:21px;}
#unbound .kaiser_sina_main					{ width:799px; padding:0;}
#unbound .kaiser_sina_con-l					{ width:64px; height:64px; margin-right:0; overflow:hidden; float:left;}
#unbound .kaiser_sina_con-l .binding		{ width:64px; height:64px; padding:0; background:url(/template/we54/images/kiaser_space_bangding_sina.png) no-repeat; overflow:hidden; float:left;}
#unbound .kaiser_sina_con-r					{ width:80%; padding:4px 0 0 19px; font-family:"微软雅黑"; overflow:hidden; float:left;}
#unbound .kaiser_sina_con-r h4				{ padding:0 16px 0 0; font-size:12px; font-weight:normal; color:#999; line-height:24px; overflow:hidden; float:left;}
#unbound .kaiser_sina_con-r a				{ width:131px; height:24px; background:none; overflow:hidden; float:left;}
#unbound .kaiser_sina_con-r p				{ width:700px; margin-left:0; margin-top:10px; display:inline; font-size:12px; font-weight:normal; color:#999; line-height:16px; overflow:hidden; float:left;}
</style>
</head>
<body>
    <div id="unbound" class="set-wrap">
    	<!--h3>新浪微博绑定设置</h3-->
        <?php if ( $_GET['ac'] == 'plugin_all' ):?>
        <div class="main kaiser_sina_main">
        	<div class="con-l kaiser_sina_con-l">
                <div class="binding"></div>
            </div> 
            <div class="con-r kaiser_sina_con-r">
				<h4>点击按钮，立刻绑定QQ帐号</h4>
                <a class="binding-btn binding-w" href="javascript:void(0)" onclick="window.top.location='<?php echo XWB_plugin::getEntryURL('xwbAuth.login');?>'"><img src="template/we54/images/kaiser_sina_bangding_button.png" /></a>
                <p>绑定以后就可以把帖子、回帖同步发到新浪微博上啦，无需记住本站的帐号和密码，随时使用新浪帐号密码轻松登录</p>
            </div>
        </div>
        <?php endif;?>
        <?php if ( $_GET['ac'] == 'plugin' ):?>
        	<div class="main kaiser_sina_main">
                <div class="con-l kaiser_sina_con-l">
                    <div class="binding"></div>
                </div> 
                <div class="con-r kaiser_sina_con-r">
                    <h4>点击按钮，立刻绑定QQ帐号</h4>
                    <a class="binding-btn binding-w" href="javascript:void(0)" onclick="window.top.location='<?php echo XWB_plugin::getEntryURL('xwbAuth.login');?>'"><img src="template/we54/images/kaiser_sina_bangding_button.png" /></a>
                    <p>绑定以后就可以把帖子、回帖同步发到新浪微博上啦，无需记住本站的帐号和密码，随时使用新浪帐号密码轻松登录</p>
                </div>
            </div>
			<?php if ( XWB_S_UID > 0 && ! empty($huwbUserRs) ):?>
            <div class="active-s1">
                <h4>他们已经绑定微博了，你还不行动？</h4>
                <?php foreach ($huwbUserRs as $value):?>
                <div class="users">
                    <a href="<?php echo XWB_plugin::getWeiboProfileLink($value['sina_uid']); ?>" target="_blank"><?php echo $value['avatar'];?></a>
                    <div class="user-info">
                        <p><?php echo XWB_plugin::convertEncoding($value['username'], XWB_S_CHARSET, 'UTF-8');?></p>
                        <a class="addfollow-btn" href="<?php echo XWB_plugin::getWeiboProfileLink($value['sina_uid']); ?>" target="_blank"></a>
                        <a class="already-addfollow-btn hidden" href="javascript:void(0)#"></a>
                    </div>
                </div>
                <?php endforeach;?>
            </div>
            <?php endif;?>
        <?php endif;?>
    </div>
</body>
</html>