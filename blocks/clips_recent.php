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
 * @param $options
 * @return mixed
 */

function b_smartmedia_clips_recent_show($options)
{
    // This must contain the name of the folder in which reside SmartClient
    if (!defined('SMARTMEDIA_DIRNAME')) {
        define('SMARTMEDIA_DIRNAME', 'smartmedia');
    }
    require_once XOOPS_ROOT_PATH . '/modules/' . SMARTMEDIA_DIRNAME . '/include/common.php';

    //$max_clips = $options[0];
    $title_length = $options[0];
    $max_clips    = $options[1];

    $clipsArray =& $smartmediaClipHandler->getClipsFromAdmin(0, $max_clips, 'clips.created_date', 'DESC', 'all');

    if ($clipsArray) {
        foreach ($clipsArray as $clipArray) {
            $clip             = [];
            $clip['itemlink'] = '<a href="' . SMARTMEDIA_URL . 'clip.php?categoryid=' . $clipArray['categoryid'] . '&folderid=' . $clipArray['folderid'] . '&clipid=' . $clipArray['clipid'] . '">' . $clipArray['title'] . '</a>';
            $block['clips'][] = $clip;
            unset($clip);
        }
    }

    $block['smartmedia_url'] = SMARTMEDIA_URL;

    return $block;
}

/**
 * @param $options
 * @return string
 */
function b_smartmedia_clips_recent_edit($options)
{
    $form = '<table>';
    $form .= '<tr>';
    $form .= '<td>' . _MB_SMARTMEDIA_TRUNCATE_TITLE . '</td>';
    $form .= '<td>' . "<input type='text' name='options[]' value='" . $options[0] . "' /></td>";
    $form .= '</tr>';
    $form .= '<tr>';
    $form .= '<td>' . _MB_SMARTMEDIA_MAX_CLIPS . '</td>';
    $form .= '<td>' . "<input type='text' name='options[]' value='" . $options[1] . "' /></td>";
    $form .= '</tr>';
    $form .= '</table>';

    return $form;
}
