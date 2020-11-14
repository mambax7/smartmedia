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

use XoopsModules\Smartmedia;

require_once __DIR__ . '/header.php';

global $smartmedia_itemHandler;

$itemid = \Xmf\Request::getInt('itemid', 0, 'GET');

if (0 == $itemid) {
    redirect_header('<script>javascript:history.go(-1)</script>', 1, _MD_SMARTMEDIA_NOITEMSELECTED);
    exit();
}

// Creating the ITEM object for the selected ITEM
$itemObj = new ssItem($itemid);

// If the selected ITEM was not found, exit
if ($itemObj->notLoaded()) {
    redirect_header('<script>javascript:history.go(-1)</script>', 1, _MD_SMARTMEDIA_NOITEMSELECTED);
    exit();
}

// Creating the category object that holds the selected ITEM
$categoryObj = &$itemObj->category();

// Check user permissions to access that category of the selected ITEM
if (!smartmedia_itemAccessGranted($itemObj->getVar('itemid'), $itemObj->getVar('categoryid'))) {
    redirect_header('<script>javascript:history.go(-1)</script>', 1, _NOPERM);
    exit;
}

global $xoopsConfig, $xoopsDB, $xoopsModule, $myts;

$who_where = sprintf(_MD_SMARTMEDIA_WHO_WHEN, $itemObj->posterName(), $itemObj->datesub());

echo "<!DOCTYPE HTML>\n";
echo "<html>\n<head>\n";
echo '<title>' . _MD_SMARTMEDIA_ITEMCOMEFROM . ' ' . $xoopsConfig['sitename'] . "</title>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=" . _CHARSET . "' >\n";
echo "<meta name='AUTHOR' content='" . $xoopsConfig['sitename'] . "' >\n";
echo "<meta name='COPYRIGHT' content='Copyright (c) 2001 by " . $xoopsConfig['sitename'] . "' >\n";
echo "<meta name='DESCRIPTION' content='" . $xoopsConfig['slogan'] . "' >\n";
echo "<meta name='GENERATOR' content='" . XOOPS_VERSION . "' >\n\n\n";

echo "<body bgcolor='#ffffff' text='#000000' onload='window.print()'>
     <div style='width: 650px; border: 1px solid #000; padding: 20px;'>
     <div style='text-align: center; display: block; margin: 0 0 6px 0;'><img src='" . XOOPS_URL . "/modules/smartmedia/assets/images/smartmedia_logo.gif' border='0' alt='' ><h2 style='margin: 0;'>" . $itemObj->title() . "</h2></div>
     <div align='center'>" . $who_where . "</div>
                <div style='text-align: center; display: block; padding-bottom: 12px; margin: 0 0 6px 0; border-bottom: 2px solid #ccc;'></div>
                <div></div>
                <b><p>" . $itemObj->title() . '</p></b>
                <p>' . $itemObj->body() . "</p>
                <div style='padding-top: 12px; border-top: 2px solid #ccc;'></div>
                <p>" . _MD_SMARTMEDIA_ITEMCOMEFROM . $xoopsConfig['sitename'] . ' : <a href=' . XOOPS_URL . '/modules/smartmedia/item.php?itemid=' . $itemObj->itemid() . '>' . XOOPS_URL . '/modules/smartmedia/item.php?itemid=' . $itemObj->itemid() . '</a></p>
            </div>
    <br>';

echo '<br>
          </body>
          </html>';
