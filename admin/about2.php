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
 * Module: SmartClient
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

use XoopsModules\Smartmedia;

require_once __DIR__ . '/admin_header.php';

//require_once SMARTMEDIA_ROOT_PATH . "class/about.php";
$aboutObj = new Smartmedia\About(_AM_SMARTMEDIA_ABOUT);
$aboutObj->render();
