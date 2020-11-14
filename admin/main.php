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
    Utility
};
use Xmf\Module\Admin;
use Xmf\Request;

require_once __DIR__ . '/admin_header.php';
$myts = \MyTextSanitizer::getInstance();

$op = Request::getString('op', '', 'GET');

// Test de la fonction getFolders

$folderHandler = Helper::getInstance()->getHandler('Folder');

/*$limit = 6;
 $start = 3;
 echo "limit : $limit -- start : $start<br><br>";
 $folders = $folderHandler->getFolders($limit, $start, '', '', 'parent.categoryid ASC, weight ASC, parent.folderid', 'ASC');
 echo "<br>";
 foreach ($folders as $foldercat) {
 foreach ($foldercat as $folder) {
 echo "folderid : " . $folder->folderid() . "<br>";
 }
 }
 exit;*/

switch ($op) {
    case 'createdir':
        $path = isset($_GET['path']) ? $_GET['path'] : false;
        if ($path) {
            if ('root' === $path) {
                $path = '';
            }
            $thePath = Utility::getUploadDir(true, $path);
            $res     = Utility::admin_mkdir($thePath);
            if ($res) {
                $source = SMARTMEDIA_ROOT_PATH . 'assets/images/blank.png';
                $dest   = $thePath . 'blank.png';
                Utility::copyr($source, $dest);
            }
            $msg = $res ? _AM_SMARTMEDIA_DIRCREATED : _AM_SMARTMEDIA_DIRNOTCREATED;
        } else {
            $msg = _AM_SMARTMEDIA_DIRNOTCREATED;
        }

        redirect_header('main.php', 2, $msg . ': ' . $thePath);
        exit();
        break;
}

function pathConfiguration()
{
    global $xoopsModule;
    // Upload and Images Folders
    Utility::collapsableBar('configtable', 'configtableicon');
    echo "<img id='configtableicon' name='configtableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' ></a>&nbsp;" . _AM_SMARTMEDIA_PATHCONFIGURATION . '</h3>';
    echo "<div id='configtable'>";
    echo '<br>';
    echo "<table width='100%' class='outer' cellspacing='1' cellpadding='3' border='0' ><tr>";
    echo "<td class='bg3'><b>" . _AM_SMARTMEDIA_PATH_ITEM . '</b></td>';
    echo "<td class='bg3'><b>" . _AM_SMARTMEDIA_PATH . '</b></td>';
    echo "<td class='bg3' align='center'><b>" . _AM_SMARTMEDIA_STATUS . '</b></td></tr>';
    echo "<tr><td class='odd'>" . _AM_SMARTMEDIA_PATH_FILES . '</td>';
    $upload_path = Utility::getUploadDir();

    echo "<td class='odd'>" . $upload_path . '</td>';
    echo "<td class='even' style='text-align: center;'>" . Utility::admin_getPathStatus('root') . '</td></tr>';

    echo "<tr><td class='odd'>" . _AM_SMARTMEDIA_PATH_IMAGES . '</td>';
    $image_path = Utility::getImageDir();
    echo "<td class='odd'>" . $image_path . '</td>';
    echo "<td class='even' style='text-align: center;'>" . Utility::admin_getPathStatus('images') . '</td></tr>';

    echo "<tr><td class='odd'>" . _AM_SMARTMEDIA_PATH_IMAGES_CATEGORY . '</td>';
    $image_path = Utility::getImageDir('category');
    echo "<td class='odd'>" . $image_path . '</td>';
    echo "<td class='even' style='text-align: center;'>" . Utility::admin_getPathStatus('images/category') . '</td></tr>';

    echo "<tr><td class='odd'>" . _AM_SMARTMEDIA_PATH_IMAGES_FOLDER . '</td>';
    $image_path = Utility::getImageDir('folder');
    echo "<td class='odd'>" . $image_path . '</td>';
    echo "<td class='even' style='text-align: center;'>" . Utility::admin_getPathStatus('images/folder') . '</td></tr>';

    echo "<tr><td class='odd'>" . _AM_SMARTMEDIA_PATH_IMAGES_CLIP . '</td>';
    $image_path = Utility::getImageDir('clip');
    echo "<td class='odd'>" . $image_path . '</td>';
    echo "<td class='even' style='text-align: center;'>" . Utility::admin_getPathStatus('images/clip') . '</td></tr>';

    echo '</table>';
    echo '<br>';

    echo '</div>';
}

require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

global $smartmediaCategoryHandler, $smartmedia_itemHandler;

xoops_cp_header();

$adminObject = Admin::getInstance();
$adminObject->displayNavigation('main.php');

$adminObject->addItemButton(_AM_SMARTMEDIA_CATEGORY_CREATE, 'category.php?op=mod', 'add', '');
$adminObject->addItemButton(_AM_SMARTMEDIA_FOLDER_CREATE, 'folder.php?op=mod', 'add', '');
$adminObject->addItemButton(_AM_SMARTMEDIA_CLIP_CREATE, 'clip.php?op=mod', 'add', '');
$adminObject->displayButton('left', '');

global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $itemid;

//smartmedia_adminMenu(0, _AM_SMARTMEDIA_INDEX);

// Total categories
$totalcategories = $smartmediaCategoryHandler->getCategoriesCount(-1);

// Total Folders
$totalfolders = $folderHandler->getFoldersCount();

// Total Clips
$totalclips = $smartmediaClipHandler->getclipsCount();

// Check Path Configuration
if ((Utility::admin_getPathStatus('root', true) < 0)
    || (Utility::admin_getPathStatus('images', true) < 0)
    || (Utility::admin_getPathStatus('images/category', true) < 0)
    || (Utility::admin_getPathStatus('images/folder', true) < 0)
    || (Utility::admin_getPathStatus('images/clip', true) < 0)) {
    pathConfiguration();
}

// -- //
Utility::collapsableBar('toptable', 'toptableicon');
echo "<img id='toptableicon' name='toptableicon' src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/assets/images/icon/close12.gif alt='' ></a>&nbsp;" . _AM_SMARTMEDIA_INVENTORY . '</h3>';
echo "<div id='toptable'>";
echo '<br>';
echo "<table width='100%' class='outer' cellspacing='1' cellpadding='3' border='0' ><tr>";
echo "<td class='head'>" . _AM_SMARTMEDIA_TOTALCAT . "</td><td align='center' class='even'>" . $totalcategories . '</td>';
echo "<td class='head'>" . _AM_SMARTMEDIA_TOTALFOLDERS . "</td><td align='center' class='even'>" . $totalfolders . '</td>';
echo "<td class='head'>" . _AM_SMARTMEDIA_TOTALCLIPS . "</td><td align='center' class='even'>" . $totalclips . '</td>';
echo '</tr></table>';
echo '<br>';

//echo "<form><div style=\"margin-bottom: 24px;\">";
//echo "<input type='button' name='button' onclick=\"location='category.php?op=mod'\" value='" . _AM_SMARTMEDIA_CATEGORY_CREATE . "'>&nbsp;&nbsp;";
//echo "<input type='button' name='button' onclick=\"location='folder.php?op=mod'\" value='" . _AM_SMARTMEDIA_FOLDER_CREATE . "'>&nbsp;&nbsp;";
//echo "<input type='button' name='button' onclick=\"location='clip.php?op=mod'\" value='" . _AM_SMARTMEDIA_CLIP_CREATE . "'>&nbsp;&nbsp;";
//echo "</div></form>";
//echo "</div>";

// Check Path Configuration
if ((Utility::admin_getPathStatus('root', true) > 0)
    && (Utility::admin_getPathStatus('images', true) > 0)
    && (Utility::admin_getPathStatus('images/category', true) > 0)
    && (Utility::admin_getPathStatus('images/folder', true) > 0)
    && (Utility::admin_getPathStatus('images/clip', true) > 0)) {
    pathConfiguration();
}

echo '</div>';

//smartmedia_modFooter();
//xoops_cp_footer();
require_once __DIR__ . '/admin_footer.php';
