<?php

use XoopsModules\Smartmedia;

include __DIR__ . '/../preloads/autoloader.php';

$moduleDirName = basename(dirname(__DIR__));
$moduleDirNameUpper   = strtoupper($moduleDirName); //$capsDirName


/** @var \XoopsDatabase $db */
/** @var Smartmedia\Helper $helper */
/** @var Smartmedia\Utility $utility */
$db      = \XoopsDatabaseFactory::getDatabaseConnection();
$helper  = Smartmedia\Helper::getInstance();
$utility = new Smartmedia\Utility();
//$configurator = new Smartmedia\Common\Configurator();

$helper->loadLanguage('common');

//handlers
//$categoryHandler     = new Smartmedia\CategoryHandler($db);
//$downloadHandler     = new Smartmedia\DownloadHandler($db);

if (!defined($moduleDirNameUpper . '_CONSTANTS_DEFINED')) {
    define($moduleDirNameUpper . '_DIRNAME', basename(dirname(__DIR__)));
    define($moduleDirNameUpper . '_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_PATH', XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_URL', XOOPS_URL . '/modules/' . $moduleDirName . '/');
    define($moduleDirNameUpper . '_IMAGE_URL', constant($moduleDirNameUpper . '_URL') . '/assets/images/');
    define($moduleDirNameUpper . '_IMAGE_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/assets/images');
    define($moduleDirNameUpper . '_ADMIN_URL', constant($moduleDirNameUpper . '_URL') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN_PATH', constant($moduleDirNameUpper . '_ROOT_PATH') . '/admin/');
    define($moduleDirNameUpper . '_ADMIN', constant($moduleDirNameUpper . '_URL') . '/admin/index.php');
    define($moduleDirNameUpper . '_AUTHOR_LOGOIMG', constant($moduleDirNameUpper . '_URL') . '/assets/images/logoModule.png');
    define($moduleDirNameUpper . '_UPLOAD_URL', XOOPS_UPLOAD_URL . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/' . $moduleDirName); // WITHOUT Trailing slash
    define($moduleDirNameUpper . '_CONSTANTS_DEFINED', 1);
}



//
//if (!defined('SMARTMEDIA_DIRNAME')) {
//    define('SMARTMEDIA_DIRNAME', 'smartmedia');
//}
//
//if (!defined('SMARTMEDIA_URL')) {
//    define('SMARTMEDIA_URL', XOOPS_URL . '/modules/' . SMARTMEDIA_DIRNAME . '/');
//}
//if (!defined('SMARTMEDIA_ROOT_PATH')) {
//    define('SMARTMEDIA_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/' . SMARTMEDIA_DIRNAME . '/');
//}
//
//if (!defined('SMARTMEDIA_IMAGE_URL')) {
//    define('SMARTMEDIA_IMAGE_URL', SMARTMEDIA_URL . 'images/');
//}

require_once SMARTMEDIA_ROOT_PATH . "include/functions.php";

// Creating the SmartModule object
$smartModule           = smartmedia_getModuleInfo();
$myts                  = \MyTextSanitizer::getInstance();
$smartmedia_moduleName = $myts->displayTarea($smartModule->getVar('name'));

$is_smartmedia_admin = smartmedia_userIsAdmin();

// Creating the SmartModule config Object
$smartConfig = smartmedia_getModuleConfig();

//require_once SMARTMEDIA_ROOT_PATH . "class/permission.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/category.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/category_text.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/folder.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/folder_text.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/clip.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/clip_text.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/tabs.php";
//require_once SMARTMEDIA_ROOT_PATH . "class/format.php";
//
//require_once SMARTMEDIA_ROOT_PATH . "class/keyhighlighter.class.php";

// Creating the permission handler object
$smartmediaPermissionHandler = Smartmedia\Helper::getInstance()->getHandler('Permission');

// Creating the category handler object
$smartmediaCategoryHandler = Smartmedia\Helper::getInstance()->getHandler('Category');

// Creating the category_text handler object
$smartmediaCategoryTextHandler = Smartmedia\Helper::getInstance()->getHandler('CategoryText');

// Creating the folder handler object
$smartmediaFolderHandler = Smartmedia\Helper::getInstance()->getHandler('Folder');

// Creating the doler_text handler object
$smartmediaFolderTextHandler = Smartmedia\Helper::getInstance()->getHandler('FolderText');

// Creating the clip handler object
$smartmediaClipHandler = Smartmedia\Helper::getInstance()->getHandler('Clip');

// Creating the clip_text handler object
$smartmediaClipTextHandler = Smartmedia\Helper::getInstance()->getHandler('ClipText');

// Creating the clip_text handler object
$smartmediaFormatHandler = Smartmedia\Helper::getInstance()->getHandler('Format');




$pathIcon16    = Xmf\Module\Admin::iconUrl('', 16);
$pathIcon32    = Xmf\Module\Admin::iconUrl('', 32);
//$pathModIcon16 = $helper->getModule()->getInfo('modicons16');
//$pathModIcon32 = $helper->getModule()->getInfo('modicons32');

$icons = [
    'edit'    => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _EDIT . "' align='middle'>",
    'delete'  => "<img src='" . $pathIcon16 . "/delete.png' alt='" . _DELETE . "' align='middle'>",
    'clone'   => "<img src='" . $pathIcon16 . "/editcopy.png' alt='" . _CLONE . "' align='middle'>",
    'preview' => "<img src='" . $pathIcon16 . "/view.png' alt='" . _PREVIEW . "' align='middle'>",
    'print'   => "<img src='" . $pathIcon16 . "/printer.png' alt='" . _CLONE . "' align='middle'>",
    'pdf'     => "<img src='" . $pathIcon16 . "/pdf.png' alt='" . _CLONE . "' align='middle'>",
    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt='" . _ADD . "' align='middle'>",
    '0'       => "<img src='" . $pathIcon16 . "/0.png' alt='" . 0 . "' align='middle'>",
    '1'       => "<img src='" . $pathIcon16 . "/1.png' alt='" . 1 . "' align='middle'>",
];

$debug = false;

// MyTextSanitizer object
$myts = \MyTextSanitizer::getInstance();

if (!isset($GLOBALS['xoopsTpl']) || !($GLOBALS['xoopsTpl'] instanceof \XoopsTpl)) {
    require_once $GLOBALS['xoops']->path('class/template.php');
    $GLOBALS['xoopsTpl'] = new \XoopsTpl();
}

$GLOBALS['xoopsTpl']->assign('mod_url', XOOPS_URL . '/modules/' . $moduleDirName);
// Local icons path
if (is_object($helper->getModule())) {
    $pathModIcon16 = $helper->getModule()->getInfo('modicons16');
    $pathModIcon32 = $helper->getModule()->getInfo('modicons32');

    $GLOBALS['xoopsTpl']->assign('pathModIcon16', XOOPS_URL . '/modules/' . $moduleDirName . '/' . $pathModIcon16);
    $GLOBALS['xoopsTpl']->assign('pathModIcon32', $pathModIcon32);
}
