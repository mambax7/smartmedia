<ul>
<{foreach item=clip from=$block.clips}>
    <li><{$clip.itemlink}></li>
  <{/foreach}>
</ul>

<div style="text-align:right; padding: 5px;">
<a href="<{$xoops_url}>/modules/smartmedia/"><{$block.lang_visititem}></a>
</div>