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
require_once dirname(dirname(__DIR__)) . '/mainfile.php';
require_once XOOPS_ROOT_PATH . '/modules/smartmedia/include/functions.php';

$com_itemid = \Xmf\Request::getInt('com_itemid', 0, 'GET');
if ($com_itemid > 0) {
    $itemObj       = new ssItem($com_itemid);
    $com_replytext = _POSTEDBY . '&nbsp;<b>' . Utility::getLinkedUnameFromId($itemObj->uid()) . '</b>&nbsp;' . _DATE . '&nbsp;<b>' . $itemObj->dateSub() . '</b><br><br>' . $itemObj->summary();
    $bodytext      = $itemObj->body();
    if ('' != $bodytext) {
        $com_replytext .= '<br><br>' . $bodytext . '';
    }
    $com_replytitle = $itemObj->title();
    require_once XOOPS_ROOT_PATH . '/include/comment_new.php';
}
