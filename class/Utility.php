<?php

namespace XoopsModules\Smartmedia;

/*
 Utility Class Definition

 You may not change or alter any portion of this comment or credits of
 supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit
 authors.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Module:  xSitemap
 *
 * @package      \module\xsitemap\class
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @copyright    https://xoops.org 2001-2017 &copy; XOOPS Project
 * @author       ZySpec <zyspec@yahoo.com>
 * @author       Mamba <mambax7@gmail.com>
 * @since        File available since version 1.54
 */

use XoopsModules\Smartmedia;
use XoopsModules\Smartmedia\Common;
use XoopsModules\Smartmedia\Constants;

/**
 * Class Utility
 */
class Utility extends Common\SysUtility
{
    //--------------- Custom module methods -----------------------------
    /**
     * Detemines if a table exists in the current db
     *
     * @param string $table the table name (without XOOPS prefix)
     * @return bool True if table exists, false if not
     *
     * @access public
     * @author xhelp development team
     */
    public static function TableExists($table)
    {
        $bRetVal = false;
        //Verifies that a MySQL table exists
        $xoopsDB  = \XoopsDatabaseFactory::getDatabaseConnection();
        $realname = $xoopsDB->prefix($table);
        $sql      = 'SHOW TABLES FROM ' . XOOPS_DB_NAME;
        $ret      = $xoopsDB->queryF($sql);
        while (list($m_table) = $xoopsDB->fetchRow($ret)) {
            if ($m_table == $realname) {
                $bRetVal = true;
                break;
            }
        }
        $xoopsDB->freeRecordSet($ret);

        return $bRetVal;
    }

    /**
     * Gets a value from a key in the xhelp_meta table
     *
     * @param string $key
     * @return string $value
     *
     * @access public
     * @author xhelp development team
     */
    public static function GetMeta($key)
    {
        $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
        $sql     = \sprintf('SELECT metavalue FROM `%s` WHERE metakey=%s', $xoopsDB->prefix('smartmedia_meta'), $xoopsDB->quoteString($key));
        $ret     = $xoopsDB->query($sql);
        if (!$ret) {
            $value = false;
        } else {
            [$value] = $xoopsDB->fetchRow($ret);
        }

        return $value;
    }

    /**
     * Sets a value for a key in the xhelp_meta table
     *
     * @param string $key
     * @param string $value
     * @return bool TRUE if success, FALSE if failure
     *
     * @access public
     * @author xhelp development team
     */
    public static function SetMeta($key, $value)
    {
        $xoopsDB = \XoopsDatabaseFactory::getDatabaseConnection();
        $ret     = self::GetMeta($key);
        if ($ret) {
            $sql = \sprintf('UPDATE `%s` SET metavalue = %s WHERE metakey = %s', $xoopsDB->prefix('smartmedia_meta'), $xoopsDB->quoteString($value), $xoopsDB->quoteString($key));
        } else {
            $sql = \sprintf('INSERT INTO `%s` (metakey, metavalue) VALUES (%s, %s)', $xoopsDB->prefix('smartmedia_meta'), $xoopsDB->quoteString($key), $xoopsDB->quoteString($value));
        }
        $ret = $xoopsDB->queryF($sql);
        if (!$ret) {
            return false;
        }

        return true;
    }

    /**
     * @param     $name
     * @param     $value
     * @param int $time
     */
    public static function setCookieVar($name, $value, $time = 0)
    {
        if (0 == $time) {
            $time = \time() + 3600 * 24 * 365;
        }
        setcookie($name, $value, $time, '/');
    }

    /**
     * @param        $name
     * @param string $default
     * @return string
     */
    public static function getCookieVar($name, $default = '')
    {
        if (isset($_COOKIE[$name]) && ($_COOKIE[$name] > '')) {
            return $_COOKIE[$name];
        }

        return $default;
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function getConfig($key)
    {
        $configs = self::getModuleConfig();

        return $configs[$key];
    }

    /**
     * @return string
     */
    public static function make_control_disabled()
    {
        return 'disabled="disabled" style="color: grey;"';
    }

    /**
     * @param $matches
     * @return string
     */
    public static function highlighter($matches)
    {
        //$color=getmoduleoption('highlightcolor');
        $smartConfig = self::getModuleConfig();
        $color       = $smartConfig['highlight_color'];
        if (0 !== mb_strpos($color, '#')) {
            $color = '#' . $color;
        }

        return '<span style="font-weight: bolder; background-color: ' . $color . ';">' . $matches[0] . '</span>';
    }

    /**
     * @param $document
     * @return mixed
     */
    public static function metagen_html2text($document)
    {
        // PHP Manual:: function preg_replace
        // $document should contain an HTML document.
        // This will remove HTML tags, javascript sections
        // and white space. It will also convert some
        // common HTML entities to their text equivalent.
        // Credits : newbb2

        $search = [
            "'<script[^>]*?>.*?</script>'si", // Strip out javascript
            "'<img.*?>'si", // Strip out img tags
            "'<[\/\!]*?[^<>]*?>'si", // Strip out HTML tags
            "'([\r\n])[\s]+'", // Strip out white space
            "'&(quot|#34);'i", // Replace HTML entities
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
        ]; // evaluate as php

        $replace = [
            '',
            '',
            '',
            '\\1',
            '"',
            '&',
            '<',
            '>',
            ' ',
            \chr(161),
            \chr(162),
            \chr(163),
            \chr(169),
        ];

        $text = \preg_replace($search, $replace, $document);

        \preg_replace_callback(
            '/&#(\d+);/',
            static function ($matches) {
                return \chr($matches[1]);
            },
            $document
        );

        return $text;
    }

    /**
     * @return string
     */
    public static function getAllowedMimeTypes()
    {
        return '';
        //return array('jpg/jpeg', 'image/bmp', 'image/gif', 'image/jpeg', 'image/jpg', 'image/x-png', 'image/png');
    }

    /**
     * @param bool $withLink
     * @return string
     */
    public static function module_home($withLink = true)
    {
        global $smartmedia_moduleName;

        if (!$withLink) {
            return $smartmedia_moduleName;
        }

        return '<a href="' . SMARTMEDIA_URL . '">' . $smartmedia_moduleName . '</a>';
    }

    /**
     * Copy a file, or a folder and its contents
     *
     * @param string $source The source
     * @param string $dest   The destination
     * @return      bool     Returns true on success, false on failure
     * @version     1.0.0
     * @author      Aidan Lister <aidan@php.net>
     */
    public static function copyr($source, $dest)
    {
        // Simple copy for a file
        if (\is_file($source)) {
            return \copy($source, $dest);
        }

        // Make destination directory
        if (!\is_dir($dest) && !\mkdir($dest) && !\is_dir($dest)) {
            throw new \RuntimeException(\sprintf('Directory "%s" was not created', $dest));
        }

        // Loop through the folder
        $dir = \dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ('.' === $entry || '..' === $entry) {
                continue;
            }

            // Deep copy directories
            if (\is_dir("$source/$entry") && ("$source/$entry" !== $dest)) {
                copyr("$source/$entry", "$dest/$entry");
            } else {
                \copy("$source/$entry", "$dest/$entry");
            }
        }

        // Clean up
        $dir->close();

        return true;
    }

    /**
     * @return array
     */
    public static function getStatusArray()
    {
        global $xoopsDB;
        $ret    = [];
        $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smartmedia_status') . ' ORDER BY status ASC';
        $result = $xoopsDB->query($sql);
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $ret[$myrow['statusid']] = $myrow['status'];
        }

        return $ret;
    }

    /**
     * @param bool $forSelectBox
     * @return array
     */
    public static function getFormatArray($forSelectBox = false)
    {
        static $smartmedia_formatArray;
        if (!isset($smartmedia_formatArray)) {
            global $xoopsDB;
            $ret    = [];
            $sql    = 'SELECT * FROM ' . $xoopsDB->prefix('smartmedia_formats') . ' ORDER BY format ASC';
            $result = $xoopsDB->query($sql);
            while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
                if ($forSelectBox) {
                    $ret[$myrow['formatid']] = $myrow['format'];
                } else {
                    $ret[$myrow['formatid']] = $myrow;
                }
            }

            return $ret;
        }

        return $smartmedia_formatArray;
    }

    /**
     * @param      $clipObj
     * @param      $folderid
     * @param      $categoryid
     * @param bool $from_within
     */
    public static function displayClip($clipObj, $folderid, $categoryid, $from_within = false)
    {
        global $xoopsModule, $smartmediaClipHandler, $pathIcon16;

        if ($from_within) {
            $extra = '&from_within=1';
        }

        $modify = "<a href='clip.php?op=mod&clipid=" . $clipObj->clipid() . "&folderid=$folderid" . $extra . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . \_AM_SMARTMEDIA_CLIP_EDIT . "' alt='" . \_AM_SMARTMEDIA_CLIP_EDIT . "' ></a>";
        $delete = "<a href='clip.php?op=del&clipid=" . $clipObj->clipid() . "&folderid=$folderid" . $extra . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . \_AM_SMARTMEDIA_CLIP_DELETE . "' alt='" . \_AM_SMARTMEDIA_CLIP_DELETE . "' ></a>";

        $description = $clipObj->description();
        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($description) >= 100) {
                $description = mb_substr($description, 0, 100 - 1) . '...';
            }
        }

        echo '<tr>';
        echo "<td class='even' align='left'>&nbsp;&nbsp;</td>";
        echo "<td class='even' align='left'>"
             . "<a href='"
             . XOOPS_URL
             . '/modules/'
             . $xoopsModule->dirname()
             . '/clip.php?categoryid='
             . $categoryid
             . '&folderid='
             . $folderid
             . '&clipid='
             . $clipObj->clipid()
             . "'><img src='"
             . XOOPS_URL
             . "/modules/smartmedia/assets/images/icon/clip.gif' alt='' >&nbsp;"
             . $clipObj->title()
             . '</a></td>';
        echo "<td class='even' align='left'>" . $description . '</td>';
        echo "<td class='even' align='center'>" . $clipObj->weight() . '</td>';
        echo "<td class='even' align='right'> $modify $delete </td>";
        echo '</tr>';
    }

    /**
     * @param $folderObj
     * @param $categoryid
     */
    public static function displayFolder($folderObj, $categoryid)
    {
        global $xoopsModule, $folderHandler, $pathIcon16;

        //var_dump($folderObj);
        $show_clips = "<a href='clip.php?op=show_within&folderid=" . $folderObj->folderid() . "&categoryid=$categoryid'><img src='" . $pathIcon16 . '/film.png' . "' title='" . \_AM_SMARTMEDIA_FOLDER_SHOW_CLIP . "' alt='" . \_AM_SMARTMEDIA_FOLDER_SHOW_CLIP . "' ></a>";
        $modify     = "<a href='folder.php?op=mod&folderid=" . $folderObj->folderid() . "&categoryid=$categoryid'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . \_AM_SMARTMEDIA_FOLDER_EDIT . "' alt='" . \_AM_SMARTMEDIA_FOLDER_EDIT . "' ></a>";
        $delete     = "<a href='folder.php?op=del&folderid=" . $folderObj->folderid() . "&categoryid=$categoryid'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . \_AM_SMARTMEDIA_FOLDER_DELETE . "' alt='" . \_AM_SMARTMEDIA_FOLDER_DELETE . "' ></a>";

        $description = $folderObj->description();
        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($description) >= 100) {
                $description = mb_substr($description, 0, 100 - 1) . '...';
            }
        }

        echo '<tr>';
        echo "<td class='even' align='left'>&nbsp;&nbsp;</td>";
        echo "<td class='even' align='left'>"
             . "<a href='"
             . XOOPS_URL
             . '/modules/'
             . $xoopsModule->dirname()
             . "/folder.php?categoryid=$categoryid&folderid="
             . $folderObj->folderid()
             . "'><img src='"
             . XOOPS_URL
             . "/modules/smartmedia/assets/images/icon/folder.gif' alt='' >&nbsp;"
             . $folderObj->title()
             . '</a></td>';
        echo "<td class='even' align='left'>" . $description . '</td>';
        echo "<td class='even' align='center'>" . $folderObj->weight() . '</td>';
        echo "<td class='even' align='right'> $show_clips $modify $delete </td>";
        echo '</tr>';
    }

    /**
     * @param      $categoryObj
     * @param int  $level
     * @param bool $showFolders
     * @param null $foldersCategoriesObj
     */
    public static function displayCategory($categoryObj, $level = 0, $showFolders = false, $foldersCategoriesObj = null)
    {
        global $xoopsModule, $smartmediaCategoryHandler, $pathIcon16;

        $modify = "<a href='category.php?op=mod&categoryid=" . $categoryObj->categoryid() . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . \_AM_SMARTMEDIA_EDITCOL . "' alt='" . \_AM_SMARTMEDIA_EDITCOL . "' ></a>";
        $delete = "<a href='category.php?op=del&categoryid=" . $categoryObj->categoryid() . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . \_AM_SMARTMEDIA_DELETECOL . "' alt='" . \_AM_SMARTMEDIA_DELETECOL . "' ></a>";

        $description = $categoryObj->description();
        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($description) >= 100) {
                $description = mb_substr($description, 0, 100 - 1) . '...';
            }
        }

        $spaces = '';
        for ($j = 0; $j < $level; ++$j) {
            $spaces .= '&nbsp;&nbsp;&nbsp;';
        }

        if (!$showFolders) {
            $col_span = " colspan='2' ";
        } else {
            $col_span = '';
        }

        echo '<tr>';
        echo "<td colspan='2' class='even' align='lefet'>"
             . $spaces
             . "<a href='"
             . XOOPS_URL
             . '/modules/'
             . $xoopsModule->dirname()
             . '/category.php?categoryid='
             . $categoryObj->categoryid()
             . "'><img src='"
             . XOOPS_URL
             . "/modules/smartmedia/assets/images/icon/subcat.gif' alt='' >&nbsp;"
             . $categoryObj->title()
             . '</a></td>';
        echo "<td class='even' align='left'>" . $description . '</td>';
        echo "<td class='even' align='center'>" . $categoryObj->weight() . '</td>';
        echo "<td class='even' align='right'> $modify $delete </td>";
        echo '</tr>';

        if ($foldersCategoriesObj) {
            foreach ($foldersCategoriesObj[$categoryObj->categoryid()] as $folderObj) {
                displayFolder($folderObj, $categoryObj->categoryid());
            }
        }

        $subCategoriesObj = $smartmediaCategoryHandler->getCategories(0, 0, $categoryObj->categoryid());
        if (\count($subCategoriesObj) > 0) {
            ++$level;
            foreach ($subCategoriesObj as $key => $thiscat) {
                displayCategory($thiscat, $level);
            }
        }
        unset($categoryObj);
    }

    /**
     * @param      $folderObj
     * @param int  $level
     * @param bool $showClips
     * @param null $clipsFoldersObj
     * @param      $categoryid
     * @param bool $from_within
     */
    public static function displayFolderForClip($folderObj, $level, $showClips, $clipsFoldersObj, $categoryid, $from_within = false)
    {
        global $xoopsModule, $folderHandler, $pathIcon16;

        $modify = "<a href='folder.php?op=mod&folderid=" . $folderObj->folderid() . '&categoryid=' . $categoryid . "'><img src='" . $pathIcon16 . '/edit.png' . "' title='" . \_AM_SMARTMEDIA_FOLDER_EDIT . "' alt='" . \_AM_SMARTMEDIA_FOLDER_EDIT . "' ></a>";
        $delete = "<a href='folder.php?op=del&folderid=" . $folderObj->folderid() . '&categoryid=' . $categoryid . "'><img src='" . $pathIcon16 . '/delete.png' . "' title='" . \_AM_SMARTMEDIA_FOLDER_DELETE . "' alt='" . \_AM_SMARTMEDIA_FOLDER_DELETE . "' ></a>";

        $description = $folderObj->description();
        if (!XOOPS_USE_MULTIBYTES) {
            if (mb_strlen($description) >= 100) {
                $description = mb_substr($description, 0, 100 - 1) . '...';
            }
        }

        $spaces = '';
        for ($j = 0; $j < $level; ++$j) {
            $spaces .= '&nbsp;&nbsp;&nbsp;';
        }

        if (!$showClips) {
            $col_span = " colspan='2' ";
        } else {
            $col_span = '';
        }

        echo '<tr>';
        echo "<td colspan='2' class='even' align='lefet'>"
             . $spaces
             . "<a href='"
             . XOOPS_URL
             . '/modules/'
             . $xoopsModule->dirname()
             . '/folder.php?folderid='
             . $folderObj->folderid()
             . '&categoryid='
             . $categoryid
             . "'><img src='"
             . XOOPS_URL
             . "/modules/smartmedia/assets/images/icon/folder.gif' alt='' >&nbsp;"
             . $folderObj->title()
             . '</a></td>';
        echo "<td class='even' align='left'>" . $description . '</td>';
        echo "<td class='even' align='center'>" . $folderObj->weight() . '</td>';
        echo "<td class='even' align='right'> $modify $delete </td>";
        echo '</tr>';

        if ($clipsFoldersObj) {
            foreach ($clipsFoldersObj[$folderObj->folderid()] as $clipObj) {
                displayClip($clipObj, $folderObj->folderid(), $categoryid, $from_within);
            }
        }

        unset($folderObj);
    }

    /**
     * Thanks to the NewBB2 Development Team
     * @param      $item
     * @param bool $getStatus
     * @return bool|int|string
     */
    public static function admin_getPathStatus($item, $getStatus = false)
    {
        if ('root' === $item) {
            $path = '';
        } else {
            $path = $item;
        }

        $thePath = self::getUploadDir(true, $path);

        if (empty($thePath)) {
            return false;
        }
        if (@\is_writable($thePath)) {
            $pathCheckResult = 1;
            $path_status     = \_AM_SMARTMEDIA_AVAILABLE;
        } elseif (!@\is_dir($thePath)) {
            $pathCheckResult = -1;
            $path_status     = \_AM_SMARTMEDIA_NOTAVAILABLE . " <a href=main.php?op=createdir&amp;path=$item>" . \_AM_SMARTMEDIA_CREATETHEDIR . '</a>';
        } else {
            $pathCheckResult = -2;
            $path_status     = _AM_SMARTMEDIA_NOTWRITABLE . " <a href=main.php?op=setperm&amp;path=$item>" . _AM_SCS_SETMPERM . '</a>';
        }
        if (!$getStatus) {
            return $path_status;
        }

        return $pathCheckResult;
    }

    /**
     * Thanks to the NewBB2 Development Team
     * @param $target
     * @return bool
     */
    public static function admin_mkdir($target)
    {
        // http://www.php.net/manual/en/function.mkdir.php
        // saint at corenova.com
        // bart at cdasites dot com
        if (\is_dir($target) || empty($target)) {
            return true;
        } // best case check first
        if (\is_dir($target) && !\is_dir($target)) {
            return false;
        }
        if (self::admin_mkdir(mb_substr($target, 0, mb_strrpos($target, '/')))) {
            if (!\is_dir($target)) {
                return \mkdir($target);
            }
        } // crawl back up & create dir tree
        return true;
    }

    /**
     * Thanks to the NewBB2 Development Team
     * @param     $target
     * @param int $mode
     * @return bool
     */
    public static function admin_chmod($target, $mode = 0777)
    {
        return @\chmod($target, $mode);
    }

    /**
     * @param bool $local
     * @param bool $item
     * @return string
     */
    public static function getUploadDir($local = true, $item = false)
    {
        if ($item) {
            if ('root' === $item) {
                $item = '';
            } else {
                $item .= '/';
            }
        } else {
            $item = '';
        }

        if ($local) {
            return XOOPS_ROOT_PATH . "/uploads/smartmedia/$item";
        }

        return XOOPS_URL . "/uploads/smartmedia/$item";
    }

    /**
     * @param string $item
     * @param bool   $local
     * @return string
     */
    public static function getImageDir($item = '', $local = true)
    {
        if ($item) {
            $item = "images/$item";
        } else {
            $item = 'images';
        }

        return self::getUploadDir($local, $item);
    }

    /**
     * @param string $item
     * @param bool   $local
     * @return string
     */
    public static function getModuleImageDir($item = '', $local = true)
    {
        if ($item) {
            $imagedir = 'images/' . $item . '/';
        } else {
            $imagedir = 'images/';
        }

        if ($local) {
            return SMARTMEDIA_ROOT_PATH . $imagedir;
        }

        return SMARTMEDIA_URL . $imagedir;
    }

    /**
     * @param $src
     * @param $maxWidth
     * @param $maxHeight
     * @return array
     */
    public static function imageResize($src, $maxWidth, $maxHeight)
    {
        $width  = '';
        $height = '';
        $type   = '';
        $attr   = '';

        if (\file_exists($src)) {
            [$width, $height, $type, $attr] = \getimagesize($src);
            if ($width > $maxWidth) {
                $originalWidth = $width;
                $width         = $maxWidth;
                $height        = $width * $height / $originalWidth;
            }

            if ($height > $maxHeight) {
                $originalHeight = $height;
                $height         = $maxHeight;
                $width          = $height * $width / $originalHeight;
            }

            $attr = " width='$width' height='$height'";
        }

        return [$width, $height, $type, $attr];
    }

    /**
     * @return null|string
     */
    public static function getHelpPath()
    {
        $smartConfig = self::getModuleConfig();
        switch ($smartConfig['helppath_select']) {
            case 'docs.xoops.org':
                return 'http://docs.xoops.org/help/ssectionh/index.htm';
                break;
            case 'inside':
                return XOOPS_URL . '/modules/smartmedia/doc/';
                break;
            case 'custom':
                return $smartConfig['helppath_custom'];
                break;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public static function getModuleInfo()
    {
        static $smartModule;
        if (!isset($smartModule)) {
            global $xoopsModule;
            if (isset($xoopsModule) && \is_object($xoopsModule) && 'smartmedia' === $xoopsModule->getVar('dirname')) {
                $smartModule = &$xoopsModule;
            } else {
                $moduleHandler = \xoops_getHandler('module');
                $smartModule   = $moduleHandler->getByDirname('smartmedia');
            }
        }

        return $smartModule;
    }

    /**
     * @return mixed
     */
    public static function getModuleConfig()
    {
        static $smartConfig;
        $helper = Helper::getInstance();
        if (!$smartConfig) {
            global $xoopsModule;
            if (isset($xoopsModule) && \is_object($xoopsModule) && 'smartmedia' === $xoopsModule->getVar('dirname')) {
                global $xoopsModuleConfig;
                $smartConfig = $xoopsModuleConfig;
            } else {
                $smartModule = $helper->getModule(); //smartmedia_getModuleInfo();
                $hModConfig  = \xoops_getHandler('config');
                $smartConfig = $hModConfig->getConfigsByCat(0, $xoopsModule->getVar('mid'));
            }
        }

        return $smartConfig;
    }

    /**
     * @param $dirname
     * @return bool|null
     */
    public static function deleteFile($dirname)
    {
        // Simple delete for a file
        if (\is_file($dirname)) {
            return \unlink($dirname);
        }

        return null;
    }

    /**
     * @param array $errors
     * @return string
     */
    public static function formatErrors($errors = [])
    {
        $ret = '';
        foreach ($errors as $key => $value) {
            $ret .= '<br> - ' . $value;
        }

        return $ret;
    }

    /**
     * @param        $categoryObj
     * @param int    $selectedid
     * @param int    $level
     * @param string $ret
     * @return string
     */
    public static function addCategoryOption($categoryObj, $selectedid = 0, $level = 0, $ret = '')
    {
        // Creating the category handler object
        $categoryHandler = Helper::getInstance()->getHandler('category');
        $spaces          = '';
        for ($j = 0; $j < $level; ++$j) {
            $spaces .= '--';
        }

        $ret .= "<option value='" . $categoryObj->categoryid() . "'";
        if ($selectedid == $categoryObj->categoryid()) {
            $ret .= " selected='selected'";
        }
        $ret .= '>' . $spaces . $categoryObj->name() . "</option>\n";

        $subCategoriesObj = $categoryHandler->getCategories(0, 0, $categoryObj->categoryid());
        if (\count($subCategoriesObj) > 0) {
            ++$level;
            foreach ($subCategoriesObj as $catID => $subCategoryObj) {
                $ret .= self::addCategoryOption($subCategoryObj, $selectedid, $level);
            }
        }

        return $ret;
    }

    /**
     * @param int  $selectedid
     * @param int  $parentcategory
     * @param bool $allCatOption
     * @return string
     */
    public static function createCategoryOptions($selectedid = 0, $parentcategory = 0, $allCatOption = true)
    {
        $ret = '';
        if ($allCatOption) {
            $ret .= "<option value='0'";
            $ret .= '>' . _MB_SMARTMEDIA_ALLCAT . "</option>\n";
        }

        // Creating the category handler object
        $categoryHandler = Helper::getInstance()->getHandler('Category');

        // Creating category objects
        $categoriesObj = $categoryHandler->getCategories(0, 0, $parentcategory);
        if (\count($categoriesObj) > 0) {
            foreach ($categoriesObj as $catID => $categoryObj) {
                $ret .= self::addCategoryOption($categoryObj, $selectedid);
            }
        }

        return $ret;
    }

    /**
     * @return bool
     */
    public static function moderator()
    {
        global $xoopsUser, $xoopsDB, $xoopsConfig, $xoopsUser;

        if (!$xoopsUser) {
            $result = false;
        } else {
            $moduleHandler = \xoops_getHandler('module');
            $hModConfig    = \xoops_getHandler('config');

            $smartModule = $moduleHandler->getByDirname('smartmedia');
            if ($smartModule) {
                $module_id = $smartModule->getVar('mid');
            }

            $module_name = $smartModule->getVar('dirname');
            $smartConfig = $hModConfig->getConfigsByCat(0, $smartModule->getVar('mid'));

            $grouppermHandler = \xoops_getHandler('groupperm');

            $categories = $grouppermHandler->getItemIds('category_moderation', $xoopsUser->getVar('uid'), $module_id);
            if (0 == \count($categories)) {
                $result = false;
            } else {
                $result = true;
            }
        }

        return $result;
    }

    public static function modFooter()
    {
        global $xoopsUser, $xoopsDB, $xoopsConfig;

        $moduleHandler = \xoops_getHandler('module');
        $hModConfig    = \xoops_getHandler('config');

        $smartModule = $moduleHandler->getByDirname('smartmedia');
        $module_id   = $smartModule->getVar('mid');

        $module_name = $smartModule->getVar('dirname');
        $smartConfig = &$hModConfig->getConfigsByCat(0, $smartModule->getVar('mid'));

        $module_id = $smartModule->getVar('mid');

        $versioninfo  = $moduleHandler->get($smartModule->getVar('mid'));
        $modfootertxt = 'Module ' . $versioninfo->getInfo('name') . ' - Version ' . $versioninfo->getInfo('version') . '';

        $modfooter = "<a href='" . $versioninfo->getInfo('developer_website_url') . "' target='_blank'><img src='" . XOOPS_URL . "/modules/smartmedia/assets/images/sscssbutton.gif' title='" . $modfootertxt . "' alt='" . $modfootertxt . "'></a>";

        echo "<div style='padding-top: 10px;' align='center'>" . $modfooter . '</div>';
    }

    /**
     * Checks if a user is admin of SmartMedia
     *
     * smartmedia_userIsAdmin()
     *
     * @return bool : array with userids and uname
     */
    public static function userIsAdmin()
    {
        global $xoopsUser, $xoopsModule;

        $result = false;

        $moduleHandler = \xoops_getHandler('module');
        $smartModule   = $moduleHandler->getByDirname('smartmedia');
        if ($smartModule) {
            $module_id = $smartModule->getVar('mid');
        }

        if (!empty($xoopsUser)) {
            $groups = $xoopsUser->getGroups();
            $result = \in_array(XOOPS_GROUP_ADMIN, $groups) || $xoopsUser->isAdmin($module_id);
        }

        return $result;
    }

    /**
     * Checks if a user has access to a selected item. If no item permissions are
     * set, access permission is denied. The user needs to have necessary category
     * permission as well.
     *
     * smartmedia_itemAccessGranted()
     *
     * @param int     $itemid : itemid on which we are setting permissions
     * @param         $categoryid
     * @return bool : TRUE if the no errors occured
     */

    // TODO : Move this function to ssItem class
    public static function itemAccessGranted($itemid, $categoryid)
    {
        global $xoopsUser;

        if (smartmedia_userIsAdmin()) {
            $result = true;
        } else {
            $result = false;

            $groups = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

            $grouppermHandler = \xoops_getHandler('groupperm');
            $moduleHandler    = \xoops_getHandler('module');
            $hModConfig       = \xoops_getHandler('config');

            $smartModule = $moduleHandler->getByDirname('smartmedia');

            $module_id = $smartModule->getVar('mid');
            // Do we have access to the parent category
            if ($grouppermHandler->checkRight('category_read', $categoryid, $groups, $module_id)) {
                // Do we have access to the item ?
                if ($grouppermHandler->checkRight('item_read', $itemid, $groups, $module_id)) {
                    $result = true;
                } else { // No we don't !
                    $result = false;
                }
            } else { // No we don't !
                $result = false;
            }
        }

        return $result;
    }

    /**
     * Override ITEMs permissions of a category by the category read permissions
     *
     *   smartmedia_overrideItemsPermissions()
     *
     * @param array $groups     : group with granted permission
     * @param int   $categoryid :
     * @return bool : TRUE if the no errors occured
     */
    public static function overrideItemsPermissions($groups, $categoryid)
    {
        global $xoopsDB;

        $result        = true;
        $moduleHandler = \xoops_getHandler('module');
        $smartModule   = $moduleHandler->getByDirname('smartmedia');

        $module_id        = $smartModule->getVar('mid');
        $grouppermHandler = \xoops_getHandler('groupperm');

        $sql    = 'SELECT itemid FROM ' . $xoopsDB->prefix('smartmedia_item') . " WHERE categoryid = '$categoryid' ";
        $result = $xoopsDB->query($sql);

        if (\count($result) > 0) {
            while (list($itemid) = $xoopsDB->fetchRow($result)) {
                // First, if the permissions are already there, delete them
                $grouppermHandler->deleteByModule($module_id, 'item_read', $itemid);
                // Save the new permissions
                if (\count($groups) > 0) {
                    foreach ($groups as $group_id) {
                        $grouppermHandler->addRight('item_read', $itemid, $group_id, $module_id);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Saves permissions for the selected item
     *
     *   smartmedia_saveItemPermissions()
     *
     * @param array $groups : group with granted permission
     * @param int   $itemID : itemid on which we are setting permissions
     * @return bool : TRUE if the no errors occured
     */
    public static function saveItemPermissions($groups, $itemID)
    {
        $result        = true;
        $moduleHandler = \xoops_getHandler('module');
        $smartModule   = $moduleHandler->getByDirname('smartmedia');

        $module_id        = $smartModule->getVar('mid');
        $grouppermHandler = \xoops_getHandler('groupperm');
        // First, if the permissions are already there, delete them
        $grouppermHandler->deleteByModule($module_id, 'item_read', $itemID);
        // Save the new permissions
        if (\count($groups) > 0) {
            foreach ($groups as $group_id) {
                $grouppermHandler->addRight('item_read', $itemID, $group_id, $module_id);
            }
        }

        return $result;
    }

    /**
     * Saves permissions for the selected category
     *
     *   smartmedia_saveCategory_Permissions()
     *
     * @param array  $groups     : group with granted permission
     * @param int    $categoryid : categoryid on which we are setting permissions
     * @param string $perm_name  : name of the permission
     * @return bool : TRUE if the no errors occured
     */
    public static function saveCategory_Permissions($groups, $categoryid, $perm_name)
    {
        $result        = true;
        $moduleHandler = \xoops_getHandler('module');
        $smartModule   = $moduleHandler->getByDirname('smartmedia');

        $module_id        = $smartModule->getVar('mid');
        $grouppermHandler = \xoops_getHandler('groupperm');
        // First, if the permissions are already there, delete them
        $grouppermHandler->deleteByModule($module_id, $perm_name, $categoryid);
        // Save the new permissions
        if (\count($groups) > 0) {
            foreach ($groups as $group_id) {
                $grouppermHandler->addRight($perm_name, $categoryid, $group_id, $module_id);
            }
        }

        return $result;
    }

    /**
     * Saves permissions for the selected category
     *
     *   smartmedia_saveModerators()
     *
     * @param array $moderators : moderators uids
     * @param int   $categoryid : categoryid on which we are setting permissions
     * @return bool : TRUE if the no errors occured
     */
    public static function saveModerators($moderators, $categoryid)
    {
        $result           = true;
        $moduleHandler    = \xoops_getHandler('module');
        $smartModule      = $moduleHandler->getByDirname('smartmedia');
        $module_id        = $smartModule->getVar('mid');
        $grouppermHandler = \xoops_getHandler('groupperm');
        // First, if the permissions are already there, delete them
        $grouppermHandler->deleteByModule($module_id, 'category_moderation', $categoryid);
        // Save the new permissions
        if (\count($moderators) > 0) {
            foreach ($moderators as $uid) {
                $grouppermHandler->addRight('category_moderation', $categoryid, $uid, $module_id);
            }
        }

        return $result;
    }

    /**
     * smartmedia_getAdminLinks()
     *
     * @param int   $itemid
     * @param mixed $userid
     * @param mixed $name
     * @param mixed $users
     * @return
     */

    /**
     * sf_getLinkedUnameFromId()
     *
     * @param int   $userid Userid of poster etc
     * @param int   $name   :  0 Use Usenamer 1 Use realname
     * @param array $users
     * @return int|string
     */
    public static function getLinkedUnameFromId($userid = 0, $name = 0, $users = [])
    {
        if (!\is_numeric($userid)) {
            return $userid;
        }

        $userid = (int)$userid;
        if ($userid > 0) {
            if ($users == []) {
                //fetching users
                $memberHandler = \xoops_getHandler('member');
                $user          = $memberHandler->getUser($userid);
            } else {
                if (!isset($users[$userid])) {
                    return $GLOBALS['xoopsConfig']['anonymous'];
                }
                $user = &$users[$userid];
            }

            if (\is_object($user)) {
                $ts       = \MyTextSanitizer::getInstance();
                $username = $user->getVar('uname');
                $fullname = '';

                $fullname2 = $user->getVar('name');

                if ($name && !empty($fullname2)) {
                    $fullname = $user->getVar('name');
                }
                if (!empty($fullname)) {
                    $linkeduser = "$fullname [<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . $ts->htmlSpecialChars($username) . '</a>]';
                } else {
                    $linkeduser = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . \ucwords($ts->htmlSpecialChars($username)) . '</a>';
                }

                return $linkeduser;
            }
        }

        return $GLOBALS['xoopsConfig']['anonymous'];
    }

    /**
     * @param string $url
     * @return mixed|string
     */
    public static function getxoopslink($url = '')
    {
        $xurl = $url;
        if (mb_strlen($xurl) > 0) {
            $xurl[0] = '/';
            if ($xurl[0]) {
                $xurl = \str_replace('/', '', $xurl);
            }
            $xurl = \str_replace('{SITE_URL}', XOOPS_URL, $xurl);
        }
        $xurl = $url;

        return $xurl;
    }

    /**
     * @param int    $currentoption
     * @param string $breadcrumb
     */
    public static function adminMenu($currentoption = 0, $breadcrumb = '')
    {
        /* Nice buttons styles */
        echo "
        <style type='text/css'>
        #buttontop { float:left; width:100%; background: #e7e7e7; font-size:93%; line-height:normal; border-top: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; margin: 0; }
        #buttonbar { float:left; width:100%; background: #e7e7e7 url('" . XOOPS_URL . "/modules/smartmedia/assets/images/bg.gif') repeat-x left bottom; font-size:93%; line-height:normal; border-left: 1px solid black; border-right: 1px solid black; margin-bottom: 12px; }
        #buttonbar ul { margin:0; margin-top: 15px; padding:10px 10px 0; list-style:none; }
        #buttonbar li { display:inline; margin:0; padding:0; }
        #buttonbar a { float:left; background:url('" . XOOPS_URL . "/modules/smartmedia/assets/images/left_both.gif') no-repeat left top; margin:0; padding:0 0 0 9px; border-bottom:1px solid #000; text-decoration:none; }
        #buttonbar a span { float:left; display:block; background:url('" . XOOPS_URL . "/modules/smartmedia/assets/images/right_both.gif') no-repeat right top; padding:5px 15px 4px 6px; font-weight:bold; color:#765; }
        /* Commented Backslash Hack hides rule from IE5-Mac \*/
        #buttonbar a span {float:none;}
        /* End IE5-Mac hack */
        #buttonbar a:hover span { color:#333; }
        #buttonbar #current a { background-position:0 -150px; border-width:0; }
        #buttonbar #current a span { background-position:100% -150px; padding-bottom:5px; color:#333; }
        #buttonbar a:hover { background-position:0% -150px; }
        #buttonbar a:hover span { background-position:100% -150px; }
        </style>
    ";

        // global $xoopsDB, $xoopsModule, $xoopsConfig, $xoopsModuleConfig;
        global $xoopsModule, $xoopsConfig;
        $myts = \MyTextSanitizer::getInstance();

        $tblColors                 = [];
        $tblColors[0]              = $tblColors[1] = $tblColors[2] = $tblColors[3] = $tblColors[4] = $tblColors[5] = $tblColors[6] = $tblColors[7] = $tblColors[8] = '';
        $tblColors[$currentoption] = 'current';

        /** @var \XoopsModules\Smartmedia\Helper $helper */
        $helper = \XoopsModules\Handler::getInstance();
        $helper->loadLanguage('modinfo');

        echo "<div id='buttontop'>";
        echo '<table style="width: 100%; padding: 0; " cellspacing="0"><tr>';
        //echo "<td style=\"width: 45%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;\"><a class=\"nobutton\" href=\"../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod=" . $xoopsModule->getVar('mid') . "\">" . _AM_SMARTMEDIA_OPTS . "</a> | <a href=\"../index.php\">" . _AM_SMARTMEDIA_GOMOD . "</a> | <a href='" . smartmedia_getHelpPath() ."' target=\"_blank\">" . _AM_SMARTMEDIA_HELP . "</a> | <a href=\"about.php\">" . _AM_SMARTMEDIA_ABOUT . "</a></td>";
        echo '<td style="width: 65%; font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;"><a class="nobutton" href="../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod='
             . $xoopsModule->getVar('mid')
             . '">'
             . \_AM_SMARTMEDIA_OPTS
             . "</a> | <a href='"
             . XOOPS_URL
             . '/modules/system/admin.php?fct=modulesadmin&op=update&module='
             . $xoopsModule->getVar('dirname')
             . "'>"
             . \_AM_SMARTMEDIA_UPDATE_MODULE
             . " | <a href='"
             . SMARTMEDIA_URL
             . "admin/upgrade.php?op=checkTables'>"
             . \_AM_SMARTMEDIA_DB_CHECKTABLES
             . ' | <a href="../index.php">'
             . \_AM_SMARTMEDIA_GOMOD
             . '</a> | <a href="about.php">'
             . \_AM_SMARTMEDIA_ABOUT
             . '</a></td>';
        //echo "<td style=\"width: 55%; font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;\"><b>" . $myts->displayTarea($xoopsModule->name()) . " " . _AM_SMARTMEDIA_MODADMIN . "</b> " . $breadcrumb . "</td>";
        echo '<td style="width: 55%; font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;">' . $breadcrumb . '</td>';
        echo '</tr></table>';
        echo '</div>';

        echo "<div id='buttonbar'>";
        echo '<ul>';
        echo "<li id='" . $tblColors[0] . "'><a href=\"index.php\"><span>" . \_AM_SMARTMEDIA_INDEX . '</span></a></li>';
        echo "<li id='" . $tblColors[1] . "'><a href=\"category.php\"><span>" . \_AM_SMARTMEDIA_CATEGORIES . '</span></a></li>';
        echo "<li id='" . $tblColors[2] . "'><a href=\"folder.php\"><span>" . \_AM_SMARTMEDIA_FOLDERS . '</span></a></li>';
        echo "<li id='" . $tblColors[3] . "'><a href=\"clip.php\"><span>" . \_AM_SMARTMEDIA_CLIPS . '</span></a></li>';
        echo "<li id='" . $tblColors[4] . "'><a href=\"format.php\"><span>" . \_AM_SMARTMEDIA_FORMATS . '</span></a></li>';
        echo "<li id='" . $tblColors[5] . "'><a href=\"myblocksadmin.php\"><span>" . \_AM_SMARTMEDIA_BLOCKSANDGROUPS . '</span></a></li>';
        echo '</ul></div>';
    }

    /**
     * @param string $tablename
     * @param string $iconname
     */
    public static function collapsableBar($tablename = '', $iconname = '')
    {
        ?>
        <script type="text/javascript"><!--
            function goto_URL(object) {
                window.location.href = object.options[object.selectedIndex].value;
            }

            function toggle(id) {
                if (document.getElementById) {
                    obj = document.getElementById(id);
                }
                if (document.all) {
                    obj = document.all[id];
                }
                if (document.layers) {
                    obj = document.layers[id];
                }
                if (obj) {
                    if (obj.style.display == "none") {
                        obj.style.display = "";
                    } else {
                        obj.style.display = "none";
                    }
                }

                return false;
            }

            var iconClose = new Image();
            iconClose.src = '../assets/images/icon/close12.gif';
            var iconOpen = new Image();
            iconOpen.src = '../assets/images/icon/open12.gif';

            function toggleIcon(iconName) {
                if (document.images[iconName].src == window.iconOpen.src) {
                    document.images[iconName].src = window.iconClose.src;
                }
                elseif(document.images[iconName].src == window.iconClose.src);
                {
                    document.images[iconName].src = window.iconOpen.src;
                }

                return;
            }

            //-->
        </script>
        <?php
        echo "<h3 style=\"color: #2F5376; font-weight: bold; font-size: 14px; margin: 6px 0 0 0; \"><a href='javascript:;' onclick=\"toggle('" . $tablename . "'); toggleIcon('" . $iconname . "')\";>";
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function gethandler($name)
    {
        static $smartmediaHandlers;

        if (!isset($smartmediaHandlers[$name])) {
            $smartmediaHandlers[$name] = Helper::getInstance()->getHandler($name);
        }

        return $smartmediaHandlers[$name];
    }
}
