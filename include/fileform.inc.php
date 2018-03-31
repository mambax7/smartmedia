<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

require_once XOOPS_ROOT_PATH . '/modules/smartmedia/include/functions.php';
require_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

global $smartmedia_fileHandler;

$fileid = \Xmf\Request::getInt('fileid', 0, 'GET');

if (0 != $fileid) {
    $fileObj = new ssFile($fileid);
} else {
    $fileObj =& $smartmedia_fileHandler->create();
}

// FILES UPLOAD FORM
$files_form = new \XoopsThemeForm(_MD_SMARTMEDIA_UPLOAD_FILE, 'files_form', xoops_getenv('PHP_SELF'));
$files_form->setExtra("enctype='multipart/form-data'");

// NAME
$name_text = new \XoopsFormText(_MD_SMARTMEDIA_FILE_NAME, 'name', 50, 255, $fileObj->name());
$name_text->setDescription(_MD_SMARTMEDIA_FILE_NAME_DSC);
$files_form->addElement($name_text, true);

// DESCRIPTION
$description_text = new \XoopsFormTextArea(_MD_SMARTMEDIA_FILE_DESCRIPTION, 'description', $fileObj->description());
$description_text->setDescription(_MD_SMARTMEDIA_FILE_DESCRIPTION_DSC);
$files_form->addElement($description_text, 7, 60);

// FILE TO UPLOAD
if (0 == $fileid) {
    $file_box = new \XoopsFormFile(_MD_SMARTMEDIA_FILE_TO_UPLOAD, 'my_file', $max_imgsize);
    $file_box->setExtra("size ='50'");
    $files_form->addElement($file_box);
}

$files_button_tray = new \XoopsFormElementTray('', '');
$files_hidden      = new \XoopsFormHidden('op', 'uploadfile');
$files_button_tray->addElement($files_hidden);

if (0 == $fileid) {
    $files_butt_create = new \XoopsFormButton('', '', _MD_SMARTMEDIA_UPLOAD, 'submit');
    $files_butt_create->setExtra('onclick="this.form.elements.op.value=\'uploadfile\'"');
    $files_button_tray->addElement($files_butt_create);
} else {
    $files_butt_create = new \XoopsFormButton('', '', _MD_SMARTMEDIA_MODIFY, 'submit');
    $files_butt_create->setExtra('onclick="this.form.elements.op.value=\'modify\'"');
    $files_button_tray->addElement($files_butt_create);
}

$files_butt_clear = new \XoopsFormButton('', '', _MD_SMARTMEDIA_CLEAR, 'reset');
$files_button_tray->addElement($files_butt_clear);

$butt_cancel = new \XoopsFormButton('', '', _MD_SMARTMEDIA_CANCEL, 'button');
$butt_cancel->setExtra('onclick="history.go(-1)"');
$files_button_tray->addElement($butt_cancel);

$files_form->addElement($files_button_tray);

// fileid
$files_form->addElement(new XoopsFormHidden('fileid', $fileid));

// itemid
$files_form->addElement(new XoopsFormHidden('itemid', $itemid));

$files_form->display();
