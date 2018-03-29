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

global $smartmediaFolderHandler, $smartmediaClipHandler;

$folderid = isset($_GET['folderid']) ? (int)$_GET['folderid'] : 0;

// Creating the folder object for the selected folder
$folderObj = $smartmediaFolderHandler->get($folderid);

// If the selected folder was not found, exit
if (!$folderObj) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SMARTMEDIA_FOLDER_NOT_SELECTED);
    exit();
}

$totalItem = $smartmediaFolderHandler->onlineClipsCount($folderid);

// If there is no Item under this categories or the sub-categories, exit
if (!isset($totalItem[$folderid]) || 0 == $totalItem[$folderid]) {
    redirect_header('javascript:history.go(-1)', 3, _MD_SMARTMEDIA_NO_CLIP);
    exit;
}

$GLOBALS['xoopsOption']['template_main'] = 'smartmedia_folder.tpl';

require_once XOOPS_ROOT_PATH . "/header.php";
require_once __DIR__ . '/footer.php';

// Updating folder counter
$folderObj->updateCounter();

// Retreiving the parent category name to this folder
$categoryid = isset($_GET['categoryid']) ? (int)$_GET['categoryid'] : 0;
$parentObj  = $smartmediaCategoryHandler->get($categoryid);

// Folder Smarty variabble
$xoopsTpl->assign('folder', $folderObj->toArray());

// Breadcrumb
$xoopsTpl->assign('categoryPath', $parentObj->getItemLink() . ' &gt; ' . $folderObj->title());

// At which record shall we start
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

$clipsObj = $smartmediaClipHandler->getclips($helper->getConfig('clips_per_folder'), $start, $folderid, 'weight', 'ASC', false);

$clips = [];
$i     = 1;
foreach ($clipsObj as $clipObj) {
    $clip       = $clipObj->toArray2($categoryid);
    $clip['id'] = $i;
    $clips[]    = $clip;
    ++$i;
    unset($clip);
}

$xoopsTpl->assign('clips', $clips);

$xoopsTpl->assign('module_home', smartmedia_module_home());

// The Navigation Bar
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

if (0 != $helper->getConfig('clips_per_folder')) {
    $pagenav = new \XoopsPageNav($totalItem[$folderObj->getVar('folderid')], $helper->getConfig('clips_per_folder'), $start, 'start', 'categoryid=' . $categoryid . '&folderid=' . $folderObj->getVar('folderid'));
    $xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');
    if ($helper->getConfig('clips_per_folder') >= 8) {
        $xoopsTpl->assign('navbarbottom', 1);
    }
}
// MetaTag Generator
smartmedia_createMetaTags($folderObj->title(), $parentObj->title(), $folderObj->description());

require_once XOOPS_ROOT_PATH . "/footer.php";
