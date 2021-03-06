<{include file="db:smartmedia_header.tpl"}>

<{if $index_msg}>
    <div class="smartmedia_index_msg"><{$index_msg}></div>
<{/if}>

<table class="category_list">
<div align="right"><{$navbar|default:false}></div>
  <tr>
    <{foreach item=category from=$categories}>
    <td class="category_item">
      <div class="category_title_link"><{$category.itemlink}></div>
      <table>
        <tr>
          <td class="category_description">
            <{if $category.image_path}>
              <a href="<{$category.itemurl}>"><img class="list_image" src="<{$category.image_path}>" align="left" alt="<{$category.title}>" width="<{$category.list_image_width}>" ></a>
            <{/if}>
            <{$category.description}>
            <{if $category.adminLinks}>
            <div class="smartmedia_adminlinks"><{$category.adminLinks}></div>
            <{/if}>
          </td>
       </tr>
      </table>
    </td>
    <{if $category.id % 2 == 0}>
      </tr>
      <tr>
    <{/if}>
    <{/foreach}>

  </tr>

</table>
<{if $navbarbottom|default:0==1}>
      <div align="right"><{$navbar}></div>
 <{/if}>

<{include file='db:smartmedia_footer.tpl'}>
