<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $category
 * @param $item_id
 * @return null
 */
function smartmedia_notify_iteminfo($category, $item_id)
{
    global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;

    if (empty($xoopsModule) || 'smartmedia' !== $xoopsModule->getVar('dirname')) {
        /** @var \XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname('smartmedia');
        /** @var \XoopsConfigHandler $configHandler */
        $configHandler = xoops_getHandler('config');
        $config        = &$configHandler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module = &$xoopsModule;
        $config = &$xoopsModuleConfig;
    }

    if ('global' === $category) {
        $item['name'] = '';
        $item['url']  = '';

        return $item;
    }

    global $xoopsDB;

    if ('category' === $category) {
        // Assume we have a valid category id
        $sql          = 'SELECT name FROM ' . $xoopsDB->prefix('smartmedia_categories') . ' WHERE categoryid  = ' . $item_id;
        $result       = $xoopsDB->query($sql); // TODO: error check
        $result_array = $xoopsDB->fetchArray($result);
        $item['name'] = $result_array['name'];
        $item['url']  = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/category.php?categoryid=' . $item_id;

        return $item;
    }

    if ('item' === $category) {
        // Assume we have a valid story id
        $sql          = 'SELECT title FROM ' . $xoopsDB->prefix('smartmedia_item') . ' WHERE itemid = ' . $item_id;
        $result       = $xoopsDB->query($sql); // TODO: error check
        $result_array = $xoopsDB->fetchArray($result);
        $item['name'] = $result_array['title'];
        $item['url']  = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/item.php?itemid=' . $item_id;

        return $item;
    }

    return null;
}
