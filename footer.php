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

global $xoopsModule, $xoopsModuleConfig;

require_once XOOPS_ROOT_PATH . '/modules/smartmedia/include/functions.php';

$uid = $xoopsUser ? $xoopsUser->getVar('uid') : 0;

$xoopsTpl->assign('smartmedia_adminpage', "<a href='" . SMARTMEDIA_URL . "admin/index.php'>" . _MD_SMARTMEDIA_ADMIN_PAGE . '</a>');
$xoopsTpl->assign('isAdmin', $is_smartmedia_admin);
$xoopsTpl->assign('smartmedia_url', SMARTMEDIA_URL);
$xoopsTpl->assign('smartmedia_images_url', SMARTMEDIA_IMAGE_URL);

$xoopsTpl->assign('xoops_module_header', ">
<!--
function show(object)
{
    if (document.getElementById && document.getElementById(object) != null)
         node = document.getElementById(object) .style.display='block';
    else if (document.layers && document.layers[object] != null)
        document.layers[object].display = 'block';
    else if (document.all)
        document.all[object].style.display = 'block'; }

function hide(object)
{
    if (document.getElementById && document.getElementById(object) != null)
         node = document.getElementById(object) .style.display='none';
    else if (document.layers && document.layers[object] != null)
        document.layers[object].display = 'none';
    else if (document.all)
         document.all[object].style.display = 'none'; } //-->

</script>
\"");

$xoopsTpl->assign('ref_smartfactory', 'SmartMedia is developed by The SmartFactory (http://www.smartfactory.ca), a division of InBox Solutions (http://www.inboxsolutions.net)');
