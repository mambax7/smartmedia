<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use Xmf\Request;
use XoopsModules\Smartmedia;
use XoopsModules\Smartmedia\Metagen;

require_once __DIR__ . '/header.php';

global $smartmediaCategoryHandler, $folderHandler, $smartmediaClipHandler;

$clipid     = Request::getInt('clipid', 0, 'GET');
$folderid   = Request::getInt('folderid', 0, 'GET');
$categoryid = Request::getInt('categoryid', 0, 'GET');

// Creating the clip object for the selected clip
//patche pour navpage defectueux
$clipsObj = $smartmediaClipHandler->getClips(0, 0, $folderid, 'weight', 'ASC', false);
//$clipsObj =& $smartmediaClipHandler->getclips($helper->getConfig('clips_per_folder'), $start, $folderid, 'weight', 'ASC', false);
$theClipObj = $clipsObj[$clipid];

$array_keys    = array_keys($clipsObj);
$current_clip  = array_search($clipid, $array_keys, true);
$clips_count   = count($array_keys);
$previous_clip = $current_clip - 1;
$next_clip     = $current_clip + 1;

if ($previous_clip >= 0) {
    $previous_clip_url = $clipsObj[$array_keys[$previous_clip]]->getItemUrl($categoryid);
} else {
    $previous_clip_url = '';
}

if ($next_clip < $clips_count) {
    $next_clip_url = $clipsObj[$array_keys[$next_clip]]->getItemUrl($categoryid);
} else {
    $next_clip_url = '';
}

// If the selected clip was not found, exit
if (!$theClipObj) {
    redirect_header('<script>javascript:history.go(-1)</script>', 1, _MD_SMARTMEDIA_CLIP_NOT_SELECTED);
    exit();
}

$GLOBALS['xoopsOption']['template_main'] = 'smartmedia_clip.tpl';

require_once XOOPS_ROOT_PATH . '/header.php';
require_once __DIR__ . '/footer.php';

// Updating clip counter
$theClipObj->updateCounter();

// Retreiving the parent folder object to this clip
$folderObj = $folderHandler->get($folderid);
$folderObj->setVar('categoryid', $categoryid);

// Retreiving the parent category object to this clip
$categoryObj = $smartmediaCategoryHandler->get($categoryid);

// Folder Smarty variabble
$xoopsTpl->assign('clip', $theClipObj->toArray2($categoryid));

// Breadcrumb
$xoopsTpl->assign('categoryPath', $categoryObj->getItemLink() . ' &gt; ' . $folderObj->getItemLink() . ' &gt; ' . $theClipObj->title());

// At which record shall we start
$start = Request::getInt('start', 0, 'GET');

//patche pour navpage defectueux
$clipsObj = &$smartmediaClipHandler->getClips(0, 0, $clipid, 'weight', 'ASC', false);
//$clipsObj =& $smartmediaClipHandler->getclips($helper->getConfig('clips_per_folder'), $start, $clipid, 'weight', 'ASC', false);

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

$tabsObj = new Smartmedia\Tabs($theClipObj);

// Get user's browser
require_once SMARTMEDIA_ROOT_PATH . 'include/browser_detect.php';
$browser = browser_detection('browser');

$xoopsTpl->assign('tabs', $tabsObj->getTabs($browser));
$xoopsTpl->assign('module_home', Utility::module_home());
$xoopsTpl->assign('size', 'width: 256px; height: 248px;');

$xoopsTpl->assign('previous_clip_url', $previous_clip_url);
$xoopsTpl->assign('lang_previous_clip', _MD_SMARTMEDIA_PREVIOUS_CLIP);
$xoopsTpl->assign('next_clip_url', $next_clip_url);
$xoopsTpl->assign('lang_next_clip', _MD_SMARTMEDIA_NEXT_CLIP);
$xoopsTpl->assign('lang_clip_counter', sprintf(_MD_SMARTMEDIA_CLIP_HAS_BEEN_SEEN, $theClipObj->counter()));

/*


// The Navigation Bar
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$pagenav = new \XoopsPageNav($thisclip_itemcount, $helper->getConfig('indexperpage'), $start, 'start', 'clipid=' . $clipObj->getVar('clipid'));
If ($helper->getConfig('useimagenavpage') == 1) {
$clip['navbar'] = '<div style="text-align:right;">' . $pagenav->renderImageNav() . '</div>';
} else {
$clip['navbar'] = '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';
}

$xoopsTpl->assign('clip', $clip);
*/

// MetaTag Generator
Metagen::createMetaTags($theClipObj->title('clean'), $folderObj->title('clean') . ' - ' . $categoryObj->title('clean'), $theClipObj->description());

require_once XOOPS_ROOT_PATH . '/footer.php';
