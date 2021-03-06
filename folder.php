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
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @author       XOOPS Development Team
 */

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartmedia\{
    Helper,
    Utility,
    Metagen
};
use Xmf\Request;

/** @var Helper $helper */

require_once __DIR__ . '/header.php';

$helper =Helper::getInstance();

global $folderHandler, $smartmediaClipHandler;

$folderid = Request::getInt('folderid', 0, 'GET');

// Creating the folder object for the selected folder
$folderObj = $folderHandler->get($folderid);

// If the selected folder was not found, exit
if (!$folderObj) {
    redirect_header('<script>javascript:history.go(-1)</script>', 1, _MD_SMARTMEDIA_FOLDER_NOT_SELECTED);
    exit();
}

$totalItem = $folderHandler->onlineClipsCount($folderid);

// If there is no Item under this categories or the sub-categories, exit
if (!isset($totalItem[$folderid]) || 0 == $totalItem[$folderid]) {
    redirect_header('<script>javascript:history.go(-1)</script>', 3, _MD_SMARTMEDIA_NO_CLIP);
    exit;
}

$GLOBALS['xoopsOption']['template_main'] = 'smartmedia_folder.tpl';

require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/footer.php';

// Updating folder counter
$folderObj->updateCounter();

// Retreiving the parent category name to this folder
$categoryid = Request::getInt('categoryid', 0, 'GET');
$parentObj  = $smartmediaCategoryHandler->get($categoryid);

// Folder Smarty variabble
$xoopsTpl->assign('folder', $folderObj->toArray());

// Breadcrumb
$xoopsTpl->assign('categoryPath', $parentObj->getItemLink() . ' &gt; ' . $folderObj->title());

// At which record shall we start
$start = Request::getInt('start', 0, 'GET');

$clipsObj = $smartmediaClipHandler->getClips($helper->getConfig('clips_per_folder'), $start, $folderid, 'weight', 'ASC', false);

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

$xoopsTpl->assign('module_home', Utility::module_home());

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
Metagen::createMetaTags($folderObj->title(), $parentObj->title(), $folderObj->description());

require_once XOOPS_ROOT_PATH . '/footer.php';
