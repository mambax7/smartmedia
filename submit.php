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
 * @author     XOOPS Development Team
 */

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartmedia;
/** @var Smartmedia\Helper $helper */
$helper = Smartmedia\Helper::getInstance();

require_once __DIR__ . '/header.php';
require_once XOOPS_ROOT_PATH . "/header.php";

global $smartmediaCategoryHandler, $smartmedia_itemHandler, $xoopsUser, $xoopsConfig, $xoopsModule;

// Get the total number of categories
$totalCategories = count($smartmediaCategoryHandler->getCategories());

if (0 == $totalCategories) {
    redirect_header('index.php', 1, _AM_SMARTMEDIA_NOCOLEXISTS);
    exit();
}

// Find if the user is admin of the module
$isAdmin = $helper->isUserAdmin();
// If the user is not admin AND we don't allow user submission, exit
if (!($isAdmin || (null !== ($helper->getConfig('allowsubmit')) && 1 == $helper->getConfig('allowsubmit') && (is_object($xoopsUser)))
      || (null !== ($helper->getConfig('anonpost')) && 1 == $helper->getConfig('anonpost')))) {
    redirect_header('index.php', 1, _NOPERM);
    exit();
}

$op = '';

if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

switch ($op) {
    case 'preview':

        global $xoopsUser, $xoopsConfig, $xoopsModule,  $xoopsDB;


        $newItemObj = $smartmedia_itemHandler->create();

        if (!$xoopsUser) {
            if (1 == $helper->getConfig('anonpost')) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);
                exit();
            }
        } else {
            $uid = $xoopsUser->uid();
        }

        // Putting the values about the ITEM in the ITEM object
        $newItemObj->setVar('categoryid', $_POST['categoryid']);
        $newItemObj->setVar('uid', $uid);
        $newItemObj->setVar('title', $_POST['title']);
        $newItemObj->setVar('summary', $_POST['summary']);
        $newItemObj->setVar('body', $_POST['body']);
        $newItemObj->setVar('notifypub', $_POST['notifypub']);

        // Storing the item object in the database
        if (!$newItemObj->store()) {
            redirect_header('javascript:history.go(-1)', 3, _MD_SMARTMEDIA_SUBMIT_ERROR . smartmedia_formatErrors($newItemObj->getErrors()));
            exit();
        }

        // Get the cateopry object related to that item
        $categoryObj =& $newItemObj->category();

        // If autoapprove_submitted
        if (1 == $helper->getConfig('autoapprove_submitted')) {
            // We do not not subscribe user to notification on publish since we publish it right away

            // Send notifications
            $newItemObj->sendNotifications([_SMARTMEDIA_NOT_ITEM_PUBLISHED]);

            $redirect_msg = _MD_SMARTMEDIA_ITEM_RECEIVED_AND_PUBLISHED;
        } else {
            // Subscribe the user to On Published notification, if requested
            if (1 == $_POST['notifypub']) {
                require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                $notificationHandler = xoops_getHandler('notification');
                $notificationHandler->subscribe('item', $categoryObj->categoryid(), 'approve', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
            }
            // Send notifications
            $newItemObj->sendNotifications([_SMARTMEDIA_NOT_ITEM_SUBMITTED]);

            $redirect_msg = _MD_SMARTMEDIA_ITEM_RECEIVED_NEED_APPROVAL;
        }

        redirect_header('javascript:history.go(-2)', 2, $redirect_msg);

        exit();
        break;

    case 'post':

        global $xoopsUser, $xoopsConfig, $xoopsModule,  $xoopsDB;

        $newItemObj = $smartmedia_itemHandler->create();

        if (!$xoopsUser) {
            if (1 == $helper->getConfig('anonpost')) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);
                exit();
            }
        } else {
            $uid = $xoopsUser->uid();
        }

        // Putting the values about the ITEM in the ITEM object
        $newItemObj->setVar('categoryid', $_POST['categoryid']);
        $newItemObj->setVar('uid', $uid);
        $newItemObj->setVar('title', $_POST['title']);
        $newItemObj->setVar('summary', isset($_POST['summary']) ? $_POST['summary'] : '');
        $newItemObj->setVar('body', $_POST['body']);
        $notifypub = \Xmf\Request::getString('notifypub', '', 'POST');
        $newItemObj->setVar('notifypub', $notifypub);

        // Setting the status of the item
        if (1 == $helper->getConfig('autoapprove_submitted')) {
            $newItemObj->setVar('status', _SMARTMEDIA_STATUS_PUBLISHED);
        } else {
            $newItemObj->setVar('status', _SMARTMEDIA_STATUS_SUBMITTED);
        }

        // Storing the ITEM object in the database
        if (!$newItemObj->store()) {
            redirect_header('javascript:history.go(-1)', 2, _MD_SMARTMEDIA_SUBMIT_ERROR);
            exit();
        }

        // Get the cateopry object related to that item
        $categoryObj =& $newItemObj->category();

        // If autoapprove_submitted
        if (1 == $helper->getConfig('autoapprove_submitted')) {
            // We do not not subscribe user to notification on publish since we publish it right away

            // Send notifications
            $newItemObj->sendNotifications([_SMARTMEDIA_NOT_ITEM_PUBLISHED]);

            $redirect_msg = _MD_SMARTMEDIA_ITEM_RECEIVED_AND_PUBLISHED;
        } else {
            // Subscribe the user to On Published notification, if requested
            if ($notifypub) {
                require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
                $notificationHandler = xoops_getHandler('notification');
                $notificationHandler->subscribe('item', $newItemObj->itemid(), 'approved', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
            }
            // Send notifications
            $newItemObj->sendNotifications([_SMARTMEDIA_NOT_ITEM_SUBMITTED]);

            $redirect_msg = _MD_SMARTMEDIA_ITEM_RECEIVED_NEED_APPROVAL;
        }

        redirect_header('javascript:history.go(-2)', 2, $redirect_msg);

        exit();
        break;

    case 'form':
    default:

        global $xoopsUser, $myts;

        $name = $xoopsUser ? ucwords($xoopsUser->getVar("uname")) : 'Anonymous';

        $sectionname = $myts->htmlSpecialChars($xoopsModule->getVar('name'));

        echo "<table width='100%' style='padding: 0; margin: 0; border-bottom: 1px solid #2F5376;'><tr>";
        echo "<td width='50%'><span style='font-size: 10px; line-height: 18px;'><a href='" . XOOPS_URL . "'>" . _MD_SMARTMEDIA_HOME . "</a> > <a href='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/index.php'>" . $sectionname . '</a> > ' . _MD_SMARTMEDIA_SUBMIT . '</span></td>';
        echo "<td width='50%' align='right'><span style='font-size: 18px; text-align: right; font-weight: bold; color: #2F5376; letter-spacing: -1.5px; margin: 0; line-height: 18px;'>" . $sectionname . '</span></td></tr></table>';

        echo "<span style='margin-top: 8px; color: #33538e; margin-bottom: 8px; font-size: 18px; line-height: 18px; font-weight: bold; display: block;'>" . _MD_SMARTMEDIA_SUB_SNEWNAME . '</span>';
        echo "<span style='color: #456; margin-bottom: 8px; line-height: 130%; display: block;}#33538e'>" . _MD_SMARTMEDIA_GOODDAY . "<b>$name</b>, " . _MD_SMARTMEDIA_SUB_INTRO . '</span>';

        require_once __DIR__ . '/include/submit.inc.php';

        require_once XOOPS_ROOT_PATH . '/footer.php';
        break;
}
