<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @param $userid
 * @return array
 */

use XoopsModules\Smartmedia;

function smartmedia_search($queryarray, $andor, $limit, $offset, $userid)
{
    require_once __DIR__ . '/common.php';

    $ret = [];

    // Searching the categories
    $smartmediaCategoryHandler = Smartmedia\Helper::getInstance()->getHandler('Category');
    $categories_result         = $smartmediaCategoryHandler->getObjectsForSearch($queryarray, $andor, $limit, $offset, $userid);

    foreach ($categories_result as $result) {
        $item['image'] = 'images/icon/cat.gif';
        $item['link']  = 'category.php?categoryid=' . $result['id'] . '&amp;keywords=' . implode('+', $queryarray);
        $item['title'] = '' . $result['title'];
        $item['time']  = '';
        $item['uid']   = '';
        $ret[]         = $item;
        unset($item);
    }

    // Searching the folders
    $smartmediaFolderHandler = Smartmedia\Helper::getInstance()->getHandler('Folder');
    $folders_result          = $smartmediaFolderHandler->getObjectsForSearch($queryarray, $andor, $limit, $offset, $userid);

    foreach ($folders_result as $result) {
        $item['image'] = 'images/icon/folder.gif';
        $item['link']  = 'folder.php?categoryid=' . $result['categoryid'] . '&amp;folderid=' . $result['id'] . '&amp;keywords=' . implode('+', $queryarray);
        $item['title'] = '' . $result['title'];
        $item['time']  = '';
        $item['uid']   = '';
        $ret[]         = $item;
        unset($item);
    }

    // Searching the clipd
    $smartmediaClipHandler = Smartmedia\Helper::getInstance()->getHandler('Clip');
    $clips_result          = $smartmediaClipHandler->getObjectsForSearch($queryarray, $andor, $limit, $offset, $userid);

    foreach ($clips_result as $result) {
        $item['image'] = 'images/icon/clip.gif';
        $item['link']  = 'clip.php?categoryid=' . $result['categoryid'] . '&amp;folderid=' . $result['folderid'] . '&amp;clipid=' . $result['id'] . '&amp;keywords=' . implode('+', $queryarray);
        $item['title'] = '' . $result['title'];
        $item['time']  = '';
        $item['uid']   = '';
        $ret[]         = $item;
        unset($item);
    }

    return $ret;
}
