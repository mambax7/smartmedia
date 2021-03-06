<{include file="db:smartmedia_header.tpl"}>

<table class="category_single">
  <tr>
    <td class="category_item">
     <div align="right"><{$navbar}></div>
      <div class="category_title_link"><{$category.title}></div>
      <table>
        <tr>
          <td class="category_description">
          <{if $category.image_path}>
            <img class="main_image" src="<{$category.image_path}>" align="left" alt="<{$category.clean_title}>" width="<{$category.main_image_width}>">
          <{/if}>
          <{$category.description}>
            <{if $category.adminLinks}>
              <div class="smartmedia_adminlinks"><{$category.adminLinks}></div>
            <{/if}>
          </td>
       </tr>
      </table>
    </td>
  </tr>
</table>

<table class="folder_list">
  <tr>
    <{foreach item=folder from=$folders}>
    <td class="folder_item">
      <div class="folder_title_list"><{$folder.itemlink}></div>
      <table>
        <tr>
          <td class="folder_summary">
            <{if $folder.image_hr_path}>
                <a href="<{$folder.itemurl}>"><img class="list_image" src="<{$folder.image_hr_path}>" align="left" alt="<{$folder.title}>" width="<{$folder.list_image_width}>"></a>
            <{/if}>
            <{$folder.summary}>
            <{if $folder.adminLinks}>
              <div class="smartmedia_adminlinks"><{$folder.adminLinks}></div>
            <{/if}>
          </td>
       </tr>
      </table>
    </td>
    <{if $folder.id % 2 == 0}>
      </tr>
      <tr>
    <{/if}>
    <{/foreach}>
  </tr>
</table>
<{if $navbarbottom==1}>
      <div align="right"><{$navbar}></div>
 <{/if}>

<{include file='db:smartmedia_footer.tpl'}>
