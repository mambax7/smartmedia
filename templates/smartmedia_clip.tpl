<{include file="db:smartmedia_header.tpl"}>

<div class="clip_title_link">
  <{$clip.title}>
  <{if $clip.adminLinks}>
    <{$clip.adminLinks}>
  <{/if}>
</div>
<div class="clip_description"><{$clip.description}></div>

<table width="100%">
  <tr>
    <td valign="top" width="<{$clip.width}>">
      <table class="clip_single">
        <tr>
          <td class="clip_item">
            <table>
              <tr>
                <td class="clip_image">
                    <{$clip.template}>
                </td>
             </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table class="clip_links">
              <tr>
                <td align="left" width="50%">
                  <{if $previous_clip_url}>
                    <a href="<{$previous_clip_url}>"><img src="<{$smartmedia_images_url}>400d_flechg.gif" alt="<{$lang_previous_clip}>" ></a>
                    <a href="<{$previous_clip_url}>"><{$lang_previous_clip}></a>
                  <{else}>
                    &nbsp
                  <{/if}>
                </td>
                <td align="right" width="50%">
                  <{if $next_clip_url}>
                    <a href="<{$next_clip_url}>"><{$lang_next_clip}></a>
                    <a href="<{$next_clip_url}>"><img src="<{$smartmedia_images_url}>400d_flechd.gif" alt="<{$lang_next_clip}>" ></a>
                  <{else}>
                    &nbsp
                  <{/if}>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td valign="top" style="width:100%;">
        <style type="text/css">
        #buttonbar { width: 254px; float:left; background: #ffffff url("<{$xoops_url}>/modules/smartmedia/assets/images/bg2.gif") repeat-x left bottom; font-size:93%; line-height:normal; border: 0; margin-bottom: 0; }
        #buttonbar ul {
          margin: 0px 0 0;
          padding:4px 0 0; list-style:none; }
        #buttonbar li { display:inline; margin:0; padding:0; }
        #buttonbar a { float:left; background:url("<{$xoops_url}>/modules/smartmedia/assets/images/left_both.gif") no-repeat left top; margin:0; padding:0 0 0 9px; border-bottom:1px solid #000; text-decoration:none; }
        #buttonbar a span { float:left; display:block; background:url("<{$xoops_url}>/modules/smartmedia/assets/images/right_both.gif") no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
        /* Commented Backslash Hack hides rule from IE5-Mac \*/
        #buttonbar a span {float:none;}
        /* End IE5-Mac hack */
        #buttonbar a:hover span { color:#333; }
        #buttonbar #current a { background-position:0 -150px; border-width:0; }
        #buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
        #buttonbar a:hover { background-position:0 -150px; }
        #buttonbar a:hover span { background-position:100% -150px; }
        </style>



    <{foreach item=tab from=$tabs}>
      <{foreach item=subtab from=$tab.subtabs}>

        <!-- Start of Tab <{$subtab.id_text}> -->
        <div id="layer<{$subtab.id_text}>" style="display: <{$subtab.visibility}>;<{$size}> z-index: 1;">
          <table style="width:254px;" cellspacing="0" cellpadding="0" border="0">
            <tr>
              <td colspan="5">
                <div id="buttonbar">
                  <ul>

                    <{foreach item=tab_button from=$tabs}>
                        <{if $tab.id == $tab_button.id}>
                          <li id="current"><a href="javascript:;" onclick="show('<{$tab_button.first_layer}>', hide('layer<{$subtab.id_text}>'))" ><span><{$tab_button.caption}></span></a></li>
                        <{else}>
                          <li id=''><a href="javascript:;" onclick="show('<{$tab_button.first_layer}>', hide('layer<{$subtab.id_text}>'))" ><span><{$tab_button.caption}></span></a></li>

                        <{/if}>
                    <{/foreach}>
                  </ul>
                </div>
              </td>
            </tr>
            <tr>
              <td width="1" height="8" bgcolor="#990000"><spacer type="block" height="8" width="1"></td>
              <td width="7" height="1" bgcolor="#E1E6E9"><spacer type="block" height="1" width="7"></td>
              <td width="238" height="1" bgcolor="#E1E6E9"><spacer type="block" height="1" width="238"></td>
              <td width="7" height="1" bgcolor="#E1E6E9"><spacer type="block" height="1" width="7"></td>
              <td width="1" height="1" bgcolor="#990000"><spacer type="block" height="1" width="1"></td>
            </tr>

            <tr>
              <td width="1" height="189" bgcolor="#990000"><spacer type="block" height="189" width="1"></td>
              <td width="7" bgcolor="#E1E6E9"><spacer type="block" height="1" width="7"></td>

              <td width="238" valign="top" bgcolor="#E1E6E9">
                <p class="play"><{$subtab.text}></p>
              </td>
              <td width="7" bgcolor="#E1E6E9"><spacer type="block" height="1" width="7"></td>
              <td width="1" bgcolor="#990000"><spacer type="block" height="1" width="1"></td>
            </tr>

            <tr>
              <td width="1" height="5" bgcolor="#990000"><spacer type="block" height="5" width="1"></td>
              <td width="236" height="1" colspan="3" bgcolor="#E1E6E9"><spacer type="block" height="1" width="236"></td>
              <td width="1" height="1" bgcolor="#990000"><spacer type="block" height="1" width="1"></td>
            </tr>

            <tr>
              <td width="1" height="8" bgcolor="#990000"><spacer type="block" height="8" width="1"></td>
              <td width="20" height="1" bgcolor="#E1E6E9"><spacer type="block" height="1" width="7"></td>
              <td width="238" height="1" bgcolor="#E1E6E9">
                <table width="238" cellspacing="0" cellpadding="0" border="0">
                  <tr>
                    <td width="10"><{$subtab.arrow_left}></td>
                    <td width="30"><{$subtab.previous}></td>
                    <td width="100" class="suiv_lay" align="center"><{$subtab.x_of}></td>
                    <td width="30" align="right"><{$subtab.next}></td>
                    <td width="10"><{$subtab.arrow_right}></td>
                  </tr>
                </table>
              </td>
              <td width="7" height="1" bgcolor="#E1E6E9"><spacer type="block" height="1" width="7"></td>
              <td width="1" height="1" bgcolor="#990000"><spacer type="block" height="1" width="1"></td>
            </tr>

            <tr>
              <td width="1" height="5" bgcolor="#990000"><spacer type="block" height="5" width="1"></td>
              <td width="236" height="1" colspan="3" bgcolor="#E1E6E9"><spacer type="block" height="1" width="236"></td>
              <td width="1" height="1" bgcolor="#990000"><spacer type="block" height="1" width="1"></td>
            </tr>

            <tr>
              <td width="254" height="1" colspan="5" bgcolor="#990000"><spacer type="block" height="1" width="254"></td>
            </tr>
          </table>
        </div>
      <!-- End of Tab <{$subtab.id_text}> -->
      <{/foreach}>

    <{/foreach}>
    </td>
  </tr>
</table>

<div class="smartmedia_clip_counter"><{$lang_clip_counter}></div>

<{if $clip.file_hr_path}>
  <div class="smartmedia_highresclip"><{$clip.file_hr_link}></div>
<{/if}>

<{include file='db:smartmedia_footer.tpl'}>
