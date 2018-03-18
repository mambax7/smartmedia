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

require_once __DIR__ . '/header.php';

global $smartmediaCategoryHandler;

// At which record shall we start for the Categories
$catstart = isset($_GET['catstart']) ? (int)$_GET['catstart'] : 0;

$totalCategories = $smartmediaCategoryHandler->getCategoriesCount();

$GLOBALS['xoopsOption']['template_main'] = 'smartmedia_index.tpl';

require_once XOOPS_ROOT_PATH . "/header.php";
require_once __DIR__ . '/footer.php';

// Creating the categories objects

$categoriesObj = $smartmediaCategoryHandler->getCategories($xoopsModuleConfig['categories_on_index'], $catstart);

$categories = [];
$i          = 1;

foreach ($categoriesObj as $categoryObj) {
    $category       = $categoryObj->toArray();
    $category['id'] = $i;
    $categories[]   = $category;
    ++$i;
    unset($category);
}

$xoopsTpl->assign('categories', $categories);

$xoopsTpl->assign('module_home', smartmedia_module_home(false));
$index_msg = $myts->displayTarea($xoopsModuleConfig['index_msg'], 1);
$xoopsTpl->assign('index_msg', $index_msg);

// ITEM Navigation Bar

if ($xoopsModuleConfig['categories_on_index'] > 0) {
    $pagenav = new \XoopsPageNav($totalCategories, $xoopsModuleConfig['categories_on_index'], $catstart, 'catstart', '');
    $xoopsTpl->assign('navbar', '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>');

    if ($xoopsModuleConfig['categories_on_index'] >= 8) {
        $xoopsTpl->assign('navbarbottom', 1);
    }
}

// MetaTag Generator
smartmedia_createMetaTags($smartmedia_moduleName, '', $index_msg);

require_once XOOPS_ROOT_PATH . "/footer.php";
