插件名称：Discuz! X2.5 短信通
插件作者：呀呀个呸
插件网址：http://s1.chanyoo.cn/

插件介绍：
Discuz! X2.5 短信通插件http://www.discuz.net/thread-2251159-1-1.html
插件功能：手机短信验证注册，手机短信找回密码，手机号登录帐号，手机号绑定任务，注册修改密码短信提醒，指定版块强制绑定手机号指定用户组可以除外，参加活动短信通知，活动帖审核短信通知，活动贴发通知短信，取消参加活动短信通知，显示会员绑定手机状态，留言板短信提醒，后台发送短信通知。

安装方法：
安装之前请先把全站备份或者先在本地测试：
把插件包解压，然后把\source\plugin\smstong目录上传到网站source/plugin目录下。
到后台-插件-安装新插件-Discuz! X2.5 SMSTong-安装。
到后台-插件-插件列表-Discuz! X2.5 SMSTong-启用。
到后台-插件-插件列表-Discuz! X2.5 SMSTong-设置里面输入您的帐号和密码然后开启对应功能。

本插件后台已经全部绿色化了，安装好后设置好开源软件增值服务平台帐号密码，后台就可以发短信通知（前提是用户资料里面有手机号）和群发短信。

此插件使用完全免费开放，注册帐号免费赠送10条短信，如需更多免费短信，可以登录系统到帐号管理-帐号信息-免费获取短信链接里面通过完成相应任务获取免费短信条数。

只想用后台发短信的用户可以只上传\source\plugin\smstong目录到网站source/plugin目录下然后安装，只不过里面的有些功能设置就无效了。

注意：此插件卸载重新安装会删除手机绑定任务数据，所以请不要轻易尝试。卸载或者安装前请备份数据库。

因有些功能涉及到复杂的操作代码无法完全绿色化，故以下功能由用户根据需要选择覆盖相应文件设置才会有效：


--------------------------------以下文件覆盖为可选操作，各人根据需要选择------------------------------------------

1. 开启手机号可以登录需要覆盖以下文件：
\template\default\member\login_simple.htm
\source\function\function_member.php

2. 开启手机短信注册需要覆盖以下文件：
\source\class\class_member.php
同时开启了邮件验证需要以下文件
\source\module\member\member_activate.php

3. 开启购买邀请码短信接收要覆盖以下文件：
\api\trade\notify_invite.php
\source\module\misc\misc_buyinvitecode.php
\template\default\common\buyinvitecode.htm

4. 开启注册短信提醒需要覆盖以下文件：
\source\class\class_member.php

5. 开启修改密码短信提醒需要覆盖以下文件：
\source\include\spacecp\spacecp_profile.php

6. 开启短信找回密码需要覆盖以下文件：
\template\default\member\login.htm
\source\module\member\member_lostpasswd.php

7. 开启强制绑定手机需要覆盖以下文件：
\source\module\forum\forum_post.php

8. 开启强制绑定手机版块需要覆盖以下文件：
\source\module\forum\forum_post.php

9. 不限制强制绑定手机用户组需要覆盖以下文件：
\source\module\forum\forum_post.php

10. 开启参加活动短信通知需要覆盖以下文件：
\source\module\forum\forum_misc.php

11. 开启活动帖审核短信通知需要覆盖以下文件：
\source\module\forum\forum_misc.php

12. 开启活动帖发送短信通知需要覆盖以下文件：
\source\module\forum\forum_misc.php

13. 开启取消参加活动短信通知需要覆盖以下文件：
\source\module\forum\forum_misc.php

14. 开启回复短信通知需要覆盖以下文件：
\source\include\post\post_newreply.php

15. 开启站内短消息提醒需要覆盖以下文件：
\source\function\function_core.php

16. 开启留言板短信提醒需要覆盖以下文件：
\source\include\spacecp\spacecp_comment.php

17. 如果需要QQ注册也验证手机号需要覆盖以下文件：
\source\plugin\qqconnect\template\module.htm

18. 开启站内短消息提醒覆盖以下文件：
\source\include\spacecp\spacecp_comment.php

19. 开启用户发新贴通知站长需要覆盖以下文件：
\source\include\post\post_newthread.php

20. 开启论坛首页显示已经绑定手机会员需要覆盖以下文件：
\source\module\forum\forum_index.php
\template\default\forum\discuz.htm

21. 开启会员发新贴通知对应板块版主需要覆盖以下文件：
\source\include\post\post_newthread.php

22.开启强制绑定手机才能查看帖子需要覆盖以下文件：
\source\module\forum\forum_viewthread.php

23.开启开启购买邀请码短信接收需要覆盖以下文件：
\api\notify_invite.php

24.开启会员积分充值通知站长需要覆盖以下文件：
\api\notify_credit.php

25.开启会员积分变动短信通知会员手机需要覆盖以下文件：
\source\class\class_credit.php

26.开启申请加入群组短信通知群组管理员手机需要覆盖以下文件：
\source\module\forum\forum_group.php

27.开启加入群组审核短信通知会员手机需要覆盖以下文件：
\source\module\forum\forum_group.php

--------------------------------以上文件覆盖为可选操作，各人根据需要选择------------------------------------------


如需以上某个功能只需覆盖插件对应的文件到网站，然后到后台-插件-插件列表-Discuz! X2.5 SMSTong-设置里面开启对应功能开关，更新缓存即可。

手机绑定任务独立包下载：
GBK: http://www.imrobots.cn/download/task_mobile_gbk.rar
UTF8: http://www.imrobots.cn/download/task_mobile_utf8.rar


手机绑定任务设置：
上传\source\class\task\task_mobile.php，\source\language\task\lang_mobile.php文件到网站对应目录然后到后台
管理中心-运营-站点任务-任务类型-绑定手机任务-安装。
管理中心-运营-站点任务-添加-绑定手机任务-任务奖励:积分-积分种类:金钱-积分数量:100-提交。
管理中心-运营-站点任务-管理-勾选绑定手机任务前面的可用-提交。
更新缓存后到前台任务里面就可以领取手机绑定任务了。


开源软件增值服务平台注册地址：http://www.chanyoo.net/mod_static-view-sc_id-1111116.html
现在开始可以免费获取短信条数：http://www.chanyoo.net/mod_bulletin-bulletin_content-bulletin_id-5.html


插件删除方法：
把之前备份的文件文件上传到网站目录覆盖相应的文件。
到后台-插件-插件列里面卸载Discuz! X2.5 SMSTong插件，然后删除\source\plugin\smstong目录


删除以下文件：
\source\class\task\task_mobile.php
\source\language\task\lang_mobile.php
\source\include\cron\cron_birthday_wish_gbk.php或者\source\include\cron\cron_birthday_wish_utf8.php


官方下载：http://www.chanyoo.cn/mod_download-download-dw_id-69.html

此插件相关问题，可以联系作者QQ: 320266360

本插件需要覆盖的文件是在官方最新原版Discuz X2.5（Discuz! X2.5 Release 20120701）的基础上开发的。如果覆盖的文件包括php程序文件或者模板文件你自己曾经手动修改过，请用文本对比程序自己比较要覆盖的文件和你的网站上的文件差异，然后把插件里面的文件跟你网站文件不同的部分拷贝到对应文件的对应位置。切记操作前先做好备份工作！

常见问题：

1. 以前安装过老版本短信通插件的怎么安装这个最新的版本，先把Discuz! X2.5 升级到官方的20120427版本，然后安装我们的这个短信通插件即可。

2. 安装的时候出现SQL错误：Duplicate column name 'mobilestatus'那么是以前安装了老版本的插件导致的，解决办法是先安装，出错了不管直接到插件列表卸载，然后再安装就可以了。

3. 安装后前台操作的地方出现内部错误，解决的办法是安装完插件后设置好参数和功能开关，更新网站缓存，然后清空浏览器缓存，关闭浏览器重新打开浏览器就可以了。

4. 以前安装过老版本短信通插件安装新版后注册页面出现了两个手机号和验证码输入框，解决办法是把官方原版的\template\default\member\register.htm传到网站覆盖，然后更新缓存。

5. 以前安装过老版本短信通插件安装新版后网站后台-全局里面的短信设置和发送短信菜单还在，解决办法是把官方原版的\source\admincp\admincp_menu.php传到网站覆盖，然后更新缓存。

6. 发送短信的时候如果提示发送失败空间不支持CURL或者FSOCKET或者没反应，解决的办法是编辑php.ini配置文件，分别找到配置项extension=php_curl.dll extension=php_sockets.dll extension=php_mbstring.dll 把这三项配置前面的分号删除，修改php.ini配置文件里的allow_url_fopen = On 然后保存php.ini配置文件，重启web服务器，然后再试就应该可以了。

7. 如果extension=php_curl.dll extension=php_sockets.dll extension=php_mbstring.dll三个配置项都是开启的，但是还是提示发送失败，那么就是网站所在服务器太繁忙了，远程调用失败，请先优化服务器性能，或者重启服务器。实在不行请更换空间。

8. 如果发送短信提示失败内容超过最大长度70，原因是插件下发短信默认会带网站名称为短信内容签名，如果您的网站名称很长就会出现这个提示信息，解决的办法是到插件设置里面填写一个简短的短信内容签名。

9. 如果发现插件界面有些文字是英文字符，这是由于插件目录\source\plugin\smstong里面的文件没有全部上传导致的，解决办法就是把目录\source\plugin\smstong全部上传到网站对应目录，然后更新缓存。

10. 用手机号登录失败或者手机短信找回密码提示用户名和手机号不匹配，这个手机号在你网站的两个帐号的个人资料里面，手机号和用户名绑定不唯一造成的，解决办法是清除其中一个帐号里面的手机号。