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
 * @author       XOOPS Development Team
 */

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

$moduleDirName      = basename(__DIR__);
$moduleDirNameUpper = strtoupper($moduleDirName);

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

$modversion = [
    'version'             => 0.87,
    'module_status'       => 'Beta 1',
    'release_date'        => '2018/03/17',
    'name'                => _MI_SMARTMEDIA_NAME,
    'description'         => _MI_SMARTMEDIA_DESC,
    'release'             => '2018-03-17',
    'author'              => 'The SmartFactory, XOOPS Development Team',
    'author_mail'         => 'name@site.com',
    'author_website_url'  => 'https://xoops.org',
    'author_website_name' => 'XOOPS Project',
    'credits'             => 'XOOPS Development Team, Innu Aitun, Radio-Canada, InBox Solutions, Nelson Dumais, Christian Aubry, fx2024, RISQ, Mamba',
    //    'license' => 'GPL 2.0 or later',
    'help'                => 'page=help',
    'license'             => 'GPL 2.0 or later',
    'license_url'         => 'www.gnu.org/licenses/gpl-2.0.html',

    'release_info' => 'release_info',
    'release_file' => XOOPS_URL . "/modules/{$moduleDirName}/docs/release_info file",

    'manual'              => 'Installation.txt',
    'manual_file'         => XOOPS_URL . "/modules/{$moduleDirName}/docs/link to manual file",
    'min_php'             => '5.5',
    'min_xoops'           => '2.5.9',
    'min_admin'           => '1.2',
    'min_db'              => ['mysql' => '5.5'],
    'image'               => 'assets/images/logoModule.png',
    'dirname'             => $moduleDirName,
    'modicons16'          => 'assets/images/icons/16',
    'modicons32'          => 'assets/images/icons/32',
    //About
    'demo_site_url'       => 'https://xoops.org',
    'demo_site_name'      => 'XOOPS Demo Site',
    'support_url'         => 'https://xoops.org/modules/newbb',
    'support_name'        => 'Support Forum',
    'module_website_url'  => 'www.xoops.org',
    'module_website_name' => 'XOOPS Project',
    // Admin system menu
    'system_menu'         => 1,
    // Admin things
    'hasAdmin'            => 1,
    'adminindex'          => 'admin/index.php',
    'adminmenu'           => 'admin/menu.php',
    // Menu
    'hasMain'             => 1,
    // Scripts to run upon installation or update
    'onInstall'           => 'include/oninstall.php',
    'onUpdate'            => 'include/onupdate.php',
    'onUninstall'         => 'include/onuninstall.php',
    // ------------------- Mysql -----------------------------
    'sqlfile'             => ['mysql' => 'sql/mysql.sql'],
    // ------------------- Tables ----------------------------
    'tables'              => [
        $moduleDirName . '_' . 'categories',
        $moduleDirName . '_' . 'categories_text',
        $moduleDirName . '_' . 'clips',
        $moduleDirName . '_' . 'clips_text',
        $moduleDirName . '_' . 'folders',
        $moduleDirName . '_' . 'folders_categories',
        $moduleDirName . '_' . 'folders_text',
        $moduleDirName . '_' . 'formats',
        $moduleDirName . '_' . 'meta',
        $moduleDirName . '_' . 'status',
    ],
];

// Added by marcan for the About page in admin section
$modversion['adminMenu']              = 'smartmedia_adminMenu';
$modversion['modFooter']              = 'smartmedia_modFooter';
$modversion['developer_lead']         = 'marcan [Marc-Andre Lanciault]';
$modversion['developer_contributor']  = 'Innu Aitun, Radio-Canada, InBox Solutions, Nelson Dumais, Christian Aubry, fx2024, RISQ';
$modversion['developer_website_url']  = 'http://dev.xoops.org/modules/xfmod/project/?group_id=1219';
$modversion['developer_website_name'] = 'SmartMedia at the Developers Forge';
$modversion['developer_email']        = 'marcan@smartfactory';
$modversion['status_version']         = 'Beta 1';
$modversion['status']                 = 'Beta';
$modversion['date']                   = '2005-06-14';

// Search
$modversion['hasSearch']      = 1;
$modversion['search']['file'] = 'include/search.inc.php';
$modversion['search']['func'] = 'smartmedia_search';

//Blocks
$modversion['blocks'][] = [
    'file'        => 'clips_recent.php',
    'name'        => _MI_SMARTMEDIA_BLOCK_CLIPS_RECENT,
    'description' => _MI_SMARTMEDIA_BLOCK_CLIPS_RECENT_DSC,
    'show_func'   => 'b_smartmedia_clips_recent_show',
    'edit_func'   => 'b_smartmedia_clips_recent_edit',
    'options'     => '20|5',
    'template'    => 'smartmedia_clips_recent.tpl',
];

// Templates
$modversion['templates'] = [
    ['file' => 'smartmedia_header.tpl', 'description' => 'Module header'],
    ['file' => 'smartmedia_footer.tpl', 'description' => 'Module footer'],
    ['file' => 'smartmedia_index.tpl', 'description' => 'Display index'],
    ['file' => 'smartmedia_category.tpl', 'description' => 'Display category'],
    ['file' => 'smartmedia_folder.tpl', 'description' => 'Display folder'],
    ['file' => 'smartmedia_wmp_video.tpl', 'description' => 'Display Windows Media Player Movie'],
    ['file' => 'smartmedia_qt_video.tpl', 'description' => 'Display QuickTime Movie'],
    ['file' => 'smartmedia_flash_video.tpl', 'description' => 'Display Flash'],
    ['file' => 'smartmedia_wmp_audio.tpl', 'description' => 'Display Windows Media Player Audio'],
    ['file' => 'smartmedia_real_audio.tpl', 'description' => 'Display Real Player Audio'],
    ['file' => 'smartmedia_real_video.tpl', 'description' => 'Display Real Player Video'],
    ['file' => 'smartmedia_qt_audiohtml', 'description' => 'Display QuickTime Audio'],
    ['file' => 'smartmedia_clip.tpl', 'description' => 'Display clip'],
];

// Config Settings (only for modules that need config settings generated automatically)

$modversion['config'][] = [
    'name'        => 'index_msg',
    'title'       => '_MI_SMARTMEDIA_INDEX_MSG',
    'description' => '_MI_SMARTMEDIA_INDEX_MSGDSC',
    'formtype'    => 'textarea',
    'valuetype'   => 'text',
    'default'     => '',
];

$modversion['config'][] = [
    'name'        => 'categories_on_index',
    'title'       => '_MI_SMARTMEDIA_CAT_ON_INDEX',
    'description' => '_MI_SMARTMEDIA_CAT_ON_INDEXDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [_MI_SMARTMEDIA_ALL => 0, '4' => 4, '8' => 8, '12' => 12, '20' => 20, '30' => 30, '50' => 50],
];

$modversion['config'][] = [
    'name'        => 'folders_per_category',
    'title'       => '_MI_SMARTMEDIA_FOL_PER_CAT',
    'description' => '_MI_SMARTMEDIA_FOL_PER_CATDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [_MI_SMARTMEDIA_ALL => 0, '4' => 4, '8' => 8, '12' => 12, '20' => 20, '30' => 30, '50' => 50],
];

$modversion['config'][] = [
    'name'        => 'clips_per_folder',
    'title'       => '_MI_SMARTMEDIA_CLI_PER_FOL',
    'description' => '_MI_SMARTMEDIA_CLI_PER_FOLDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [_MI_SMARTMEDIA_ALL => 0, '4' => 4, '8' => 8, '12' => 12, '20' => 20, '30' => 30, '50' => 50],
];

$modversion['config'][] = [
    'name'        => 'cat_per_page_admin',
    'title'       => '_MI_SMARTMEDIA_CAT_ON_ADMIN',
    'description' => '_MI_SMARTMEDIA_CAT_ON_ADMINDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [_MI_SMARTMEDIA_ALL => 0, '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50],
];

$modversion['config'][] = [
    'name'        => 'folder_per_page_admin',
    'title'       => '_MI_SMARTMEDIA_FOLDER_ON_ADMIN',
    'description' => '_MI_SMARTMEDIA_FOLDER_ON_ADMINDSC',
    'formtype'    => 'select',
    'valuetype'   => 'int',
    'default'     => 0,
    'options'     => [_MI_SMARTMEDIA_ALL => 0, '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '50' => 50],
];

$modversion['config'][] = [
    'name'        => 'main_image_width',
    'title'       => '_MI_SMARTMEDIA_MAIN_IMG_WIDTH',
    'description' => '_MI_SMARTMEDIA_MAIN_IMG_WIDTHDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '300',
];

$modversion['config'][] = [
    'name'        => 'list_image_width',
    'title'       => '_MI_SMARTMEDIA_LIST_IMG_WIDTH',
    'description' => '_MI_SMARTMEDIA_LIST_IMG_WIDTHDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '150',
];

require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
$myList = new \XoopsLists();

$modversion['config'][] = [
    'name'        => 'default_language',
    'title'       => '_MI_SMARTMEDIA_DEFAULT_LANGUAGE',
    'description' => '_MI_SMARTMEDIA_DEFAULT_LANGUAGEDSC',
    'formtype'    => 'select',
    'valuetype'   => 'text',
    'default'     => 'english',
    'options'     => $myList::getLangList(),
];

$modversion['config'][] = [
    'name'        => 'highlight_color',
    'title'       => '_MI_SMARTMEDIA_HIGHLIGHT_COLOR',
    'description' => '_MI_SMARTMEDIA_HIGHLIGHT_COLORDSC',
    'formtype'    => 'textbox',
    'valuetype'   => 'text',
    'default'     => '#FFFF80',
];

/**
 * Make Sample button visible?
 */
$modversion['config'][] = [
    'name'        => 'displaySampleButton',
    'title'       => '_MI_SMARTMEDIA_SHOW_SAMPLE_BUTTON',
    'description' => '_MI_SMARTMEDIA_SHOW_SAMPLE_BUTTON_DESC',
    'formtype'    => 'yesno',
    'valuetype'   => 'int',
    'default'     => 0,
];

//$modversion['config'][] = [
//    'name'        => 'displaySampleButton',
//    'title'       => "constant('CO_' . $moduleDirNameUpper . '_SHOW_SAMPLE_BUTTON')",
//    'description' => "'CO_' . $moduleDirNameUpper . '_SHOW_SAMPLE_BUTTON_DESC'",
//    'formtype'    => 'yesno',
//    'valuetype'   => 'int',
//    'default'     => 0,
//];

/*
 $modversion['config'][1]['name'] = 'itemtype';
 $modversion['config'][1]['title'] = '_MI_SMARTMEDIA_ITEM_TYPE';
 $modversion['config'][1]['description'] = '_MI_SMARTMEDIA_ITEM_TYPEDSC';
 $modversion['config'][1]['formtype'] = 'select';
 $modversion['config'][1]['valuetype'] = 'text';
 $modversion['config'][1]['default'] = 'article';
 $modversion['config'][1]['options'] = array('Item' => 'item', 'Article' => 'article', 'Project' => 'project');

 $modversion['config'][2]['name'] = 'allowsubmit';
 $modversion['config'][2]['title'] = '_MI_SMARTMEDIA_ALLOWSUBMIT';
 $modversion['config'][2]['description'] = '_MI_SMARTMEDIA_ALLOWSUBMITDSC';
 $modversion['config'][2]['formtype'] = 'yesno';
 $modversion['config'][2]['valuetype'] = 'int';
 $modversion['config'][2]['default'] = 0;

 $modversion['config'][5]['name'] = 'dateformat';
 $modversion['config'][5]['title'] = '_MI_SMARTMEDIA_DATEFORMAT';
 $modversion['config'][5]['description'] = '_MI_SMARTMEDIA_DATEFORMATDSC';
 $modversion['config'][5]['formtype'] = 'textbox';
 $modversion['config'][5]['valuetype'] = 'text';
 $modversion['config'][5]['default'] = 'd-M-Y H:i';
 */

// Comments
$modversion['hasComments']          = 1;
$modversion['comments']['itemName'] = 'itemid';
$modversion['comments']['pageName'] = 'item.php';

// Comment callback functions
$modversion['comments']['callbackFile']        = 'include/comment_functions.php';
$modversion['comments']['callback']['approve'] = 'smartmedia_com_approve';
$modversion['comments']['callback']['update']  = 'smartmedia_com_update';

// Notification
$modversion['hasNotification'] = 0;
//$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
//$modversion['notification']['lookup_func'] = 'smartmedia_notify_iteminfo';
