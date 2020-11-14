<?php

namespace XoopsModules\Smartmedia;

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
 * @since
 * @author       XOOPS Development Team
 */

/**
 * interface Constants
 */
interface Constants
{
    /**#@+
     * Constant definition
     */

    public const DISALLOW = 0;
    // CONFIG displayicons
    public const DISPLAYICONS_ICON = 1;
    public const DISPLAYICONS_TEXT = 2;
    public const DISPLAYICONS_NO = 3;
    // CONFIG submissions
    public const SUBMISSIONS_NONE = 1;
    public const SUBMISSIONS_DOWNLOAD = 2;
    public const SUBMISSIONS_MIRROR = 3;
    public const SUBMISSIONS_BOTH = 4;
    // CONFIG anonpost
    public const ANONPOST_NONE = 1;
    public const ANONPOST_DOWNLOAD = 2;
    public const ANONPOST_MIRROR = 3;
    public const ANONPOST_BOTH = 4;
    // CONFIG autoapprove
    public const AUTOAPPROVE_NONE = 1;
    public const AUTOAPPROVE_DOWNLOAD = 2;
    public const AUTOAPPROVE_MIRROR = 3;
    public const AUTOAPPROVE_BOTH = 4;
    public const DEFAULT_ELEMENT_SIZE = 1;
    /**#@-*/
}
