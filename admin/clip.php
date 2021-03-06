<?php

/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

use XoopsModules\Smartmedia\{
    Helper,
    Utility,
    Tree
};
use Xmf\Module\Admin;
use Xmf\Request;

/** @var Helper $helper */

require_once __DIR__ . '/admin_header.php';
//xoops_cp_header();

$helper = Helper::getInstance();

/**
 * @param $item
 */
function displayClipItem($item)
{
    global $xoopsModule, $pathIcon16;
    //var_dump($folderObj);
    $modify = "<a href='clip.php?op=mod&clipid=" . $item['clipid'] . '&folderid=' . $item['folderid'] . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SMARTMEDIA_CLIP_EDIT . "' alt='" . _AM_SMARTMEDIA_CLIP_EDIT . "'></a>";
    $delete = "<a href='clip.php?op=del&clipid=" . $item['clipid'] . '&folderid=' . $item['folderid'] . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SMARTMEDIA_CLIP_DELETE . "' alt='" . _AM_SMARTMEDIA_CLIP_DELETE . "'></a>";

    echo '<tr>';
    echo "<td class='even' align='left'>&nbsp;&nbsp;</td>";
    echo "<td class='even' align='left'>"
         . "<a href='"
         . XOOPS_URL
         . '/modules/'
         . $xoopsModule->dirname()
         . '/clip.php?categoryid='
         . $item['categoryid']
         . '&folderid='
         . $item['folderid']
         . '&clipid='
         . $item['clipid']
         . "'><img src='"
         . SMARTMEDIA_URL
         . "images/icon/clip.gif' alt=''>&nbsp;"
         . $item['title']
         . '</a></td>';
    echo "<td class='even' align='left'>" . $item['foldertitle'] . '</td>';
    echo "<td class='even' align='center'>" . $item['weight'] . '</td>';
    echo "<td class='even' align='right'> $modify $delete </td>";
    echo '</tr>';
}

require_once __DIR__ . '/admin_header.php';

global $smartmediaClipHandler;

$op = Request::getCmd('op', '');

/* Possible $op :
 mod : Displaying the form to edit or add a clip
 mod_text : Displaying the form to edit a clip language info
 add_clip : Adding or editing a clip in the db
 add_clip_text : Adding or editing a clip language info in the db
 del : deleting a clip
 show : show the clips related to the folder
 */

// At what clip do we start
$startclip = Request::getInt('startclip', 0, 'GET');

// Display a single clip
/**
 * @param $clip_textObj
 */
function displayClip_text($clip_textObj)
{
    global $xoopsModule, $smartmediaClipHandler, $pathIcon16;

    $modify = "<a href='clip.php?op=modtext&clipid=" . $clip_textObj->clipid() . '&languageid=' . $clip_textObj->languageid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SMARTMEDIA_CLIP_EDIT . "' alt='" . _AM_SMARTMEDIA_CLIP_EDIT . "'></a>";
    $delete = "<a href='clip.php?op=deltext&clipid=" . $clip_textObj->clipid() . '&languageid=' . $clip_textObj->languageid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SMARTMEDIA_CLIP_DELETE . "' alt='" . _AM_SMARTMEDIA_CLIP_DELETE . "'></a>";
    echo '<tr>';
    echo "<td class='even' align='left'>" . $clip_textObj->languageid() . '</td>';
    echo "<td class='even' align='left'> " . $clip_textObj->title() . ' </td>';
    echo "<td class='even' align='center'> " . $modify . $delete . ' </td>';
    echo '</tr>';
}

// Add or edit a clip or a clip language info in the db
/**
 * @param bool $language_text
 */
function addClip($language_text = false)
{
    global $xoopsUser, $xoopsConfig, $xoopsModule, $myts, $smartmediaClipHandler;
    $helper = Helper::getInstance();
    require_once XOOPS_ROOT_PATH . '/class/uploader.php';

    $max_size          = 10000000;
    $max_imgwidth      = 1000;
    $max_imgheight     = 1000;
    $allowed_mimetypes = Utility::getAllowedMimeTypes();
    $upload_msgs       = [];

    $clipid = Request::getInt('clipid', 0, 'POST');

    if (Request::hasVar('languageid', 'POST')) {
        $languageid = $_POST['languageid'];
    } elseif (Request::hasVar('default_languageid', 'POST')) {
        $languageid = $_POST['default_languageid'];
    } else {
        $languageid = $helper->getConfig('default_language');
    }

    if (0 != $clipid) {
        $clipObj = $smartmediaClipHandler->get($clipid, $languageid);
    } else {
        $clipObj = $smartmediaClipHandler->create();
    }

    if (!$language_text) {
        /*      // Upload lr_image
         if ($_FILES['lr_image_file']['name'] != "") {
         $filename = $_POST["xoops_upload_file"][0] ;
         if ( !empty( $filename ) || $filename != "" ) {

         if ( $_FILES[$filename]['tmp_name'] == "" || ! is_readable( $_FILES[$filename]['tmp_name'] ) ) {
         $upload_msgs[_AM_SMARTMEDIA_FILEUPLOAD_ERROR];
         } else {
         $uploader = new \XoopsMediaUploader(Utility::getImageDir('clip'), $allowed_mimetypes, $max_size, $max_imgwidth, $max_imgheight);

         if ( $uploader->fetchMedia( $filename ) && $uploader->upload() ) {
         $clipObj->setVar('image_lr', $uploader->getSavedFileName());
         } else {
         $upload_msgs[_AM_SMARTMEDIA_FILEUPLOAD_ERROR];
         }
         }
         }
         } else {
         $clipObj->setVar('image_lr', $_POST['image_lr']);
         }
         */
        // Upload hr_image
        if ('' != $_FILES['hr_image_file']['name']) {
            $filename = $_POST['xoops_upload_file'][0];
            if (!empty($filename) || '' != $filename) {
                if ('' == $_FILES[$filename]['tmp_name'] || !is_readable($_FILES[$filename]['tmp_name'])) {
                    $upload_msgs[_AM_SMARTMEDIA_FILEUPLOAD_ERROR];
                } else {
                    $uploader = new \XoopsMediaUploader(Utility::getImageDir('clip'), $allowed_mimetypes, $max_size, $max_imgwidth, $max_imgheight);

                    if ($uploader->fetchMedia($filename) && $uploader->upload()) {
                        $clipObj->setVar('image_hr', $uploader->getSavedFileName());
                    } else {
                        $upload_msgs[_AM_SMARTMEDIA_FILEUPLOAD_ERROR];
                    }
                }
            }
        } else {
            $clipObj->setVar('image_hr', $_POST['image_hr']);
        }

        //var_dump($uploader->errors);
        //exit;

        $clipObj->setVar('width', Request::getInt('width', 320, 'POST'));
        $clipObj->setVar('height', Request::getInt('height', 260, 'POST'));
        $clipObj->setVar('folderid', Request::getInt('folderid', 0, 'POST'));
        $clipObj->setVar('weight', Request::getInt('weight', 1, 'POST'));
        $clipObj->setVar('file_hr', $_POST['file_hr']);
        $clipObj->setVar('file_lr', $_POST['file_lr']);
        $clipObj->setVar('formatid', $_POST['formatid']);
        $clipObj->setVar('default_languageid', isset($_POST['default_languageid']) ? $_POST['default_languageid'] : $helper->getConfig('default_language'));
        $clipObj->setTextVar('languageid', isset($_POST['default_languageid']) ? $_POST['default_languageid'] : $helper->getConfig('default_language'));
    } else {
        $clipObj->setTextVar('languageid', $languageid);
    }

    $clipObj->setTextVar('languageid', $languageid);
    $clipObj->setTextVar('title', $_POST['title']);
    $clipObj->setTextVar('description', $_POST['description']);
    $clipObj->setTextVar('meta_description', $_POST['meta_description']);
    $clipObj->setTextVar('tab_caption_1', $_POST['tab_caption_1']);
    $clipObj->setTextVar('tab_text_1', $_POST['tab_text_1']);
    $clipObj->setTextVar('tab_caption_2', $_POST['tab_caption_2']);
    $clipObj->setTextVar('tab_text_2', $_POST['tab_text_2']);
    $clipObj->setTextVar('tab_caption_3', $_POST['tab_caption_3']);
    $clipObj->setTextVar('tab_text_3', $_POST['tab_text_3']);

    if (!$xoopsUser) {
        $uid = 0;
    } else {
        $uid = $xoopsUser->uid();
    }

    $clipObj->setVar('modified_uid', $uid);

    if ($clipObj->isNew()) {
        $clipObj->setVar('created_uid', $uid);
        $redirect_msg = _AM_SMARTMEDIA_CLIP_CREATED;
        $redirect_to  = 'clip.php';
    } else {
        if ($language_text) {
            $redirect_to = 'clip.php?op=mod&clipid=' . $clipObj->clipid();
        } else {
            if (Request::hasVar('from_within', 'GET')) {
                // To come...
            }
            $redirect_to = 'clip.php';
        }
        $redirect_msg = _AM_SMARTMEDIA_CLIP_MODIFIED;
    }

    if (!$clipObj->store()) {
        redirect_header('<script>javascript:history.go(-1)</script>', 3, _AM_SMARTMEDIA_CLIP_SAVE_ERROR . Utility::formatErrors($clipObj->getErrors()));
        exit;
    }

    redirect_header($redirect_to, 2, $redirect_msg);

    exit();
}

// Edit clip information. Also used to add a clip
/**
 * @param bool $showmenu
 * @param int  $clipid
 * @param int  $folderid
 */
function editclip($showmenu = false, $clipid = 0, $folderid = 0)
{
    global $xoopsDB, $smartmediaClipHandler, $xoopsUser, $myts, $xoopsConfig, $xoopsModule;
    $helper = Helper::getInstance();
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    // if $clipid == 0 then we are adding a new clip
    $newClip = (0 == $clipid);

    echo '<script type="text/javascript" src="../assets/js/funcs.js"></script>';
    echo '<style>';
    echo '<!-- ';
    echo 'select { width: 130px; }';
    echo '-->';
    echo '</style>';
    $cat_sel = '';

    if (!$newClip) {
        // We are editing a clip

        // Creating the clip object for the selected clip
        $clipObj = $smartmediaClipHandler->get($clipid);
        $cat_sel = '&clipid=' . $clipObj->clipid();
        $clipObj->loadLanguage($clipObj->default_languageid());

        //if ($showmenu) {
        //smartmedia_adminMenu(3, _AM_SMARTMEDIA_CLIPS . " > " . _AM_SMARTMEDIA_EDITING);
        //}
        echo "<br>\n";
        if ($clipObj->notLoaded()) {
            redirect_header('clip.php', 1, _AM_SMARTMEDIA_NOCLIPTOEDIT);
            exit();
        }
        Utility::collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SMARTMEDIA_CLIP_EDIT . '</h3>';
        echo "<div id='bottomtable'>";
        echo '<span style="color: #567; margin: 3px 0 18px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CLIP_EDIT_INFO . '</span>';
    } else {
        // We are creating a new clip

        $clipObj = $smartmediaClipHandler->create();
        if ($showmenu) {
            //smartmedia_adminMenu(3, _AM_SMARTMEDIA_CLIPS . " > " . _AM_SMARTMEDIA_CREATINGNEW);
        }
        echo "<br>\n";
        Utility::collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SMARTMEDIA_CLIP_CREATE . '</h3>';
        echo "<div id='bottomtable'>";
        echo '<span style="color: #567; margin: 3px 0 18px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CLIP_CREATE_INFO . '</span>';
    }
    if (!$newClip) {
        /* If it's not a new clip, lets display the already created clip language info
         for this clip, as well as a button to create a new clip language info */

        if ($clipObj->canAddLanguage()) {
            // If not all languages have been added
            $adminObject = Admin::getInstance();
            //$adminObject->displayNavigation('partner.php');

            $adminObject->addItemButton(_AM_SMARTMEDIA_CLIP_TEXT_CREATE, 'clip.php?op=modtext&clipid=' . $clipObj->clipid(), 'add', '');
            $adminObject->displayButton('left', '');

            //            echo "<form><div style=\"margin-bottom: 0px;\">";
            //            echo "<input type='button' name='button' onclick=\"location='clip.php?op=modtext&clipid=" . $clipObj->clipid() . "'\" value='" . _AM_SMARTMEDIA_CLIP_TEXT_CREATE . "'>&nbsp;&nbsp;";
            //            echo "</div></form>";
            //            echo "</div>";
        }

        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<td width='20%' class='bg3' align='left'><b>" . _AM_SMARTMEDIA_LANGUAGE . '</b></td>';
        echo "<td class='bg3' align='left'><b>" . _AM_SMARTMEDIA_CLIP_TITLE . '</b></td>';
        echo "<td width='60' class='bg3' align='center'><b>" . _AM_SMARTMEDIA_ACTION . '</b></td>';
        echo '</tr>';

        $clip_textObjs = $clipObj->getAllLanguages(true);
        if (count($clip_textObjs) > 0) {
            foreach ($clip_textObjs as $key => $thiscat) {
                displayClip_text($thiscat);
            }
        } else {
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '3'>" . _AM_SMARTMEDIA_NO_LANGUAGE_INFO . '</td>';
            echo '</tr>';
        }

        echo "</table>\n<br>";

    }

    // Start clip form

    $sform = new \XoopsThemeForm(_AM_SMARTMEDIA_CLIP, 'op', xoops_getenv('SCRIPT_NAME'));
    $sform->setExtra('enctype="multipart/form-data"');
    $sform->addElement(new \XoopsFormHidden('clipid', $clipid));

    // Language
    $languageid_select = new \XoopsFormSelectLang(_AM_SMARTMEDIA_LANGUAGE_ITEM, 'default_languageid', $clipObj->default_languageid());
    $languageid_select->setDescription(_AM_SMARTMEDIA_LANGUAGE_ITEM_DSC);
    $languageid_select->addOptionArray(\XoopsLists::getLangList());
    if (!$newClip) {
        $languageid_select->setExtra("style='color: grey;' disabled='disabled'");
    }
    $sform->addElement($languageid_select);

    // title
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_TITLE_REQ, 'title', 50, 255, $clipObj->title('e')), true);

    // Description
    $desc = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_DESCRIPTION, 'description', $clipObj->description('e'), 7, 60);
    $desc->setDescription(_AM_SMARTMEDIA_CLIP_DESCRIPTIONDSC);
    $sform->addElement($desc);

    // Meta-Description
    $meta = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_META_DESCRIPTION, 'meta_description', $clipObj->meta_description('e'), 7, 60);
    $meta->setDescription(_AM_SMARTMEDIA_CLIP_META_DESCRIPTIONDSC);
    $sform->addElement($meta);

    $sform->addElement(new XoopsFormHidden('itemType', 'item_text'));

    // tab_caption_1
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_TAB_CAPTION_1, 'tab_caption_1', 50, 50, $clipObj->tab_caption_1()), false);

    // tab_text_1
    $tab1_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_TAB_TEXT_1, 'tab_text_1', $clipObj->tab_text_1('e'), 7, 60);
    $tab1_text->setDescription(_AM_SMARTMEDIA_CLIP_TABDSC);
    $sform->addElement($tab1_text);

    // tab_caption_2
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_TAB_CAPTION_2, 'tab_caption_2', 50, 50, $clipObj->tab_caption_2()), false);

    // tab_text_2
    $tab2_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_TAB_TEXT_2, 'tab_text_2', $clipObj->tab_text_2('e'), 7, 60);
    $tab2_text->setDescription(_AM_SMARTMEDIA_CLIP_TABDSC);
    $sform->addElement($tab2_text);

    // tab_caption_3
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_TAB_CAPTION_3, 'tab_caption_3', 50, 50, $clipObj->tab_caption_3()), false);

    // tab_text_3
    $tab3_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_TAB_TEXT_3, 'tab_text_3', $clipObj->tab_text_3('e'), 7, 60);
    $tab3_text->setDescription(_AM_SMARTMEDIA_CLIP_TABDSC);
    $sform->addElement($tab3_text);

    // Folder
    //    require_once SMARTMEDIA_ROOT_PATH . "class/Tree.php";
    $mySmartTree = new Tree($xoopsDB->prefix('smartmedia_folders'), 'folderid', '');
    ob_start();
    $mySmartTree->makeMySelBox('title', 'weight', $folderid, 0, 'folderid');

    //makeMySelBox($title,$order="",$preset_id=0, $none=0, $sel_name="", $onchange="")
    $sform->addElement(new XoopsFormLabel(_AM_SMARTMEDIA_FOLDER, ob_get_contents()));
    ob_end_clean();

    // file_lr
    $lores = new \XoopsFormText(_AM_SMARTMEDIA_CLIP_FILE_LR, 'file_lr', 50, 255, $clipObj->file_lr(), false);
    $lores->setDescription(_AM_SMARTMEDIA_CLIP_FILE_LRDSC);
    $sform->addElement($lores);

    // file_hr
    $hires = new \XoopsFormText(_AM_SMARTMEDIA_CLIP_FILE_HR, 'file_hr', 50, 255, $clipObj->file_hr(), false);
    $hires->setDescription(_AM_SMARTMEDIA_CLIP_FILE_HRDSC);
    $sform->addElement($hires);

    // format
    $format_select = new \XoopsFormSelect(_AM_SMARTMEDIA_CLIP_FORMAT, 'formatid', $clipObj->formatid());
    $format_select->addOptionArray(smartmedia_getFormatArray(true));
    $sform->addElement($format_select);

    // width
    $width_text = new \XoopsFormText(_AM_SMARTMEDIA_CLIP_WIDTH, 'width', 20, 20, $clipObj->width(), false);
    $width_text->setDescription(_AM_SMARTMEDIA_CLIP_WIDTHDSC);
    $sform->addElement($width_text);

    // height
    $height_text = new \XoopsFormText(_AM_SMARTMEDIA_CLIP_HEIGHT, 'height', 20, 20, $clipObj->height(), false);
    $height_text->setDescription(_AM_SMARTMEDIA_CLIP_HEIGHTDSC);
    $sform->addElement($height_text);

    /*  // LR IMAGE
     $lr_image_array = & XoopsLists :: getImgListAsArray(Utility::getImageDir('clip') );
     $lr_image_select = new \XoopsFormSelect( '', 'image_lr', $clipObj->image_lr() );
     $lr_image_select -> addOption ('-1', '---------------');
     $lr_image_select -> addOptionArray( $lr_image_array );
     $lr_image_select -> setExtra( "onchange='showImgSelected(\"the_image_lr\", \"image_lr\", \"" . 'uploads/smartmedia/images/clip' . "\", \"\", \"" . XOOPS_URL . "\")'" );
     $lr_image_tray = new \XoopsFormElementTray( _AM_SMARTMEDIA_CLIP_IMAGE_LR, '&nbsp;' );
     $lr_image_tray -> addElement( $lr_image_select );
     $lr_image_tray -> addElement( new \XoopsFormLabel( '', "<br><br><img src='" .Utility::getImageDir('clip', false) .$clipObj->image_lr() . "' name='the_image_lr' id='the_image_lr' alt=''>" ) );
     $lr_image_tray->setDescription(_AM_SMARTMEDIA_CLIP_IMAGE_LR_DSC);
     $sform -> addElement( $lr_image_tray );

     // LR IMAGE UPLOAD
     $max_size = 5000000;
     $lr_file_box = new \XoopsFormFile(_AM_SMARTMEDIA_CLIP_IMAGE_LR_UPLOAD, "lr_image_file", $max_size);
     $lr_file_box->setExtra( "size ='45'") ;
     $lr_file_box->setDescription(_AM_SMARTMEDIA_CLIP_IMAGE_LR_UPLOAD_DSC);
     $sform->addElement($lr_file_box);
     */
    // HR IMAGE
    $hr_image_array  = \XoopsLists:: getImgListAsArray(Utility::getImageDir('clip'));
    $hr_image_select = new \XoopsFormSelect('', 'image_hr', $clipObj->image_hr());
    $hr_image_select->addOption('-1', '---------------');
    $hr_image_select->addOptionArray($hr_image_array);
    $hr_image_select->setExtra("onchange='showImgSelected(\"the_image_hr\", \"image_hr\", \"" . 'uploads/smartmedia/images/clip' . '", "", "' . XOOPS_URL . "\")'");
    $hr_image_tray = new \XoopsFormElementTray(_AM_SMARTMEDIA_CLIP_IMAGE_HR, '&nbsp;');
    $hr_image_tray->addElement($hr_image_select);
    $hr_image_tray->addElement(new XoopsFormLabel('', "<br><br><img src='" . Utility::getImageDir('clip', false) . $clipObj->image_hr() . "' name='the_image_hr' id='the_image_hr' alt=''>"));
    $hr_image_tray->setDescription(sprintf(_AM_SMARTMEDIA_CLIP_IMAGE_HR_DSC, $helper->getConfig('main_image_width')));
    $sform->addElement($hr_image_tray);

    // HR IMAGE UPLOAD
    $max_size    = 5000000;
    $hr_file_box = new \XoopsFormFile(_AM_SMARTMEDIA_CLIP_IMAGE_HR_UPLOAD, 'hr_image_file', $max_size);
    $hr_file_box->setExtra("size ='45'");
    $hr_file_box->setDescription(_AM_SMARTMEDIA_CLIP_IMAGE_HR_UPLOAD_DSC);
    $sform->addElement($hr_file_box);

    // Weight
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_WEIGHT, 'weight', 4, 4, $clipObj->weight()));

    $sform->addElement(new XoopsFormHidden('itemType', 'item'));

    if (Request::hasVar('from_within', 'GET')) {
        $sform->addElement(new XoopsFormHidden('from_within', 1));
    }

    // Action buttons tray
    $buttonTray = new \XoopsFormElementTray('', '');

    $hidden = new \XoopsFormHidden('op', 'addclip');
    $buttonTray->addElement($hidden);

    if ($newClip) {
        // We are creating a new clip

        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addclip\'"');
        $buttonTray->addElement($butt_create);

        $butt_clear = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CLEAR, 'reset');
        $buttonTray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    } else {
        // We are editing a clip
        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addclip\'"');
        $buttonTray->addElement($butt_create);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    }

    $sform->addElement($buttonTray);
    $sform->display();
    echo '</div>';
    unset($hidden);
}

// Edit clip language info. Also used to add a new clip language info
/**
 * @param bool $showmenu
 * @param      $clipid
 * @param      $languageid
 */
function editclip_text($showmenu, $clipid, $languageid)
{
    global $xoopsDB, $smartmediaClipHandler, $xoopsUser, $myts, $xoopsConfig, $xoopsModule;
    $helper = Helper::getInstance();
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    echo '<script type="text/javascript" src="../assets/js/funcs.js"></script>';
    echo '<style>';
    echo '<!-- ';
    echo 'select { width: 130px; }';
    echo '-->';
    echo '</style>';

    $cat_sel = '';

    $clipObj = $smartmediaClipHandler->get($clipid, $languageid);

    if ('new' === $languageid) {
        $bread_lang = _AM_SMARTMEDIA_CREATE;
    } else {
        $bread_lang = ucfirst($languageid);
    }

    if ($showmenu) {
        //smartmedia_adminMenu(3, _AM_SMARTMEDIA_CLIPS . " > " . _AM_SMARTMEDIA_LANGUAGE_INFO . " > " . $bread_lang);
    }
    echo "<br>\n";
    Utility::collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SMARTMEDIA_CLIP_LANGUAGE_INFO_EDITING . '</h3>';
    echo "<div id='bottomtable'>";
    echo '<span style="color: #567; margin: 3px 0 18px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CLIP_LANGUAGE_INFO_EDITING_INFO . '</span>';

    // Start clip form
    $sform = new \XoopsThemeForm(_AM_SMARTMEDIA_CLIP, 'op', xoops_getenv('SCRIPT_NAME'));
    $sform->setExtra('enctype="multipart/form-data"');
    $sform->addElement(new XoopsFormHidden('clipid', $clipid));

    // Language
    $languageOptions  = [];
    $languageList     = \XoopsLists::getLangList();
    $createdLanguages = $clipObj->getCreatedLanguages();
    foreach ($languageList as $language) {
        if (('new' !== $languageid) || (!in_array($language, $createdLanguages))) {
            $languageOptions[$language] = $language;
        }
    }
    $language_select = new \XoopsFormSelect(_AM_SMARTMEDIA_LANGUAGE_ITEM, 'languageid', $languageid);
    $language_select->addOptionArray($languageOptions);
    $language_select->setDescription(_AM_SMARTMEDIA_LANGUAGE_ITEM_DSC);

    if ('new' !== $languageid) {
        $language_select->setExtra(smartmedia_make_control_disabled());
        $sform->addElement(new XoopsFormHidden('languageid', $languageid));
    }
    $sform->addElement($language_select, true);

    // Description
    $desc = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_DESCRIPTION, 'description', $clipObj->description('e'), 7, 60);
    $desc->setDescription(_AM_SMARTMEDIA_CLIP_DESCRIPTIONDSC);
    $sform->addElement($desc);

    // Meta-Description
    $meta = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_META_DESCRIPTION, 'meta_description', $clipObj->meta_description('e'), 7, 60);
    $meta->setDescription(_AM_SMARTMEDIA_CLIP_META_DESCRIPTIONDSC);
    $sform->addElement($meta);

    $sform->addElement(new XoopsFormHidden('itemType', 'item_text'));

    // tab_caption_1
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_TAB_CAPTION_1, 'tab_caption_1', 50, 50, $clipObj->tab_caption_1()), false);

    // tab_text_1
    $tab1_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_TAB_TEXT_1, 'tab_text_1', $clipObj->tab_text_1('e'), 7, 60);
    $tab1_text->setDescription(_AM_SMARTMEDIA_CLIP_TABDSC);
    $sform->addElement($tab1_text);

    // tab_caption_2
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_TAB_CAPTION_2, 'tab_caption_2', 50, 50, $clipObj->tab_caption_2()), false);

    // tab_text_2
    $tab2_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_TAB_TEXT_2, 'tab_text_2', $clipObj->tab_text_2('e'), 7, 60);
    $tab2_text->setDescription(_AM_SMARTMEDIA_CLIP_TABDSC);
    $sform->addElement($tab2_text);

    // tab_caption_3
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CLIP_TAB_CAPTION_3, 'tab_caption_3', 50, 50, $clipObj->tab_caption_3()), false);

    // tab_text_3
    $tab3_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_CLIP_TAB_TEXT_3, 'tab_text_3', $clipObj->tab_text_3('e'), 7, 60);
    $tab3_text->setDescription(_AM_SMARTMEDIA_CLIP_TABDSC);
    $sform->addElement($tab3_text);
    // Action buttons tray
    $buttonTray = new \XoopsFormElementTray('', '');

    $hidden = new \XoopsFormHidden('op', 'addclip_text');
    $buttonTray->addElement($hidden);

    if ('new' === $languageid) {
        // We are creating a new clip language info

        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addclip_text\'"');
        $buttonTray->addElement($butt_create);

        $butt_clear = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CLEAR, 'reset');
        $buttonTray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    } else {
        // We are editing a clip language info

        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addclip_text\'"');
        $buttonTray->addElement($butt_create);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $buttonTray->addElement($butt_cancel);
    }
    $sform->addElement($buttonTray);
    $sform->display();
    echo '</div>';
    unset($hidden);
}

switch ($op) {
    // Displaying the form to edit or add a clip
    case 'mod':
        //default:
        $clipid   = Request::getInt('clipid', 0, 'GET');
        $folderid = Request::getInt('folderid', 0, 'GET');
        xoops_cp_header();
        $adminObject = Admin::getInstance();
        $adminObject->displayNavigation('clip.php');
        editclip(true, $clipid, $folderid);
        break;
    // Displaying the form to edit a clip language info
    case 'modtext':
        $clipid     = Request::getInt('clipid', 0, 'GET');
        $languageid = isset($_GET['languageid']) ? $_GET['languageid'] : 'new';

        xoops_cp_header();
        $adminObject = Admin::getInstance();
        $adminObject->displayNavigation('clip.php');
        editclip_text(true, $clipid, $languageid);
        break;
    // Adding or editing a clip in the db
    case 'addclip':
        addClip(false);
        break;
    // Adding or editing a clip language info in the db
    case 'addclip_text':
        addClip(true);
        break;
    // deleting a clip
    case 'del':
        global $smartmediaClipHandler, $xoopsUser, $xoopsConfig, $xoopsDB, $_GET;

        $module_id = $xoopsModule->getVar('mid');
        /** @var \XoopsGroupPermHandler $grouppermHandler */
        $grouppermHandler = xoops_getHandler('groupperm');

        $clipid = Request::getInt('clipid', 0, 'POST');
        $clipid = Request::getInt('clipid', $clipid, 'GET');

        $clipObj = $smartmediaClipHandler->get($clipid);

        $confirm = Request::getInt('confirm', 0, 'POST');
        $name    = Request::getString('name', '', 'POST');

        if ($confirm) {
            if (!$smartmediaClipHandler->delete($clipObj)) {
                redirect_header('clip.php', 1, _AM_SMARTMEDIA_CLIP_DELETE_ERROR);
                exit;
            }

            redirect_header('clip.php', 1, sprintf(_AM_SMARTMEDIA_CLIP_DELETE_SUCCESS, $name));
            exit();
        }
        // no confirm: show deletion condition
        xoops_cp_header();
        xoops_confirm(['op' => 'del', 'clipid' => $clipObj->clipid(), 'confirm' => 1, 'name' => $clipObj->title()], 'clip.php', _AM_SMARTMEDIA_CLIP_DELETE . " '" . $clipObj->title() . "' ?", _AM_SMARTMEDIA_DELETE);
        xoops_cp_footer();

        exit();
        break;
    case 'deltext':
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $_GET;

        $cliptextHandler = Helper::getInstance()->getHandler('ClipText');

        $module_id = $xoopsModule->getVar('mid');

        $clipid = Request::getInt('clipid', 0, 'POST');
        $clipid = Request::getInt('clipid', $clipid, 'GET');

        $languageid = isset($_POST['languageid']) ? $_POST['languageid'] : null;
        $languageid = isset($_GET['languageid']) ? $_GET['languageid'] : $languageid;

        $clip_textObj = $cliptextHandler->get($clipid, $languageid);

        $confirm = Request::getInt('confirm', 0, 'POST');
        $name    = Request::getString('name', '', 'POST');

        if ($confirm) {
            if (!$cliptextHandler->delete($clip_textObj)) {
                redirect_header('clip.php?op=mod&clipid=' . $clip_textObj->clipid(), 1, _AM_SMARTMEDIA_CLIP_TEXT_DELETE_ERROR);
                exit;
            }

            redirect_header('clip.php?op=mod&clipid=' . $clip_textObj->clipid(), 1, sprintf(_AM_SMARTMEDIA_CLIP_TEXT_DELETE_SUCCESS, $name));
            exit();
        }
        // no confirm: show deletion condition
        $clipid     = Request::getInt('clipid', 0, 'GET');
        $languageid = isset($_GET['languageid']) ? $_GET['languageid'] : null;
        xoops_cp_header();
        xoops_confirm(['op' => 'deltext', 'clipid' => $clip_textObj->clipid(), 'languageid' => $clip_textObj->languageid(), 'confirm' => 1, 'name' => $clip_textObj->languageid()], 'clip.php?op=mod&clipid=' . $clip_textObj->clipid(), _AM_SMARTMEDIA_CLIP_TEXT_DELETE, _AM_SMARTMEDIA_DELETE);
        xoops_cp_footer();

        exit();
        break;
    case 'cancel':
        redirect_header('clip.php', 1, sprintf(_AM_SMARTMEDIA_BACK2IDX, ''));
        exit();

    case 'show_within':
        xoops_cp_header();
        $adminObject = Admin::getInstance();
        $adminObject->displayNavigation('clip.php');

        $folderid   = Request::getInt('folderid', 0, 'GET');
        $categoryid = Request::getInt('categoryid', 0, 'GET');

        $folderObj = $folderHandler->get($folderid);

        //smartmedia_adminMenu(3, sprintf(_AM_SMARTMEDIA_CLIPS_WITHIN_FOLDER, $folderObj->title('clean')));

        echo "<br>\n";

        // Creating the objects for clips
        $clipsFoldersObj = $smartmediaClipHandler->getClips(0, 0, $folderid);

        $array_keys  = array_keys($clipsFoldersObj);
        $criteria_id = new \CriteriaCompo();
        foreach ($array_keys as $key) {
            $criteria_id->add(new \Criteria('parent.folderid', $key), 'or');
        }
        $criteria = new \CriteriaCompo();
        $criteria->add($criteria_id);
        Utility::collapsableBar('toptable', 'toptableicon');
        echo "<img id='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SMARTMEDIA_CLIPS_TITLE . '</h3>';
        echo "<div id='toptable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . sprintf(_AM_SMARTMEDIA_CLIPS_DSC, $folderObj->title('clean')) . '</span>';

        echo '<form><div style="margin-top: 0; margin-bottom: 5px;">';
        echo "<input type='button' name='button' onclick=\"location='clip.php?op=mod&folderid=" . $folderid . "'\" value='" . _AM_SMARTMEDIA_CLIP_CREATE . "'>&nbsp;&nbsp;";
        echo '</div></form>';

        // Clips
        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<td colspan='2' width='300px' class='bg3' align='left'><b>" . _AM_SMARTMEDIA_FOLDER_CLIP . '</b></td>';
        echo "<td class='bg3' align='center'><b>" . _AM_SMARTMEDIA_DESCRIPTION . '</b></td>';
        echo "<td class='bg3'width='100' align='center'><b>" . _AM_SMARTMEDIA_WEIGHT . '</b></td>';
        echo "<td width='60' class='bg3' align='center'><b>" . _AM_SMARTMEDIA_ACTION . '</b></td>';
        echo '</tr>';
        $level = 0;

        $foldersObj = $folderHandler->getObjects(0, $criteria, false);
        if (count($foldersObj) > 0) {
            foreach ($foldersObj as $folderObj) {
                //var_dump($folderObj);
                displayFolderForClip($folderObj, 0, true, $clipsFoldersObj, $categoryid, true);
            }
        } else {
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SMARTMEDIA_NOCAT . '</td>';
            echo '</tr>';
        }

        echo "</table>\n";

        echo '</div>';

        //  editclip(false);
        break;
    case 'show':
    default:
        xoops_cp_header();
        $adminObject = Admin::getInstance();
        $adminObject->displayNavigation('clip.php');

        $folderid = Request::getInt('folderid', 0, 'GET');

        $languagesel = isset($_GET['languagesel']) ? $_GET['languagesel'] : 'all';
        $languagesel = isset($_POST['languagesel']) ? $_POST['languagesel'] : $languagesel;

        $sortsel = isset($_GET['sortsel']) ? $_GET['sortsel'] : 'clips_text.title';
        $sortsel = isset($_POST['sortsel']) ? $_POST['sortsel'] : $sortsel;

        $ordersel = isset($_GET['ordersel']) ? $_GET['ordersel'] : 'ASC';
        $ordersel = isset($_POST['ordersel']) ? $_POST['ordersel'] : $ordersel;

        $limitsel = isset($_GET['limitsel']) ? $_GET['limitsel'] : Utility::getCookieVar('smartmedia_clip_limitsel', '15');
        $limitsel = isset($_POST['limitsel']) ? $_POST['limitsel'] : $limitsel;
        Utility::setCookieVar('smartmedia_clip_limitsel', $limitsel);

        $startsel = Request::getInt('startsel', 0, 'GET');
        $startsel = isset($_POST['startsel']) ? $_POST['startsel'] : $startsel;

        $showingtxt = '';
        $cond       = '';

        $sorttxttitle  = '';
        $sorttxtfolder = '';
        $sorttxtweight = '';
        $sorttxtclipid = '';

        $ordertxtasc  = '';
        $ordertxtdesc = '';

        switch ($sortsel) {
            case 'clips.clipid':
                $sorttxtclipid = "selected='selected'";
                break;
            case 'clips.weight':
                $sorttxtweight = "selected='selected'";
                break;
            case 'folders_text.title':
                $sorttxtfolder = "selected='selected'";
                break;
            default:
                $sorttxttitle = "selected='selected'";
                break;
        }

        switch ($ordersel) {
            case 'DESC':
                $ordertxtdesc = "selected='selected'";
                break;
            default:
                $ordertxtasc = "selected='selected'";
                break;
        }
        $limittxt5   = '';
        $limittxt10  = '';
        $limittxt15  = '';
        $limittxt20  = '';
        $limittxt25  = '';
        $limittxt30  = '';
        $limittxt35  = '';
        $limittxt40  = '';
        $limittxtall = '';
        switch ($limitsel) {
            case 'all':
                $limittxtall = "selected='selected'";
                break;
            case '5':
                $limittxt5 = "selected='selected'";
                break;
            case '10':
                $limittxt10 = "selected='selected'";
                break;
            default:
                $limittxt15 = "selected='selected'";
                break;
            case '20':
                $limittxt20 = "selected='selected'";
                break;
            case '25':
                $limittxt25 = "selected='selected'";
                break;
            case '30':
                $limittxt30 = "selected='selected'";
                break;
            case '35':
                $limittxt35 = "selected='selected'";
                break;
            case '40':
                $limittxt40 = "selected='selected'";
                break;
        }

        //smartmedia_adminMenu(3, _AM_SMARTMEDIA_CLIPS_ALL);

        //echo "<br>\n";
        $adminObject->addItemButton(_AM_SMARTMEDIA_CLIP_CREATE, 'clip.php?op=mod&folderid=' . $folderid, 'add', '');
        $adminObject->displayButton('left', '');

        // Creating the objects for clips
        $clipsFoldersObj = $smartmediaClipHandler->getClips(0, 0, 0);

        $array_keys  = array_keys($clipsFoldersObj);
        $criteria_id = new \CriteriaCompo();
        foreach ($array_keys as $key) {
            $criteria_id->add(new \Criteria($xoopsDB->prefix('smartmedia_folders_categories') . '.folderid', $key), 'or');
        }
        $criteria = new \CriteriaCompo();
        $criteria->add($criteria_id);
        Utility::collapsableBar('toptable', 'toptableicon');
        echo "<img id='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt=''></a>&nbsp;" . _AM_SMARTMEDIA_CLIPS_TITLE . '</h3>';
        echo "<div id='toptable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CLIPS_ALL_DSC . '</span>';

        //        echo "<form><div style=\"margin-top: 0px; margin-bottom: 5px;\">";
        //        echo "<input type='button' name='button' onclick=\"location='clip.php?op=mod&folderid=" . $folderid . "'\" value='" . _AM_SMARTMEDIA_CLIP_CREATE . "'>&nbsp;&nbsp;";
        //        echo "</div></form>";

        /* -- Code to show selected terms -- */
        echo "<form name='pick' id='pick' action='" . $_SERVER['SCRIPT_NAME'] . "' method='POST' style='margin: 0;'>";

        echo "
        <table width='100%' cellspacing='1' cellpadding='2' border='0' style='border-left: 1px solid silver; border-top: 1px solid silver; border-right: 1px solid silver;'>
            <tr>
                <td><span style='font-weight: bold; font-size: 12px; font-variant: small-caps;'>" . _AM_SMARTMEDIA_CLIPS . "</span></td>
                <td align='right'>
                    " . _AM_SMARTMEDIA_LANGUAGE . "
                        <select name='languagesel' onchange='submit()'>";

        $languages = \XoopsLists::getLangList();
        foreach ($languages as $language) {
            echo "<option value='" . $language . "'";
            if ($language == $languagesel) {
                echo "selected='selected'";
            }
            echo '>' . $language . '</option>';
        }
        echo "<option value='all'";
        if ('all' === $languagesel) {
            echo "selected='selected'";
        }
        echo '>' . _AM_SMARTMEDIA_ALL . '</option>';
        echo '
                    </select>
                    ' . _AM_SMARTMEDIA_SORT . "
                    <select name='sortsel' onchange='submit()'>
                        <option value='clips.clipid' $sorttxtclipid>" . _AM_SMARTMEDIA_ID . "</option>
                        <option value='clips_text.title' $sorttxttitle>" . _AM_SMARTMEDIA_CLIP_TITLE . "</option>
                        <option value='folders_text.title' $sorttxtfolder>" . _AM_SMARTMEDIA_FOLDER . "</option>
                        <option value='clips.weight' $sorttxtweight>" . _AM_SMARTMEDIA_CLIP_WEIGHT . "</option>
                    </select>
                    <select name='ordersel' onchange='submit()'>
                        <option value='ASC' $ordertxtasc>" . _AM_SMARTMEDIA_ASC . "</option>
                        <option value='DESC' $ordertxtdesc>" . _AM_SMARTMEDIA_DESC . '</option>
                    </select>
                    ' . _AM_SMARTMEDIA_DISPLAY_LIMIT . "
                    <select name='limitsel' onchange='submit()'>
                        <option value='all' $limittxtall>" . _AM_SMARTMEDIA_ALL . "</option>
                        <option value='5' $limittxt5>5</option>
                        <option value='10' $limittxt10>10</option>
                        <option value='15' $limittxt15>15</option>
                        <option value='20' $limittxt20>20</option>
                        <option value='25' $limittxt25>25</option>
                        <option value='30' $limittxt30>30</option>
                        <option value='35' $limittxt35>35</option>
                        <option value='40' $limittxt40>40</option>
                    </select>
                </td>
            </tr>
        </table>
        </form>";

        // Clips
        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<td colspan='2' width='300px' class='bg3' align='left'><b>" . _AM_SMARTMEDIA_CLIP . '</b></td>';
        echo "<td class='bg3' align='center'><b>" . _AM_SMARTMEDIA_FOLDER . '</b></td>';
        echo "<td class='bg3'width='100' align='center'><b>" . _AM_SMARTMEDIA_WEIGHT . '</b></td>';
        echo "<td width='60' class='bg3' align='center'><b>" . _AM_SMARTMEDIA_ACTION . '</b></td>';
        echo '</tr>';
        $level = 0;
        if ('all' === $limitsel) {
            $thelimit = 0;
        } else {
            $thelimit = $limitsel;
        }
        $clipsItems = &$smartmediaClipHandler->getClipsFromAdmin($startsel, $thelimit, $sortsel, $ordersel, $languagesel);
        if (count($clipsItems) > 0) {
            foreach ($clipsItems as $item) {
                //var_dump($folderObj);
                displayClipItem($item);
            }
        } else {
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SMARTMEDIA_NOCAT . '</td>';
            echo '</tr>';
        }

        echo "</table>\n";

        // Clips Navigation Bar
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        if (0 != $thelimit) {
            $pagenav = new \XoopsPageNav($smartmediaClipHandler->getClipsCountFromAdmin($languagesel), $thelimit, $startsel, 'startsel', "languagesel=$languagesel&sortsel=$sortsel&ordersel=$ordersel&limitsel=$limitsel");
            echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
        }

        echo '</div>';

        editclip(false);
        break;
}

//smartmedia_modFooter();
//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
