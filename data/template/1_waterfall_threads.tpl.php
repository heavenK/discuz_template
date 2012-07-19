<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?>
<ul id="waterfall"><?php if(is_array($threads)) foreach($threads as $key => $val) { ?><li class="threadwrap">
<div class="thread">
<div class="threadpart">
<a href="forum.php?mod=viewthread&amp;tid=<?php echo $val['tid'];?>" title="<?php echo $val['subject'];?>"> 
<img src="<?php echo $val['image'];?>" border="0" alt="<?php echo $val['subject'];?>" width="<?php echo $picwidth;?>" style="max-height:<?php echo $picmaxheight;?>px;" />
</a>
</div>
<div class="threadpart cl ul">
<h4><a href="forum.php?mod=viewthread&amp;tid=<?php echo $val['tid'];?>" title="<?php echo $val['subject'];?>"><?php echo $val['subject'];?></a></h4>
<cite class="l"><em id="likes"></em>喜爱:<?php echo $val['recommends'];?></cite>
<cite class="r"><em id="views"></em>查看:<?php echo $val['views'];?><em id="replies"></em>回复:<?php echo $val['replies'];?></cite>
</div>
<div class="threadpart cl auth">
<a href="home.php?mod=space&amp;uid=<?php echo $val['authorid'];?>"><img class="l" src="<?php echo $val['avatar'];?>" onerror="this.onerror=null;this.src='<?php echo $noavatar;?>'"/></a>
<span><a href="home.php?mod=space&amp;uid=<?php echo $val['authorid'];?>"><?php echo $val['author'];?></a>&nbsp;&nbsp;发表于&nbsp;<?php echo $val['dateline'];?>.&nbsp;</span>
</div>
</div>
</li>
<?php } ?>
</ul>

