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
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author     XOOPS Development Team
 */

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */


use XoopsModules\Smartmedia;

require_once __DIR__ . '/admin_header.php';

$op    = \Xmf\Request::getCmd('op', '');

/**
 * @param bool $showmenu
 * @param int  $id
 */
function editformat($showmenu = false, $id = 0)
{
    global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;

    $smartmediaFormatHandler = Smartmedia\Helper::getInstance()->getHandler('Format');

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
    // If there is a parameter, and the id exists, retrieve data: we're editing a format
    if (0 != $id) {
        // Creating the format object
        $formatObj = new Smartmedia\Format($id);

        if (!$formatObj) {
            redirect_header('format.php', 1, _AM_SMARTMEDIA_FORMAT_NOT_SELECTED);
            exit();
        }

        $breadcrumb_action1 = _AM_SMARTMEDIA_FORMATS;
        $breadcrumb_action2 = _AM_SMARTMEDIA_EDITING;
        $page_title         = _AM_SMARTMEDIA_FORMAT_EDITING;
        $page_info          = _AM_SMARTMEDIA_FORMAT_EDITING_INFO;
        $button_caption     = _AM_SMARTMEDIA_MODIFY;

        if ($showmenu) {
            //smartmedia_adminMenu(4, $breadcrumb_action1 . " > " . $breadcrumb_action2);
        }

        echo "<br>\n";
    } else {
        // there's no parameter, so we're adding a format
        $formatObj          = $smartmediaFormatHandler->create();
        $breadcrumb_action1 = _AM_SMARTMEDIA_FORMATS;
        $breadcrumb_action2 = _AM_SMARTMEDIA_CREATE;
        $button_caption     = _AM_SMARTMEDIA_CREATE;
        $page_title         = _AM_SMARTMEDIA_FORMAT_CREATING;
        $page_info          = _AM_SMARTMEDIA_FORMAT_CREATING_INFO;
        if ($showmenu) {
            //smartmedia_adminMenu(4, $breadcrumb_action1 . " > " . $breadcrumb_action2);
        }
    }

    smartmedia_collapsableBar('bottomtable', 'bottomtableicon');
    echo "<img id='bottomtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . $page_title . '</h3>';
    echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . $page_info . '</span>';
    echo "<div id='bottomtable'>";

    // FORMAT FORM
    $sform = new \XoopsThemeForm(_AM_SMARTMEDIA_FORMAT, 'op', xoops_getenv('PHP_SELF'));
    $sform->setExtra('enctype="multipart/form-data"');

    // FORMAT
    $format_text = new \XoopsFormText(_AM_SMARTMEDIA_FORMAT, 'format', 50, 255, $formatObj->format('e'));
    $format_text->setDescription(_AM_SMARTMEDIA_FORMAT_DSC);
    $sform->addElement($format_text, true);

    // EXT
    $ext_text = new \XoopsFormText(_AM_SMARTMEDIA_FORMAT_EXT, 'ext', 5, 10, $formatObj->ext());
    $ext_text->setDescription(_AM_SMARTMEDIA_FORMAT_EXT_DSC);
    $sform->addElement($ext_text, true);

    // TEMPLATE
    $template_text = new \XoopsFormTextArea(_AM_SMARTMEDIA_FORMAT_TEMPLATE, 'template', $formatObj->template('e'), 15, 60);
    $template_text->setDescription(_AM_SMARTMEDIA_FORMAT_TEMPLATE_DSC);
    $sform->addElement($template_text, true);

    // FORMAT ID
    $sform->addElement(new XoopsFormHidden('formatid', $formatObj->formatid()));

    $button_tray = new \XoopsFormElementTray('', '');
    $hidden      = new \XoopsFormHidden('op', 'addformat');
    $button_tray->addElement($hidden);

    $butt_create = new \XoopsFormButton('', '', $button_caption, 'submit');
    $butt_create->setExtra('onclick="this.form.elements.op.value=\'addformat\'"');
    $button_tray->addElement($butt_create);

    $butt_cancel = new \XoopsFormButton('', '', _AM_SMARTMEDIA_CANCEL, 'button');
    $butt_cancel->setExtra('onclick="location=\'format.php\'"');
    $button_tray->addElement($butt_cancel);

    $sform->addElement($button_tray);
    $sform->display();
    unset($hidden);
    echo '</div>';
}

/* -- Available operations -- */
switch ($op) {
    case 'add':

        xoops_cp_header();
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        editformat(true, 0);
        break;

    case 'mod':

        global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;
        $id = \Xmf\Request::getInt('formatid', 0, 'GET');

        xoops_cp_header();
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

        editformat(true, $id);
        break;

    case 'addformat':
        global $xoopsUser;

        $id = \Xmf\Request::getInt('formatid', 0, 'POST');

        // Creating the format object
        if (0 != $id) {
            $formatObj = new Smartmedia\Format($id);
            $action    = 'edit';
        } else {
            $formatObj = $smartmediaFormatHandler->create();
            $action    = 'new';
        }

        // Putting the values in the format object
        $formatObj->setVar('formatid', $id);
        $formatObj->setVar('format', $_POST['format']);
        $formatObj->setVar('ext', $_POST['ext']);
        $formatObj->setVar('template', $_POST['template']);

        $redirect_msgs = $formatObj->getRedirectMsg($action);

        // Storing the format
        if (!$formatObj->store()) {
            redirect_header('format.php', 3, $redirect_msgs['error'] . smartmedia_formatErrors($formatObj->getErrors()));
            exit;
        }

        redirect_header('javascript:history.go(-2)', 2, $redirect_msgs['success']);

        exit();
        break;

    case 'del':

        $id = \Xmf\Request::getInt('formatid', 0, 'POST');
        $id = \Xmf\Request::getInt('formatid', $id, 'GET');

        $formatObj = new Smartmedia\Format($id);

        $confirm = \Xmf\Request::getInt('confirm', 0, POST);
        $title   = \Xmf\Request::getString('format', '', 'POST');

        $redirect_msgs = $formatObj->getRedirectMsg('delete');

        if ($confirm) {
            if (!$smartmediaFormatHandler->delete($formatObj)) {
                redirect_header('format.php', 2, $redirect_msgs['error'] . smartmedia_formatErrors($formatObj->getErrors()));
                exit;
            }

            redirect_header('javascript:history.go(-2)', 2, $redirect_msgs['success']);
            exit();
        } else {
            // no confirm: show deletion condition
            $id = \Xmf\Request::getInt('formatid', 0, 'GET');
            xoops_cp_header();
            xoops_confirm(['op' => 'del', 'formatid' => $formatObj->formatid(), 'confirm' => 1, 'title' => $formatObj->format()], 'format.php', _AM_SMARTMEDIA_FORMAT_DELETE_CONFIRM . " <br>'" . $formatObj->format() . "' <br> <br>", _AM_SMARTMEDIA_DELETE);
            xoops_cp_footer();
        }

        exit();
        break;

    case 'default':
    default:
        xoops_cp_header();
         $totalFormats = 0;

        //smartmedia_adminMenu(4, _AM_SMARTMEDIA_FORMATS);

        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

        echo "<br>\n";

        smartmedia_collapsableBar('toptable', 'toptableicon');

        echo "<img id='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' /></a>&nbsp;" . _AM_SMARTMEDIA_FORMATS_TITLE . '</h3>';
        echo "<div id='toptable'>";
        echo '<span style="color: #567; margin: 3px 0 12px 0; font-size: small; display: block; ">' . _AM_SMARTMEDIA_FORMATS_TITLE_INFO . '</span>';

        echo '<form><div style="margin-top: 0; margin-bottom: 5px;">';
        echo "<input type='button' name='button' onclick=\"location='format.php?op=mod'\" value='" . _AM_SMARTMEDIA_FORMAT_CREATE . "'>&nbsp;&nbsp;";
        echo '</div></form>';

        // creating the format objects that are published
        $formatsObjs  = $smartmediaFormatHandler->getFormats();
        if (is_array($formatsObjs)) {
            $totalFormats = count($formatsObjs);
        }

        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'>";
        echo '<tr>';
        echo "<td class='bg3' align='left'><b>" . _AM_SMARTMEDIA_FORMAT . '</b></td>';
        echo "<td width='100px' class='bg3' align='left'><b>" . _AM_SMARTMEDIA_FORMAT_EXT . '</b></td>';
        echo "<td width='90' class='bg3' align='center'><b>" . _AM_SMARTMEDIA_ACTION . '</b></td>';
        echo '</tr>';
        if ($totalFormats > 0) {
            global $pathIcon16;
            foreach ($formatsObjs as $formatsObj) {
                $modify = "<a href='format.php?op=mod&formatid=" . $formatsObj->formatid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . _AM_SMARTMEDIA_EDIT . "' alt='" . _AM_SMARTMEDIA_EDIT . "' /></a>&nbsp;";
                $delete = "<a href='format.php?op=del&formatid=" . $formatsObj->formatid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . _AM_SMARTMEDIA_DELETE . "' alt='" . _AM_SMARTMEDIA_DELETE . "'/></a>&nbsp;";

                echo '<tr>';
                echo "<td class='even' align='left'>" . $formatsObj->format() . '</td>';
                echo "<td class='even' align='left'>" . $formatsObj->ext() . '</td>';
                echo "<td class='even' align='center'> " . $modify . $delete . '</td>';
                echo '</tr>';
            }
        } else {
            $id = 0;
            echo '<tr>';
            echo "<td class='head' align='center' colspan= '7'>" . _AM_SMARTMEDIA_FORMATS_NONE . '</td>';
            echo '</tr>';
        }
        echo "</table>\n";
        echo "<br>\n";

        echo '</div>';

        editformat();

        break;
}
//smartmedia_modFooter();
//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
