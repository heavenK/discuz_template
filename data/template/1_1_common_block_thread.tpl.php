<?php if(!defined('IN_DISCUZ')) exit('Access Denied'); ?><?php
$__IMGDIR = IMGDIR;$return = <<<EOF


EOF;
 if($thread['special'] == 1) { 
$return .= <<<EOF

<form method="post" autocomplete="off" action="forum.php">
<input type="hidden" name="mod" value="misc">
<input type="hidden" name="action" value="votepoll">
<input type="hidden" name="fid" value="{$fid}">
<input type="hidden" name="tid" value="{$tid}">
<input type="hidden" name="pollsubmit" value="yes">
<ul class="side_poll">
EOF;
 if(is_array($polloptions)) foreach($polloptions as $key => $option) { 
$return .= <<<EOF
<li>
<label>
<input type="{$optiontype}" name="pollanswers[]" id="option_{$key}" class="pc" value="{$option['polloptionid']}" />
{$option['polloption']}
</label>
</li>

EOF;
 } 
$return .= <<<EOF

</ul>
<p class="ptn pns"><button type="submit" name="submit" id="submit" value="true" class="pn"><em>投票</em></button></p>
</form>

EOF;
 } elseif($thread['special'] == 2) { 
$return .= <<<EOF

<ul class="side_trade cl">
EOF;
 if(is_array($trades)) foreach($trades as $key => $trade) { 
$return .= <<<EOF
<li class="cl">

EOF;
 if($trade['aid']) { 
$return .= <<<EOF

<a href="forum.php?mod=viewthread&amp;do=tradeinfo&amp;tid={$tid}&amp;pid={$trade['pid']}" target="_blank"><img src="{$trade['aid']}" width="80" alt="{$trade['subject']}" /></a>

EOF;
 } else { 
$return .= <<<EOF

<a href="forum.php?mod=viewthread&amp;do=tradeinfo&amp;tid={$tid}&amp;pid={$trade['pid']}" target="_blank"><img src="{$__IMGDIR}/nophoto.gif" width="80" alt="{$trade['subject']}" /></a>

EOF;
 } if($trade['price'] > 0) { 
$return .= <<<EOF

&yen; {$trade['price']}

EOF;
 } if($trade['credit'] > 0) { if($trade['price'] > 0) { 
$return .= <<<EOF
附加 
EOF;
 } 
$return .= <<<EOF
 {$trade['credit']} {$_G['setting']['extcredits'][$_G['setting']['creditstransextra']['5']]['unit']}{$_G['setting']['extcredits'][$_G['setting']['creditstransextra']['5']]['title']}

EOF;
 } 
$return .= <<<EOF

<p><a href="forum.php?mod=viewthread&amp;do=tradeinfo&amp;tid={$tid}&amp;pid={$trade['pid']}" target="_blank">{$trade['subject']}</a></p>
</li>

EOF;
 } 
$return .= <<<EOF

</ul>

EOF;
 } elseif($thread['special'] == 3) { 
$return .= <<<EOF

<label 
EOF;
 if($rewardend) { 
$return .= <<<EOF
style="text-decoration: line-through"
EOF;
 } 
$return .= <<<EOF
>[+{$rewardprice}]</label><div>{$message}</div>

EOF;
 } elseif($thread['special'] == 4) { 
$return .= <<<EOF

<div>{$message}</div>

EOF;
 if($activity['aid']) { 
$return .= <<<EOF

<a href="forum.php?mod=viewthread&amp;tid={$tid}" target="_blank"><img src="{$activity['aid']}" width="80" alt="{$activity['subject']}" /></a>

EOF;
 } else { 
$return .= <<<EOF

<a href="forum.php?mod=viewthread&amp;tid={$tid}" target="_blank"><img src="{$__IMGDIR}/nophoto.gif" width="80" alt="{$activity['subject']}" /></a>

EOF;
 } 
$return .= <<<EOF

<p>已参加人数 {$activity['applynumber']}</p>

EOF;
 if($activity['number']) { 
$return .= <<<EOF

<p>剩余名额: {$activity['aboutmember']}</p>

EOF;
 } } elseif($thread['special'] == 5) { 
$return .= <<<EOF

<div>{$message}</div>
<div class="chart">
<strong class="debater1">{$debate['affirmvotes']}</strong>
<div class="chart1" style="height: {$debate['affirmvoteswidth']}%;">&nbsp;</div>
<strong class="debater2">{$debate['negavotes']}</strong>
<div class="chart2" style="height: {$debate['negavoteswidth']}%;">&nbsp;</div>
</div>
<p>正方: {$debate['affirmpoint']}</p>
<p>反方: {$debate['negapoint']}</p>

EOF;
 } else { 
$return .= <<<EOF
{$message}
EOF;
 } 
$return .= <<<EOF


EOF;
?>