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

include  dirname(__DIR__) . '/preloads/autoloader.php';

//defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

$helper = Smartmedia\Helper::getInstance();
$pathIcon32 = \Xmf\Module\Admin::menuIconPath('');
$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$adminmenu = [];

$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU0,
    'link'  => 'admin/index.php',
    'icon'  => $pathIcon32 . '/home.png',
];

$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU1,
    'link'  => 'admin/main.php',
    'icon'  => $pathIcon32 . '/manage.png',
];

// Category
$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU2,
    'link'  => 'admin/category.php',
    'icon'  => $pathIcon32 . '/category.png',
];

// Items
$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU3,
    'link'  => 'admin/folder.php',
    'icon'  => $pathIcon32 . '/view_detailed.png',
];

// Items
$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU4,
    'link'  => 'admin/clip.php',
    'icon'  => $pathIcon32 . '/marquee.png',
];

// Clip Formats
$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU5,
    'link'  => 'admin/format.php',
    'icon'  => $pathIcon32 . '/type.png',
];

// Permissions
$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU6,
    'link'  => 'admin/mygroupperm.php',
    'icon'  => $pathIcon32 . '/permissions.png',
    // Blocks and Groups
];

$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ADMENU7,
    'link'  => 'admin/myblocksadmin.php',
    'icon'  => $pathIcon32 . '/block.png',
];

//About
$adminmenu[] = [
    'title' => _MI_SMARTMEDIA_ABOUT,
    'link'  => 'admin/about.php',
    'icon'  => $pathIcon32 . '/about.png',
];
