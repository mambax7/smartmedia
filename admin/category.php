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

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */


use XoopsModules\Smartmedia;
/** @var Smartmedia\Helper $helper */
$helper = Smartmedia\Helper::getInstance();

require_once __DIR__ . '/admin_header.php';

global $smartmediaCategoryHandler;

$op = '';

if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

/* Possible $op :
 mod : Displaying thie form to edit or add a category
 mod_text : Displaying the form to edit a category language info
 add_category : Adding or editing a category in the db
 add_category_text : Adding or editing a category language info in the db
 del : deleting a category
 */

// At which record shall we start for the Categories
$catstart        = \Xmf\Request::getInt('catstart', 0, 'GET');
$totalCategories = $smartmediaCategoryHandler->getCategoriesCount();

// Display a single category
/**
 * @param $category_textObj
 */
function displayCategory_text($category_textObj)
{
    global $xoopsModule, $smartmediaCategoryHandler;

    $modify = "<a href='category.php?op=modtext&categoryid=" . $category_textObj->categoryid() . '&languageid=' . $category_textObj->languageid() . "'><img src='" . $pathIcon16 . '/edit.png' . "'  title='" . _AM_SMARTMEDIA_EDITCOL . "' alt='" . _AM_SMARTMEDIA_EDITCOL . "' /></a>";
    $delete = "<a href='category.php?op=deltext&categoryid=" . $category_textObj->categoryid() . '&languageid=' . $category_textObj->languageid() . "'><img src='" . $pathIcon16 . '/delete.png' . "'  title='" . _AM_SMARTMEDIA_EDITCOL . "' alt='" . _AM_SMARTMEDIA_DELETECOL . "' /></a>";
    echo '<tr>';
    echo "<td class='even' align='left'>" . $category_textObj->languageid() . '</td>';
    echo "<td class='even' align='left'> " . $category_textObj->title() . '</td>';
    echo "<td class='even' align='center'> " . $modify . $delete . ' </td>';
    echo '</tr>';
}

// Add or edit a category or a category language info in the db
/**
 * @param bool $language_text
 */
function addCategory($language_text = false)
{
    global $xoopsUser, $xoopsConfig, $xoopsModule, $myts, $smartmediaCategoryHandler;
    /** @var Smartmedia\Helper $helper */
    $helper = Smartmedia\Helper::getInstance();

    $categoryid = \Xmf\Request::getInt('categoryid', 0, 'POST');

    if (isset($_POST['languageid'])) {
        $languageid = $_POST['languageid'];
    } elseif (isset($_POST['default_languageid'])) {
        $languageid = $_POST['default_languageid'];
    } else {
        $languageid = $helper->getConfig('default_language');
    }

    if (0 != $categoryid) {
        $categoryObj = $smartmediaCategoryHandler->get($categoryid, $languageid);
    } else {
        $categoryObj = $smartmediaCategoryHandler->create();
    }

    // Uploading the image, if any
    // Retreive the filename to be uploaded
    if (!$language_text) {
        if ('' != $_FILES['image_file']['name']) {
            $filename = $_POST['xoops_upload_file'][0];
            if (!empty($filename) || '' != $filename) {
                $max_size          = 10000000;
                $max_imgwidth      = 1000;
                $max_imgheight     = 1000;
                $allowed_mimetypes = smartmedia_getAllowedMimeTypes();

                require_once XOOPS_ROOT_PATH . '/class/uploader.php';

                if ('' == $_FILES[$filename]['tmp_name'] || !is_readable($_FILES[$filename]['tmp_name'])) {
                    redirect_header('javascript:history.go(-1)', 2, _AM_SMARTMEDIA_FILEUPLOAD_ERROR);
                    exit;
                }

                $uploader = new \XoopsMediaUploader(smartmedia_getImageDir('category'), $allowed_mimetypes, $max_size, $max_imgwidth, $max_imgheight);

                if ($uploader->fetchMedia($filename) && $uploader->upload()) {
                    $categoryObj->setVar('image', $uploader->getSavedFileName());
                } else {
                    redirect_header('javascript:history.go(-1)', 2, _AM_SMARTMEDIA_FILEUPLOAD_ERROR . $uploader->getErrors());
                    exit;
                }
            }
        } else {
            $categoryObj->setVar('image', $_POST['image']);
        }

        $categoryObj->setVar('parentid', isset($_POST['parentid']) ? (int)$_POST['parentid'] : 0);
        $categoryObj->setVar('weight', isset($_POST['weight']) ? (int)$_POST['weight'] : 1);
        $categoryObj->setVar('default_languageid', isset($_POST['default_languageid']) ? $_POST['default_languageid'] : $helper->getConfig('default_language'));
        $categoryObj->setTextVar('languageid', isset($_POST['default_languageid']) ? $_POST['default_languageid'] : $helper->getConfig('default_language'));
    } else {
        $categoryObj->setTextVar('languageid', $languageid);
    }
    $categoryObj->setTextVar('title', $_POST['title']);
    $categoryObj->setTextVar('description', $_POST['description']);
    if ($categoryObj->isNew()) {
        $redirect_msg = _AM_SMARTMEDIA_CATCREATED;
        $redirect_to  = 'category.php';
    } else {
        if ($language_text) {
            $redirect_to = 'category.php?op=mod&categoryid=' . $categoryObj->categoryid();
        } else {
            $redirect_to = 'category.php';
        }
        $redirect_msg = _AM_SMARTMEDIA_COLMODIFIED;
    }

    if (!$categoryObj->store()) {
        redirect_header('javascript:history.go(-1)', 3, _AM_SMARTMEDIA_CATEGORY_SAVE_ERROR . smartmedia_formatErrors($categoryObj->getErrors()));
        exit;
    }

    redirect_header($redirect_to, 2, $redirect_msg);

    exit();
}

// Edit category information. Also used to add a category
/**
 * @param bool $showmenu
 * @param int  $categoryid
 */
function editcat($showmenu = false, $categoryid = 0)
{
    global $xoopsDB, $smartmediaCategoryHandler, $xoopsUser, $myts, $xoopsConfig,  $xoopsModule;
    /** @var Smartmedia\Helper $helper */
    $helper = Smartmedia\Helper::getInstance();
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    // if $categoryid == 0 then we are adding a new category
    $newCategory = (0 == $categoryid);

    echo '<script type="text/javascript" src="../assets/js/funcs.js"></script>';
    echo '<style>';
    echo '<!-- ';
    echo 'select { width: 130px; }';
    echo '-->';
    echo '</style>';
    $cat_sel = '';

    if (!$newCategory) {
        // We are editing a category

        // Creating the category object for the selected category
        $categoryObj = $smartmediaCategoryHandler->get($categoryid);
        $cat_sel     = '&categoryid=' . $categoryObj->categoryid();
        $categoryObj->loadLanguage($categoryObj->default_languageid());

        if ($showmenu) {
            //smartmedia_adminMenu(1, _AM_SMARTMEDIA_CATEGORIES . " > " . _AM_SMARTMEDIA_EDITING);
        }
        echo "<br>\n";
        if ($categoryObj->notLoaded()) {
            redirect_header('category.php', 1, _AM_SMARTMEDIA_NOCOLTOEDIT);
            exit();
        }
        smartmedia_collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SMARTMEDIA_EDITCOL . '</h3>';
        echo "<div id='bottomtable'>";
        echo '<span style="color: #567; margin: 3px 0 18px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CATEGORY_EDIT_INFO . '</span>';
    } else {
        // We are creating a new category

        $categoryObj = $smartmediaCategoryHandler->create();
        if ($showmenu) {
            //smartmedia_adminMenu(1, _AM_SMARTMEDIA_CATEGORIES . " > " . _AM_SMARTMEDIA_CREATINGNEW);
        }
        echo "<br>\n";
        smartmedia_collapsableBar('bottomtable', 'bottomtableicon');
        echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SMARTMEDIA_CATEGORY_CREATE . '</h3>';
        echo "<div id='bottomtable'>";
        echo '<span style="color: #567; margin: 3px 0 18px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CATEGORY_CREATE_INFO . '</span>';
    }
    if (!$newCategory) {
        /* If it's not a new category, lets display the already created category language info
         for this category, as well as a button to create a new category language info */

        if ($categoryObj->canAddLanguage()) {
            // If not all languages have been added

            echo '<form><div style="margin-bottom: 0;">';
            echo "<input type='button' name='button' onclick=\"location='category.php?op=modtext&categoryid=" . $categoryObj->categoryid() . "'\" value='" . _AM_SMARTMEDIA_CATEGORY_TEXT_CREATE . "'>&nbsp;&nbsp;";
            echo '</div></form>';
            echo '</div>';
        }

        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<td  width='20%' class='bg3' align='left'><b>" . _AM_SMARTMEDIA_LANGUAGE . '</b></td>';
        echo "<td class='bg3' align='left'><b>" . _AM_SMARTMEDIA_CATEGORY_TITLE . '</b></td>';
        echo "<td width='60' class='bg3' align='center'><b>" . _AM_SMARTMEDIA_ACTION . '</b></td>';
        echo '</tr>';

        $category_textObjs = $categoryObj->getAllLanguages(true);
        if (count($category_textObjs) > 0) {
            foreach ($category_textObjs as $key => $thiscat) {
                displayCategory_text($thiscat);
            }
        } else {
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '3'>" . _AM_SMARTMEDIA_NO_LANGUAGE_INFO . '</td>';
            echo '</tr>';
        }

        echo "</table>\n<br/>";
    }

    // Start category form

    $sform = new \XoopsThemeForm(_AM_SMARTMEDIA_CATEGORY, 'op', xoops_getenv('PHP_SELF'));
    $sform->setExtra('enctype="multipart/form-data"');
    $sform->addElement(new XoopsFormHidden('categoryid', $categoryid));

    // Language
    $languageid_select = new \XoopsFormSelectLang(_AM_SMARTMEDIA_LANGUAGE_ITEM, 'default_languageid', $categoryObj->default_languageid());
    $languageid_select->setDescription(_AM_SMARTMEDIA_LANGUAGE_ITEM_DSC);
    $languageid_select->addOptionArray(\XoopsLists::getLangList());
    if (!$newCategory) {
        $languageid_select->setExtra("style='color: grey;' disabled='disabled'");
    }
    $sform->addElement($languageid_select);

    // title
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CATEGORY_REQ, 'title', 50, 255, $categoryObj->title('e')), true);

    // Description
    $desc = new \XoopsFormTextArea(_AM_SMARTMEDIA_COLDESCRIPT, 'description', $categoryObj->description('e'), 7, 60);
    $desc->setDescription(_AM_SMARTMEDIA_COLDESCRIPTDSC);
    $sform->addElement($desc);
    $sform->addElement(new XoopsFormHidden('itemType', 'item_text'));

    // Parent Category
    /*ob_start();
     require_once(SMARTMEDIA_ROOT_PATH . "class/smarttree.php");
     $mySmartTree = new Smartmedia\Tree($xoopsDB -> prefix( "smartmedia_categories" ), "categoryid", "parentid" );

     $mySmartTree->makeMySelBox( "title", "weight", $categoryObj->parentid(), 1, 'parentid' );
     //makeMySelBox($title,$order="",$preset_id=0, $none=0, $sel_name="", $onchange="")
     $sform -> addElement( new \XoopsFormLabel( _AM_SMARTMEDIA_PARENT_CATEGORY_EXP, ob_get_contents() ) );
     ob_end_clean();
     */
    // IMAGE
    $image_array  = \XoopsLists:: getImgListAsArray(smartmedia_getImageDir('category'));
    $image_select = new \XoopsFormSelect('', 'image', $categoryObj->image());
    $image_select->addOption('-1', '---------------');
    $image_select->addOptionArray($image_array);
    $image_select->setExtra("onchange='showImgSelected(\"image3\", \"image\", \"" . 'uploads/smartmedia/images/category' . '", "", "' . XOOPS_URL . "\")'");
    $image_tray = new \XoopsFormElementTray(_AM_SMARTMEDIA_IMAGE, '&nbsp;');
    $image_tray->addElement($image_select);
    $image_tray->addElement(new XoopsFormLabel('', "<br><br><img src='" . smartmedia_getImageDir('category', false) . $categoryObj->image() . "' name='image3' id='image3' alt='' />"));
    $image_tray->setDescription(sprintf(_AM_SMARTMEDIA_IMAGE_DSC, $helper->getConfig('main_image_width')));
    $sform->addElement($image_tray);

    // IMAGE UPLOAD
    $max_size = 5000000;
    $file_box = new \XoopsFormFile(_AM_SMARTMEDIA_IMAGE_UPLOAD, 'image_file', $max_size);
    $file_box->setExtra("size ='45'");
    $file_box->setDescription(_AM_SMARTMEDIA_IMAGE_UPLOAD_DSC);
    $sform->addElement($file_box);

    // Weight
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_COLPOSIT, 'weight', 4, 4, $categoryObj->weight()));

    $sform->addElement(new XoopsFormHidden('itemType', 'item'));

    // Action buttons tray
    $button_tray = new \XoopsFormElementTray('', '');

    $hidden = new \XoopsFormHidden('op', 'addcategory');
    $button_tray->addElement($hidden);

    if ($newCategory) {
        // We are creating a new category

        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory\'"');
        $button_tray->addElement($butt_create);

        $butt_clear = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CLEAR, 'reset');
        $button_tray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    } else {

        // We are editing a category
        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory\'"');
        $button_tray->addElement($butt_create);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    }

    $sform->addElement($button_tray);
    $sform->display();
    echo '</div>';
    unset($hidden);
}

// Edit category language info. Also used to add a new category language info
/**
 * @param bool $showmenu
 * @param      $categoryid
 * @param      $languageid
 */
function editcat_text($showmenu = false, $categoryid, $languageid)
{
    global $xoopsDB, $smartmediaCategoryHandler, $xoopsUser, $myts, $xoopsConfig,  $xoopsModule;
    /** @var Smartmedia\Helper $helper */
    $helper = Smartmedia\Helper::getInstance();
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    echo '<script type="text/javascript" src="../assets/js/funcs.js"></script>';
    echo '<style>';
    echo '<!-- ';
    echo 'select { width: 130px; }';
    echo '-->';
    echo '</style>';

    $cat_sel = '';

    $categoryObj = $smartmediaCategoryHandler->get($categoryid, $languageid);

    if ('new' === $languageid) {
        $bread_lang = _AM_SMARTMEDIA_CREATE;
    } else {
        $bread_lang = ucfirst($languageid);
    }

    if ($showmenu) {
        //smartmedia_adminMenu(1, _AM_SMARTMEDIA_CATEGORIES . " > " . _AM_SMARTMEDIA_LANGUAGE_INFO . " > " . $bread_lang );
    }
    echo "<br>\n";
    smartmedia_collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SMARTMEDIA_CATEGORY_LANGUAGE_INFO_EDITING . '</h3>';
    echo "<div id='bottomtable'>";
    echo '<span style="color: #567; margin: 3px 0 18px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CATEGORY_LANGUAGE_INFO_EDITING_INFO . '</span>';

    // Start category form
    $sform = new \XoopsThemeForm(_AM_SMARTMEDIA_CATEGORY, 'op', xoops_getenv('PHP_SELF'));
    $sform->setExtra('enctype="multipart/form-data"');
    $sform->addElement(new XoopsFormHidden('categoryid', $categoryid));

    // Language
    $languageOptions  = [];
    $languageList     = \XoopsLists::getLangList();
    $createdLanguages = $categoryObj->getCreatedLanguages();
    foreach ($languageList as $language) {
        if (('new' !== $languageid) || (!in_array($language, $createdLanguages))) {
            $languageOptions[$language] = $language;
        }
    }
    if ('new' !== $languageid) {
        $language_select = new \XoopsFormSelect(_AM_SMARTMEDIA_LANGUAGE_ITEM, 'languageid', $languageid);
        $language_select->addOptionArray($languageOptions);
        $language_select->setDescription(_AM_SMARTMEDIA_LANGUAGE_ITEM_DSC);
        $language_select->setExtra(smartmedia_make_control_disabled());
        $sform->addElement(new XoopsFormHidden('languageid', $languageid));
    } else {
        $language_select = new \XoopsFormSelect(_AM_SMARTMEDIA_LANGUAGE_NEW, 'languageid', $languageid);
        $language_select->addOptionArray($languageOptions);
        $language_select->setDescription(_AM_SMARTMEDIA_LANGUAGE_NEW_DSC);
    }
    $sform->addElement($language_select, true);

    // title
    $sform->addElement(new XoopsFormText(_AM_SMARTMEDIA_CATEGORY_REQ, 'title', 50, 255, $categoryObj->title()), true);

    // Description
    $description_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_COLDESCRIPT, 'description', $categoryObj->description('e'), 7, 60);
    $description_text->setDescription(_AM_SMARTMEDIA_COLDESCRIPTDSC);
    $sform->addElement($description_text);

    $sform->addElement(new XoopsFormHidden('itemType', 'item_text'));

    // Action buttons tray
    $button_tray = new \XoopsFormElementTray('', '');

    $hidden = new \XoopsFormHidden('op', 'addcategory_text');
    $button_tray->addElement($hidden);

    if ('new' === $languageid) {
        // We are creating a new category language info

        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CREATE, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory_text\'"');
        $button_tray->addElement($butt_create);

        $butt_clear = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CLEAR, 'reset');
        $button_tray->addElement($butt_clear);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    } else {
        // We are editing a category language info

        $butt_create = new \XoopsFormButton('', '', _AM_SMARTMEDIA_MODIFY, 'submit');
        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcategory_text\'"');
        $button_tray->addElement($butt_create);

        $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
        $butt_cancel->setExtra('onclick="history.go(-1)"');
        $button_tray->addElement($butt_cancel);
    }
    $sform->addElement($button_tray);
    $sform->display();
    echo '</div>';
    unset($hidden);
}

switch ($op) {
    // Displaying the form to edit or add a category
    case 'mod':
        $categoryid = \Xmf\Request::getInt('categoryid', 0, 'GET');
        xoops_cp_header();
        editcat(true, $categoryid);
        break;

    // Displaying the form to edit a category language info
    case 'modtext':
        $categoryid = \Xmf\Request::getInt('categoryid', 0, 'GET');
        $languageid = isset($_GET['languageid']) ? $_GET['languageid'] : 'new';

        xoops_cp_header();
        editcat_text(true, $categoryid, $languageid);
        break;

    // Adding or editing a category in the db
    case 'addcategory':
        addCategory(false);
        break;

    // Adding or editing a category language info in the db
    case 'addcategory_text':
        addCategory(true);
        break;

    // deleting a category
    case 'del':
        global $smartmediaCategoryHandler, $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $_GET;

        $module_id    = $xoopsModule->getVar('mid');
        $gpermHandler = xoops_getHandler('groupperm');

        $categoryid = \Xmf\Request::getInt('categoryid', 0, 'POST');
        $categoryid = \Xmf\Request::getInt('categoryid', $categoryid, 'GET');

        $categoryObj = $smartmediaCategoryHandler->get($categoryid);

        // Check to see if this item has children
        if ($categoryObj->hasChild()) {
            redirect_header('category.php', 3, _AM_SMARTMEDIA_CATEGORY_CANNOT_DELETE_HAS_CHILD);
            exit();
        }

        $confirm = \Xmf\Request::getInt('confirm', 0, POST);
        $name    = \Xmf\Request::getString('name', '', 'POST');

        if ($confirm) {
            if (!$smartmediaCategoryHandler->delete($categoryObj)) {
                redirect_header('category.php', 1, _AM_SMARTMEDIA_DELETE_CAT_ERROR);
                exit;
            }

            redirect_header('category.php', 1, sprintf(_AM_SMARTMEDIA_COLISDELETED, $name));
            exit();
        } else {
            // no confirm: show deletion condition
            xoops_cp_header();
            xoops_confirm(['op' => 'del', 'categoryid' => $categoryObj->categoryid(), 'confirm' => 1, 'name' => $categoryObj->title()], 'category.php', _AM_SMARTMEDIA_DELETECOL . " '" . $categoryObj->title() . "'. <br> <br>" . _AM_SMARTMEDIA_DELETE_CAT_CONFIRM, _AM_SMARTMEDIA_DELETE);
            xoops_cp_footer();
        }
        exit();
        break;

    case 'deltext':
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $_GET;

        $smartsection_category_text_handler = Smartmedia\Helper::getInstance()->getHandler('CategoryText');

        $module_id = $xoopsModule->getVar('mid');

        $categoryid = \Xmf\Request::getInt('categoryid', 0, 'POST');
        $categoryid = \Xmf\Request::getInt('categoryid', $categoryid, 'GET');

        $languageid = isset($_POST['languageid']) ? $_POST['languageid'] : null;
        $languageid = isset($_GET['languageid']) ? $_GET['languageid'] : $languageid;

        $category_textObj = $smartsection_category_text_handler->get($categoryid, $languageid);

        $confirm = \Xmf\Request::getInt('confirm', 0, POST);
        $name    = \Xmf\Request::getString('name', '', 'POST');

        if ($confirm) {
            if (!$smartsection_category_text_handler->delete($category_textObj)) {
                redirect_header('category.php?op=mod&categoryid=' . $category_textObj->categoryid(), 1, _AM_SMARTMEDIA_DELETE_CAT_TEXT_ERROR);
                exit;
            }

            redirect_header('category.php?op=mod&categoryid=' . $category_textObj->categoryid(), 1, sprintf(_AM_SMARTMEDIA_DELETE_CAT_SUCCESS, $name));
            exit();
        } else {
            // no confirm: show deletion condition
            $categoryid = \Xmf\Request::getInt('categoryid', 0, 'GET');
            $languageid = isset($_GET['languageid']) ? $_GET['languageid'] : null;
            xoops_cp_header();
            xoops_confirm(
                ['op' => 'deltext', 'categoryid' => $category_textObj->categoryid(), 'languageid' => $category_textObj->languageid(), 'confirm' => 1, 'name' => $category_textObj->languageid()],
                'category.php?op=mod&categoryid=' . $category_textObj->categoryid(),
                _AM_SMARTMEDIA_DELETE_CAT_TEXT,
                          _AM_SMARTMEDIA_DELETE
            );
            xoops_cp_footer();
        }
        exit();
        break;

    case 'cancel':
        redirect_header('category.php', 1, sprintf(_AM_SMARTMEDIA_BACK2IDX, ''));
        exit();

    case 'default':
    default:

        xoops_cp_header();
        $adminObject = \Xmf\Module\Admin::getInstance();
        $adminObject->displayNavigation('category.php');

        $adminObject->addItemButton(_AM_SMARTMEDIA_CATEGORY_CREATE, 'category.php?op=mod', 'add', '');
        $adminObject->displayButton('left', '');
        //smartmedia_adminMenu(1, _AM_SMARTMEDIA_CATEGORIES);

        //echo "<br>\n";

        // Creating the objects for top categories
        $categoriesObj = $smartmediaCategoryHandler->getCategories($helper->getConfig('cat_per_page_admin'), $catstart);

        smartmedia_collapsableBar('toptable', 'toptableicon');
        echo "<img id='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SMARTMEDIA_CATEGORIES_TITLE . '</h3>';
        echo "<div id='toptable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_CATEGORIES_DSC . '</span>';

        //        echo "<form><div style=\"margin-top: 0px; margin-bottom: 5px;\">";
        //        echo "<input type='button' name='button' onclick=\"location='category.php?op=mod'\" value='" . _AM_SMARTMEDIA_CATEGORY_CREATE . "'>&nbsp;&nbsp;";
        //        echo "</div></form>";

        // Categories
        echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";
        echo '<tr>';
        echo "<td colspan='2' width='35%' class='bg3' align='left'><b>" . _AM_SMARTMEDIA_ITEMCATEGORYNAME . '</b></td>';
        echo "<td class='bg3' align='left'><b>" . _AM_SMARTMEDIA_DESCRIP . '</b></td>';
        echo "<td class='bg3' width='65' align='center'><b>" . _AM_SMARTMEDIA_WEIGHT . '</b></td>';
        echo "<td width='60' class='bg3' align='center'><b>" . _AM_SMARTMEDIA_ACTION . '</b></td>';
        echo '</tr>';
        $level           = 0;
        $totalCategories = $smartmediaCategoryHandler->getCategoriesCount(0);
        if (count($categoriesObj) > 0) {
            ++$level;
            foreach ($categoriesObj as $key => $thiscat) {
                displayCategory($thiscat);
            }
        } else {
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SMARTMEDIA_NOCAT . '</td>';
            echo '</tr>';
            $categoryid = '0';
        }
        echo "</table>\n";
        /*
         require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
         $pagenav = new \XoopsPageNav($totalCategories, $limitsel, $startcategory, 'startcategory');
         echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
         echo "</div>";
         */
        //editcat(false);
        break;
}
// Navigation Bar
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
if ($helper->getConfig('cat_per_page_admin') > 0) {
    $pagenav = new \XoopsPageNav($totalCategories, $helper->getConfig('cat_per_page_admin'), $catstart, 'catstart', '');
    echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
}

//smartmedia_modFooter();
//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
