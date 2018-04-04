<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartmedia;
/** @var Smartmedia\Helper $helper */
$helper = Smartmedia\Helper::getInstance();

require_once __DIR__ . '/header.php';

global $smartmediaCategoryHandler, $smartmediaFolderHandler;

$categoryid = \Xmf\Request::getInt('categoryid', 0, 'GET');

// Creating the category object for the selected category
$categoryObj = $smartmediaCategoryHandler->get($categoryid);

// If the selected Category was not found, exit
if (!$categoryObj) {
    redirect_header('javascript:history.go(-1)', 1, _MD_SMARTMEDIA_CATEGORY_NOT_SELECTED);
    exit();
}

$totalItem = $smartmediaCategoryHandler->onlineFoldersCount($categoryid);

// If there is no Item under this categories or the sub-categories, exit
if (!isset($totalItem[$categoryid]) || 0 == $totalItem[$categoryid]) {
    redirect_header('index.php', 3, _MD_SMARTMEDIA_NO_FOLDER);
    exit;
}

$GLOBALS['xoopsOption']['template_main'] = 'smartmedia_category.tpl';

require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/footer.php';

// Category Smarty variabble
$xoopsTpl->assign('category', $categoryObj->toArray());
$xoopsTpl->assign('categoryPath', $categoryObj->title());

// At which record shall we start
$start = \Xmf\Request::getInt('start', 0, 'GET');

$foldersObj =& $smartmediaFolderHandler->getfolders($helper->getConfig('folders_per_category'), $start, $categoryid, _SMARTMEDIA_FOLDER_STATUS_ONLINE, 'parent.categoryid ASC, weight ASC, parent.folderid', 'ASC', false);

$folders = [];
$i       = 1;

foreach ($foldersObj as $folderObj) {
    $folder       = $folderObj->toArray();
    $folder['id'] = $i;
    $folders[]    = $folder;
    ++$i;
    unset($folder);
}

$xoopsTpl->assign('folders', $folders);

$xoopsTpl->assign('module_home', smartmedia_module_home());

// The Navigation Bar
if ($helper->getConfig('folders_per_category') > 0) {
    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
    $pagenav = new \XoopsPageNav($totalItem[$categoryObj->getVar('categoryid')], $helper->getConfig('folders_per_category'), $start, 'start', 'categoryid=' . $categoryObj->getVar('categoryid'));
    $xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');

    if ($helper->getConfig('folders_per_category') >= 8) {
        $xoopsTpl->assign('navbarbottom', 1);
    }
}

// MetaTag Generator
smartmedia_createMetaTags($categoryObj->title(), '', $categoryObj->description());

require_once XOOPS_ROOT_PATH . '/footer.php';
