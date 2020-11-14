<?php
// $Id: blocksadmin.inc.php,v 1.1 2005/05/13 18:22:03 malanciault Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <https://xoops.org>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, https://xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit('Access Denied');
}
require_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
require_once XOOPS_ROOT_PATH . '/modules/system/admin/blocksadmin/blocksadmin.php';

$op = 'list';
if (isset($_POST)) {
    foreach ($_POST as $k => $v) {
        ${$k} = $v;
    }
}

if (\Xmf\Request::hasVar('op', 'GET')) {
    if ('edit' === $_GET['op'] || 'delete' === $_GET['op'] || 'delete_ok' === $_GET['op'] || 'clone' === $_GET['op'] || 'previewpopup' === $_GET['op']) {
        $op  = $_GET['op'];
        $bid = \Xmf\Request::getInt('bid', 0, 'GET');
    }
}

if (isset($previewblock)) {
    xoops_cp_header();
    require_once XOOPS_ROOT_PATH . '/class/template.php';
    $xoopsTpl          = new \XoopsTpl();
    $xoopsTpl->caching = 0;
    if (isset($bid)) {
        $block['bid']        = $bid;
        $block['form_title'] = _AM_EDITBLOCK;
        $myblock             = new \XoopsBlock($bid);
        $block['name']       = $myblock->getVar('name');
    } else {
        if ('save' === $op) {
            $block['form_title'] = _AM_ADDBLOCK;
        } else {
            $block['form_title'] = _AM_CLONEBLOCK;
        }
        $myblock = new \XoopsBlock();
        $myblock->setVar('block_type', 'C');
    }
    $myts = \MyTextSanitizer::getInstance();
    $myblock->setVar('title', ($btitle));
    $myblock->setVar('content', ($bcontent));
    $dummyhtml = '<html><head><meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '"><meta http-equiv="content-language" content="' . _LANGCODE . '"><title>' . $xoopsConfig['sitename'] . '</title><link rel="stylesheet" type="text/css" media="all" href="' . getcss(
            $xoopsConfig['theme_set']
        ) . '"></head><body><table><tr><th>' . $myblock->getVar('title') . '</th></tr><tr><td>' . $myblock->getContent('S', $bctype) . '</td></tr></table></body></html>';

    $dummyfile = '_dummyfile_' . time() . '.tpl';
    $fp        = fopen(XOOPS_CACHE_PATH . '/' . $dummyfile, 'w');
    fwrite($fp, $dummyhtml);
    fclose($fp);
    $block['edit_form'] = false;
    $block['template']  = '';
    $block['op']        = $op;
    $block['side']      = $bside;
    $block['weight']    = $bweight;
    $block['visible']   = $bvisible;
    $block['title']     = $myblock->getVar('title', 'E');
    $block['content']   = $myblock->getVar('content', 'E');
    $block['modules']   = &$bmodule;
    $block['ctype']     = isset($bctype) ? $bctype : $myblock->getVar('c_type');
    $block['is_custom'] = true;
    $block['cachetime'] = (int)$bcachetime;
    echo '<a href="admin.php?fct=blocksadmin">' . _AM_BADMIN . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;' . $block['form_title'] . '<br><br>';
    require_once XOOPS_ROOT_PATH . '/modules/system/admin/blocksadmin/blockform.php';
    $form->display();
    xoops_cp_footer();
    echo '<script type="text/javascript">
    <!--//
    preview_window = openWithSelfMain("' . XOOPS_URL . '/modules/system/admin.php?fct=blocksadmin&op=previewpopup&file=' . $dummyfile . '", "popup", 250, 200);
    //-->
    </script>';
    exit();
}

if ('previewpopup' === $op) {
    $file = str_replace('..', '', XOOPS_CACHE_PATH . '/' . trim($_GET['file']));
    if (file_exists($file)) {
        require_once $file;
        @unlink($file);
    }
    exit();
}

if ('list' === $op) {
    xoops_cp_header();
    list_blocks();
    xoops_cp_footer();
    exit();
}

if ('order' === $op) {
    foreach (array_keys($bid) as $i) {
        if ($side[$i] < 0) {
            $visible[$i] = 0;
            $side[$i]    = -1;
        } else {
            $visible[$i] = 1;
        }

        $bmodule[$i] = (isset($bmodule[$i]) && is_array($bmodule[$i])) ? $bmodule[$i] : [-1];

        myblocksadmin_update_block($i, $side[$i], $weight[$i], $visible[$i], $title[$i], '', '', $bcachetime[$i], $bmodule[$i], []);
    }

    redirect_header('myblocksadmin.php', 1, _AM_DBUPDATED);
    exit();
}

if ('save' === $op) {
    save_block($bside, $bweight, $bvisible, $btitle, $bcontent, $bctype, $bmodule, $bcachetime);
    exit();
}

if ('update' === $op) {
    $bcachetime = isset($bcachetime) ? (int)$bcachetime : 0;
    $options    = isset($options) ? $options : [];
    $bcontent   = isset($bcontent) ? $bcontent : '';
    $bctype     = isset($bctype) ? $bctype : '';
    $bmodule    = (isset($bmodule) && is_array($bmodule)) ? $bmodule : [-1]; // GIJ +
    $msg        = myblocksadmin_update_block($bid, $bside, $bweight, $bvisible, $btitle, $bcontent, $bctype, $bcachetime, $bmodule, $options); // GIJ c
    redirect_header('myblocksadmin.php', 1, $msg); // GIJ +
}

if ('delete_ok' === $op) {
    delete_block_ok($bid);
    exit();
}

if ('delete' === $op) {
    xoops_cp_header();
    delete_block($bid);
    xoops_cp_footer();
    exit();
}

if ('edit' === $op) {
    xoops_cp_header();
    edit_block($bid);
    xoops_cp_footer();
    exit();
}

// import from modules/system/admin/blocksadmin/blocksadmin.php
/**
 * @param       $bid
 * @param       $bside
 * @param       $bweight
 * @param       $bvisible
 * @param       $btitle
 * @param       $bcontent
 * @param       $bctype
 * @param       $bcachetime
 * @param       $bmodule
 * @param array $options
 * @return string
 */
function myblocksadmin_update_block($bid, $bside, $bweight, $bvisible, $btitle, $bcontent, $bctype, $bcachetime, $bmodule, $options = [])
{
    global $xoopsConfig;
    if (empty($bmodule)) {
        xoops_cp_header();
        xoops_error(sprintf(_AM_NOTSELNG, _AM_VISIBLEIN));
        xoops_cp_footer();
        exit();
    }
    $myblock = new \XoopsBlock($bid);
    // $myblock->setVar('side', $bside); GIJ -
    if ($bside >= 0) {
        $myblock->setVar('side', $bside);
    } // GIJ +
    $myblock->setVar('weight', $bweight);
    $myblock->setVar('visible', $bvisible);
    $myblock->setVar('title', $btitle);
    $myblock->setVar('content', $bcontent);
    $myblock->setVar('bcachetime', $bcachetime);

    // Hack by marcan to enabled array type options
    for ($i = 0, $iMax = count($options); $i < $iMax; ++$i) {
        if (is_array($options[$i])) {
            $options[$i] = implode(',', $options[$i]);
        }
    }

    if (isset($options) && (count($options) > 0)) {
        $options = implode('|', $options);
        $myblock->setVar('options', $options);
    }
    if ('C' === $myblock->getVar('block_type')) {
        switch ($bctype) {
            case 'H':
                $name = _AM_CUSTOMHTML;
                break;
            case 'P':
                $name = _AM_CUSTOMPHP;
                break;
            case 'S':
                $name = _AM_CUSTOMSMILE;
                break;
            default:
                $name = _AM_CUSTOMNOSMILE;
                break;
        }
        $myblock->setVar('name', $name);
        $myblock->setVar('c_type', $bctype);
    } else {
        $myblock->setVar('c_type', 'H');
    }
    $msg = _AM_DBUPDATED;
    if (false !== $myblock->store()) {
        $db  = \XoopsDatabaseFactory::getDatabaseConnection();
        $sql = sprintf('DELETE FROM `%s` WHERE block_id = %u', $db->prefix('block_module_link'), $bid);
        $db->query($sql);
        foreach ($bmodule as $bmid) {
            $sql = sprintf('INSERT INTO `%s` (block_id, module_id) VALUES (%u, %d)', $db->prefix('block_module_link'), $bid, (int)$bmid);
            $db->query($sql);
        }
        require_once XOOPS_ROOT_PATH . '/class/template.php';
        $xoopsTpl          = new \XoopsTpl();
        $xoopsTpl->caching = 2;
        if ('' != $myblock->getVar('template')) {
            if ($xoopsTpl->is_cached('db:' . $myblock->getVar('template'))) {
                if (!$xoopsTpl->clear_cache('db:' . $myblock->getVar('template'))) {
                    $msg = 'Unable to clear cache for block ID' . $bid;
                }
            }
        } else {
            if ($xoopsTpl->is_cached('db:system_dummy.tpl', 'block' . $bid)) {
                if (!$xoopsTpl->clear_cache('db:system_dummy.tpl', 'block' . $bid)) {
                    $msg = 'Unable to clear cache for block ID' . $bid;
                }
            }
        }
    } else {
        $msg = 'Failed update of block. ID:' . $bid;
    }

    return $msg; // GIJ +
}
