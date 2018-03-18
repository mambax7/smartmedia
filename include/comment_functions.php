<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 * @param $item_id
 * @param $total_num
 */

function smartmedia_com_update($item_id, $total_num)
{
    $db  = \XoopsDatabaseFactory::getDatabaseConnection();
    $sql = 'UPDATE ' . $db->prefix('smartmedia_items') . ' SET comments = ' . $total_num . ' WHERE itemid = ' . $item_id;
    $db->query($sql);
}

/**
 * @param $comment
 */
function smartmedia_com_approve(&$comment)
{
    // notification mail here
}
