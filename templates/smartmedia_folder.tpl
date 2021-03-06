<{include file="db:smartmedia_header.tpl"}>

<table class="folder_single">
  <tr>
    <td class="folder_item">
      <div align="right"><{$navbar}></div>
      <div class="folder_title_link"><{$folder.title}></div>
      <table>
        <tr>
          <td class="folder_description">
            <{if $folder.image_hr_path}>
              <img class="main_image" src="<{$folder.image_hr_path}>" alt="<{$folder.clean_title}>" align="left" width="<{$folder.main_image_width}>">
            <{/if}>
            <{$folder.description}>
            <{if $folder.adminLinks}>
              <div class="smartmedia_adminlinks"><{$folder.adminLinks}></div>
            <{/if}>
          </td>
       </tr>
      </table>
    </td>
  </tr>
</table>

<table class="clip_list">
  <tr>
    <{foreach item=clip from=$clips}>
    <td class="clip_item">
      <div class="clip_title_list"><{$clip.itemlink}></div>
      <table>
        <tr>
          <td class="clip_description">
            <{if $clip.image_hr_path}>
              <a href="<{$clip.itemurl}>"><img class="main_image" src="<{$clip.image_hr_path}>" align="left" alt="<{$clip.title}>" width="<{$clip.list_image_width}>"></a>
            <{/if}>
            <{$clip.description}>
            <{if $folder.adminLinks}>
              <div class="smartmedia_adminlinks"><{$clip.adminLinks}></div>
            <{/if}>
          </td>
       </tr>
      </table>
    </td>
    <{if $clip.id % 2 == 0}>
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
