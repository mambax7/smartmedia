<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */
$moduleDirName = basename(dirname(dirname(__DIR__)));
$modinfoLang   = '_MI_' . mb_strtoupper($moduleDirName);
$adminLang     = '_AM_' . mb_strtoupper($moduleDirName);

define('_AM_SMARTMEDIA_ABOUT', 'About');
define('_AM_SMARTMEDIA_ACTION', 'Action');
define('_AM_SMARTMEDIA_ALL', 'All');
define('_AM_SMARTMEDIA_ALL_EXP', '<b>All status</b> :All items of the module, whatever their status.');
define('_AM_SMARTMEDIA_ASC', 'Ascending');
define('_AM_SMARTMEDIA_AVAILABLE', "<span style='font-weight: bold; color: green;'>Available</span>");
define('_AM_SMARTMEDIA_BACK2IDX', 'Cancelled. Taking you back to the index');
define('_AM_SMARTMEDIA_BLOCKS', 'Blocks management');
define('_AM_SMARTMEDIA_BLOCKSTXT', 'This module has the following blocks, which you can configure here or in the system module.');
define('_AM_SMARTMEDIA_BLOCKSANDGROUPS', 'Blocks and Groups');
define('_AM_SMARTMEDIA_BLOCKSGROUPSADMIN', 'Blocks and Groups Management');
define('_AM_SMARTMEDIA_BY', 'by');
define('_AM_SMARTMEDIA_CANCEL', 'Cancel');
define('_AM_SMARTMEDIA_CATCREATED', 'New category was created and saved!');

// Categories

define('_AM_SMARTMEDIA_CATEGORIES_DSC', 'Here is a list of all the categories of the module.');
define('_AM_SMARTMEDIA_CATEGORIES_TITLE', 'Created categories');
define('_AM_SMARTMEDIA_CATEGORY', 'Category');
define('_AM_SMARTMEDIA_CATEGORY_ADD', 'Add this folder to');
define('_AM_SMARTMEDIA_CATEGORY_CANNOT_DELETE_HAS_CHILD', 'At least one folder is linked to this category.<br>Please delete all linked folders before deleting this category.');
define('_AM_SMARTMEDIA_CATEGORY_CHANGE', "Change this folder's category to");
define('_AM_SMARTMEDIA_CATEGORY_CREATE', 'Create a category');
define('_AM_SMARTMEDIA_CATEGORY_CREATE_INFO', 'Fill the following form in order to create a new category.');
define('_AM_SMARTMEDIA_CATEGORY_EDIT_INFO', 'You can edit this category. Modifications will immediatly take effect in the user side.');
define('_AM_SMARTMEDIA_CATEGORY_FOLDER', 'Category -> Folders');
define('_AM_SMARTMEDIA_CATEGORY_LANGUAGE_INFO_EDITING', "Editing category's translation");
define('_AM_SMARTMEDIA_CATEGORY_LANGUAGE_INFO_EDITING_INFO', 'Complete this form in order to configure for this category the content related to the selected language.');
define('_AM_SMARTMEDIA_CATEGORY_TEXT_CREATE', 'Create translation');
define('_AM_SMARTMEDIA_CATEGORY_TITLE', 'Category title');
define('_AM_SMARTMEDIA_CATEGORY_SAVE_ERROR', 'An error occured while saving the category. Here is a list of error(s) :');
define('_AM_SMARTMEDIA_CATEGORY_REQ', 'Category name*');

define('_AM_SMARTMEDIA_CLEAR', 'Clear');
define('_AM_SMARTMEDIA_COLDESCRIPT', 'Category description');
define('_AM_SMARTMEDIA_COLDESCRIPTDSC', 'This description will be displayed on the index page of the module.');
define('_AM_SMARTMEDIA_COLISDELETED', 'Category %s has been deleted');
define('_AM_SMARTMEDIA_COLMODIFIED', 'The category was successfully modified.');
define('_AM_SMARTMEDIA_COLPOSIT', 'Category position');
define('_AM_SMARTMEDIA_CATEGORIES', 'Categories');
define('_AM_SMARTMEDIA_CREATE', 'Create');
define('_AM_SMARTMEDIA_CREATED', 'Created');
define('_AM_SMARTMEDIA_CREATETHEDIR', 'Create the folder');
define('_AM_SMARTMEDIA_CREATINGNEW', 'Creating new');

// Upgrade Database constants

define('_AM_SMARTMEDIA_DB_CHECKTABLES', 'Check tables');
define('_AM_SMARTMEDIA_DB_CURRENTVER', 'Current Version <span class="currentVer">%s</span>');
define('_AM_SMARTMEDIA_DB_DBVER', 'Database Version <span class="dbVer">%s</span>');
define('_AM_SMARTMEDIA_DB_NOUPDATE', 'Your database is up-to-date. No updates are necessary.');
define('_AM_SMARTMEDIA_DB_NEEDUPDATE', 'Your database is out-of-date. Please upgrade your database tables!<br><b>Note : The SmartFactory strongly recommends you to backup all SmartSection tables before running this upgrade script.</b><br>');
define('_AM_SMARTMEDIA_DB_UPDATE_NOW', 'Update Now!');
define('_AM_SMARTMEDIA_DB_NEEDINSTALL', 'Your database is out of sync with the installed version. Please install the same version as the database');
define('_AM_SMARTMEDIA_DB_VERSION_ERR', 'Unable to determine previous version.');
define('_AM_SMARTMEDIA_DB_MSG_MODIFYTABLE', 'Modified table %s');
define('_AM_SMARTMEDIA_DB_MSG_MODIFYTABLE_ERR', 'Error modifying table %s');
define('_AM_SMARTMEDIA_DB_MSG_ADDFIELD', 'Adding field %s in table %s');
define('_AM_SMARTMEDIA_DB_MSG_ADDFIELD_ERR', 'Error adding field %s in table %s');
define('_AM_SMARTMEDIA_DB_MSG_DROPFIELD', 'Droping field %s in table %s');
define('_AM_SMARTMEDIA_DB_MSG_DROPFIELD_ERR', 'Error droping field %s in table %s');

define('_AM_SMARTMEDIA_DB_MSG_UPDATE_TABLE', 'Record updated in table %s');
define('_AM_SMARTMEDIA_DB_MSG_UPDATE_TABLE_ERR', 'Error while updating record in table %s');
define('_AM_SMARTMEDIA_DB_MSG_CREATE_TABLE', 'Table %s created');
define('_AM_SMARTMEDIA_DB_MSG_CREATE_TABLE_ERR', 'Error creating table %s');
define('_AM_SMARTMEDIA_DB_MSG_ADD_DATA', 'Data added in table %s');
define('_AM_SMARTMEDIA_DB_MSG_ADD_DATA_ERR', 'Error adding data in table %s');
define('_AM_SMARTMEDIA_DB_UPDATE_DB', 'Updating Database');
define('_AM_SMARTMEDIA_DB_UPDATE_TO', 'Updating to version %s');
define('_AM_SMARTMEDIA_DB_UPDATE_OK', 'Successfully updated to version %s');
define('_AM_SMARTMEDIA_DB_UPDATE_ERR', 'Errors updating to version %s');
define('_AM_SMARTMEDIA_DB_MSG_DROP_TABLE', 'Table %s was removed from your database.');
define('_AM_SMARTMEDIA_DB_MSG_DROP_TABLE_ERR', 'Error: table %s was NOT removed from your database.');
define('_AM_SMARTMEDIA_DB_MSG_RENAME_TABLE', 'Table %s was renamed to %s.');
define('_AM_SMARTMEDIA_DB_MSG_RENAME_TABLE_ERR', 'Error: table %s was not renamed.');

define('_AM_SMARTMEDIA_DELETE', 'Delete');
define('_AM_SMARTMEDIA_DELETEITEM', 'Delete item');
define('_AM_SMARTMEDIA_DELETE_CAT_CONFIRM', 'Please note that by deleting a category, all the sub-categories and the content of this category will be deleted as well, along with any comments that may have been posted. Are you sure you wish to delete this category ?');
define('_AM_SMARTMEDIA_DELETE_CAT_ERROR', 'An error occured while deleting this category.');
define('_AM_SMARTMEDIA_DELETE_CAT_SUCCESS', "The category's translation has been successfully deleted.");
define('_AM_SMARTMEDIA_DELETE_CAT_TEXT', "Delete this category's translation ?");
define('_AM_SMARTMEDIA_DELETE_CAT_TEXT_ERROR', "An error occured while deleting this category's translation.");
define('_AM_SMARTMEDIA_DELETECOL', 'Delete category');
define('_AM_SMARTMEDIA_DESC', 'Descending');
define('_AM_SMARTMEDIA_DESCRIP', 'Category description');
define('_AM_SMARTMEDIA_DESCRIPTION', 'Description');
define('_AM_SMARTMEDIA_DIRCREATED', 'Folder successfully created ');
define('_AM_SMARTMEDIA_DIRNOTCREATED', 'The folder could not be created ');
define('_AM_SMARTMEDIA_DISPLAY_LIMIT', 'Show');
define('_AM_SMARTMEDIA_DOHTML', ' Enable HTML tags');
define('_AM_SMARTMEDIA_DOIMAGE', ' Enable images');
define('_AM_SMARTMEDIA_DOLINEBREAK', ' Enable linebreak');
define('_AM_SMARTMEDIA_DOSMILEY', ' Enable smiley icons');
define('_AM_SMARTMEDIA_DOXCODE', ' Enable XOOPS codes');
define('_AM_SMARTMEDIA_EDITCOL', 'Edit category');
define('_AM_SMARTMEDIA_EDITING', 'Editing');
define('_AM_SMARTMEDIA_ERROR', ' An error has occurred.');

// Clips

define('_AM_SMARTMEDIA_CLIP', 'Clip');
define('_AM_SMARTMEDIA_CLIP_CREATE', 'Create a clip');
define('_AM_SMARTMEDIA_CLIP_CREATED', 'The clip has been successfully created.');
define('_AM_SMARTMEDIA_CLIP_CREATE_INFO', 'Fill the following form in order to create a new clip.');
define('_AM_SMARTMEDIA_CLIP_DELETE', 'Delete clip');
define('_AM_SMARTMEDIA_CLIP_DELETE_ERROR', 'An error occured while deleting this clip.');
define('_AM_SMARTMEDIA_CLIP_DELETE_SUCCESS', 'This clip was successfully deleted.');
define('_AM_SMARTMEDIA_CLIP_DESCRIPTION', 'Summary');
define('_AM_SMARTMEDIA_CLIP_DESCRIPTIONDSC', 'Text displayed in the folder page, on the list of clips within the folder.');
define('_AM_SMARTMEDIA_CLIP_EDIT', 'Edit clip');
define('_AM_SMARTMEDIA_CLIP_EDIT_INFO', 'You can edit this clip. Modifications will immediatly take effect in the user side.');
define('_AM_SMARTMEDIA_CLIP_FILE_LR', 'Low resolution clip URL');
define('_AM_SMARTMEDIA_CLIP_FILE_LRDSC', 'Clip that will be displayed in the module layout.');
define('_AM_SMARTMEDIA_CLIP_FILE_HR', 'High resolution clip URL');
define('_AM_SMARTMEDIA_CLIP_FILE_HRDSC', 'Will be displayed in its full size version in a new window.');
define('_AM_SMARTMEDIA_CLIP_FORMAT', "Clip's format");
define('_AM_SMARTMEDIA_CLIP_IMAGE_HR', 'Image');
define('_AM_SMARTMEDIA_CLIP_IMAGE_HR_DSC', 'Image that represents this clip (Recommended width is %spx)');
define('_AM_SMARTMEDIA_CLIP_IMAGE_HR_UPLOAD', 'Image upload');
define('_AM_SMARTMEDIA_CLIP_IMAGE_HR_UPLOAD_DSC', 'Select an image on your computer. This image will be uploaded to the site and set as the image for this clip.');
define('_AM_SMARTMEDIA_CLIP_IMAGE_LR', 'Low resolution image');
define('_AM_SMARTMEDIA_CLIP_IMAGE_LR_DSC', 'Low resolution image that represents this clip.');
define('_AM_SMARTMEDIA_CLIP_IMAGE_LR_UPLOAD', 'Low resolution image upload');
define('_AM_SMARTMEDIA_CLIP_IMAGE_LR_UPLOAD_DSC', 'Select an image on your computer. This image will be uploaded to the site and set as the low resolution image for this clip.');
define('_AM_SMARTMEDIA_CLIP_LANGUAGE_INFO_EDITING', "Editing clip's translation");
define('_AM_SMARTMEDIA_CLIP_LANGUAGE_INFO_EDITING_INFO', 'Complete this form in order to configure for this clip the content related to the selected language.');
define('_AM_SMARTMEDIA_CLIP_META_DESCRIPTION', 'Meta description');
define('_AM_SMARTMEDIA_CLIP_META_DESCRIPTIONDSC', 'Description displayed for search engines in the meta tag of the page.');
define('_AM_SMARTMEDIA_CLIP_MODIFIED', 'The clip has been successfully modified.');
define('_AM_SMARTMEDIA_CLIP_SAVE_ERROR', 'An error occured while saving this clip.');
define('_AM_SMARTMEDIA_CLIP_SHORT_TITLE', 'Short title');
define('_AM_SMARTMEDIA_CLIP_SUMMARY', 'Summary');
define('_AM_SMARTMEDIA_CLIP_TAB_CAPTION_1', 'Caption of Tab 1');
define('_AM_SMARTMEDIA_CLIP_TAB_CAPTION_2', 'Caption of Tab 2');
define('_AM_SMARTMEDIA_CLIP_TAB_CAPTION_3', 'Caption of Tab 3');
define('_AM_SMARTMEDIA_CLIP_TAB_TEXT_1', 'Content of Tab 1');
define('_AM_SMARTMEDIA_CLIP_TAB_TEXT_2', 'Content of Tab 2');
define('_AM_SMARTMEDIA_CLIP_TAB_TEXT_3', 'Content of Tab 3');
define('_AM_SMARTMEDIA_CLIP_TABDSC', "You can use the tabs to add more information to the clip. The tabs will be shown next to the clip. If you don't want to use a tab, simply leav it blank and it will not be displayed on the user side.");
define('_AM_SMARTMEDIA_CLIP_TEXT_CREATE', 'Create New Translation');
define('_AM_SMARTMEDIA_CLIP_TEXT_DELETE', "Delete this clip's translation ?");
define('_AM_SMARTMEDIA_CLIP_TEXT_DELETE_ERROR', "An error occured while deleting this clip's translation.");
define('_AM_SMARTMEDIA_CLIP_TEXT_DELETE_SUCCESS', "This clip's translation was successfully deleted.");
define('_AM_SMARTMEDIA_CLIP_TITLE', 'Title');
define('_AM_SMARTMEDIA_CLIP_TITLE_REQ', 'Title*');
define('_AM_SMARTMEDIA_CLIP_WEIGHT', 'Weight');
define('_AM_SMARTMEDIA_CLIPS', 'Clips');
define('_AM_SMARTMEDIA_CLIPS_ALL', 'All clips');
define('_AM_SMARTMEDIA_CLIPS_ALL_DSC', 'Here is a list of all clips within the module.');
define('_AM_SMARTMEDIA_CLIPS_DSC', 'Here is a list of all clips within folder <b><i>%s</i></b>.');
define('_AM_SMARTMEDIA_CLIPS_TITLE', 'Created clips');
define('_AM_SMARTMEDIA_CLIPS_WITHIN_FOLDER', 'Clips within folder <b><i>%s</i></b>');

define('_AM_SMARTMEDIA_CLIP_WIDTH', 'Clip width');
define('_AM_SMARTMEDIA_CLIP_WIDTHDSC', '');
define('_AM_SMARTMEDIA_CLIP_HEIGHT', 'Clip height');
define('_AM_SMARTMEDIA_CLIP_HEIGHTDSC', '');
define('_AM_SMARTMEDIA_EDIT', 'Edit');

// Folders

define('_AM_SMARTMEDIA_FOLDER', 'Folder');
define('_AM_SMARTMEDIA_FOLDER_CANNOT_DELETE_HAS_CHILD', 'At least one clip is linked this folder.<br>Please delete all linked clips before deleting this folder.');
define('_AM_SMARTMEDIA_FOLDER_CLIP', 'Folder -> Clip');
define('_AM_SMARTMEDIA_FOLDER_CREATE', 'Create a folder');
define('_AM_SMARTMEDIA_FOLDER_CREATED', 'The folder has been successfully created.');
define('_AM_SMARTMEDIA_FOLDER_CREATE_INFO', 'Fill the following form in order to create a new folder.');
define('_AM_SMARTMEDIA_FOLDER_DELETE', 'Delete folder');
define('_AM_SMARTMEDIA_FOLDER_DELETE_ERROR', 'An error occured while deleting this folder.');
define('_AM_SMARTMEDIA_FOLDER_DELETE_SUCCESS', 'This folder was successfully deleted.');
define('_AM_SMARTMEDIA_FOLDER_DESCRIPTION', 'Description');
define('_AM_SMARTMEDIA_FOLDER_DESCRIPTIONDSC', 'This description will be displayed at the top of the folder page.');
define('_AM_SMARTMEDIA_FOLDER_EDIT', 'Edit folder');
define('_AM_SMARTMEDIA_FOLDER_EDIT_INFO', 'You can edit this folder. Modifications will immediatly take effect in the user side.');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_HR', 'Image');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_HR_DSC', 'Image that represents this folder (Recommended width is %spx)');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_HR_UPLOAD', 'Image upload');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_HR_UPLOAD_DSC', 'Select an image on your computer. This image will be uploaded to the site and set as the image for this folder.');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_LR', 'Low resolution image');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_LR_DSC', 'Low resolution image that represents this folder.');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_LR_UPLOAD', 'Low resolution image upload');
define('_AM_SMARTMEDIA_FOLDER_IMAGE_LR_UPLOAD_DSC', 'Select an image on your computer. This image will be uploaded to the site and set as the low resolution image for this folder.');
define('_AM_SMARTMEDIA_FOLDER_LANGUAGE_INFO_EDITING', "Editing folder's translation");
define('_AM_SMARTMEDIA_FOLDER_LANGUAGE_INFO_EDITING_INFO', 'Complete this form in order to configure for this folder the content related to the selected language.');
define('_AM_SMARTMEDIA_FOLDER_META_DESCRIPTION', 'Meta description');
define('_AM_SMARTMEDIA_FOLDER_MODIFIED', 'The folder has been successfully modified.');
define('_AM_SMARTMEDIA_FOLDER_SAVE_ERROR', 'An error occured while saving this folder.');
define('_AM_SMARTMEDIA_FOLDER_SHORT_TITLE', 'Short title');
define('_AM_SMARTMEDIA_FOLDER_SHOW_CLIP', 'Clips within this folder');
define('_AM_SMARTMEDIA_FOLDER_STATUS', 'Status');
define('_AM_SMARTMEDIA_FOLDER_SUMMARY', 'Summary');
define('_AM_SMARTMEDIA_FOLDER_SUMMARYDSC', 'The summary will be displayed in the category page, on the list of folders within the category.');
define('_AM_SMARTMEDIA_FOLDER_TEXT_CREATE', 'Create translation');
define('_AM_SMARTMEDIA_FOLDER_TEXT_DELETE', "Delete this folder's translation ?");
define('_AM_SMARTMEDIA_FOLDER_TEXT_DELETE_ERROR', "An error occured while deleting this folder's translation.");
define('_AM_SMARTMEDIA_FOLDER_TEXT_DELETE_SUCCESS', "This folder's translation was successfully deleted.");
define('_AM_SMARTMEDIA_FOLDER_TITLE', 'Title');
define('_AM_SMARTMEDIA_FOLDER_TITLE_REQ', 'Title*');
define('_AM_SMARTMEDIA_FOLDER_WEIGHT', 'Weight');
define('_AM_SMARTMEDIA_FOLDERS', 'Folders');
define('_AM_SMARTMEDIA_FOLDERS_DSC', 'Here is a list of all folders of the module.');
define('_AM_SMARTMEDIA_FOLDERS_TITLE', 'Created folders');

// FORMAT

define('_AM_SMARTMEDIA_FORMAT', 'Format');
define('_AM_SMARTMEDIA_FORMAT_CREATE', 'Create a format');
define('_AM_SMARTMEDIA_FORMAT_CREATE_ERROR', 'An error occured while creating this format.');
define('_AM_SMARTMEDIA_FORMAT_CREATE_SUCCESS', 'This format was created successfully.');
define('_AM_SMARTMEDIA_FORMAT_DSC', '***to come');
define('_AM_SMARTMEDIA_FORMAT_EDITING', 'Editing a clip format');
define('_AM_SMARTMEDIA_FORMAT_EDITING_INFO', 'You can edit the clip format. Simply fill out the following form.');
define('_AM_SMARTMEDIA_FORMAT_EXT', 'Extension');
define('_AM_SMARTMEDIA_FORMAT_EXT_DSC', '***to come');
define('_AM_SMARTMEDIA_FORMAT_CREATING', 'Create a clip format');
define('_AM_SMARTMEDIA_FORMAT_CREATING_INFO', 'You can create a new clip format. Simply fill the following form.');
define('_AM_SMARTMEDIA_FORMAT_DELETE_ERROR', 'An error occured while deleting this format.');
define('_AM_SMARTMEDIA_FORMAT_DELETE_CONFIRM', 'Do you really want to delete this format ?');
define('_AM_SMARTMEDIA_FORMAT_DELETE_SUCCESS', 'This format was successfully deleted.');
define('_AM_SMARTMEDIA_FORMAT_EDIT_ERROR', 'An error occured while editing this format.');
define('_AM_SMARTMEDIA_FORMAT_EDIT_SUCCESS', 'This format was edited successfully.');
define('_AM_SMARTMEDIA_FORMAT_TEMPLATE', 'Template');
define('_AM_SMARTMEDIA_FORMAT_TEMPLATE_DSC', '***to come');
define('_AM_SMARTMEDIA_FORMATS', 'Clip Formats');
define('_AM_SMARTMEDIA_FORMATS_TITLE', 'Clip Formats');
define('_AM_SMARTMEDIA_FORMATS_TITLE_INFO', 'Here is all the clip formats available in the module. You can add, edit or delete clip formats.');
define('_AM_SMARTMEDIA_FORMATS_NONE', 'No formats');

define('_AM_SMARTMEDIA_DEFAULT_LANGUAGE', 'Default language');
define('_AM_SMARTMEDIA_DEFAULT_LANGUAGE_DSC', 'This is the default language of this item');
define('_AM_SMARTMEDIA_IMAGE', 'Category image');
define('_AM_SMARTMEDIA_IMAGE_DSC', 'Image representing the category (Recommended width is %spx.)');
define('_AM_SMARTMEDIA_IMAGE_UPLOAD', 'Image upload');
define('_AM_SMARTMEDIA_IMAGE_UPLOAD_DSC', 'Select an image on your computer. This image will be uploaded to the site and set as the category image.');
define('_AM_SMARTMEDIA_ITEM_EDIT', 'Edit item');
define('_AM_SMARTMEDIA_ITEM', 'Item');
define('_AM_SMARTMEDIA_ITEMS', 'Items');
define('_AM_SMARTMEDIA_GOMOD', 'Go to module');
define('_AM_SMARTMEDIA_GROUPS', 'Groups management');
define('_AM_SMARTMEDIA_GROUPSINFO', 'Configure module and blocks permissions for each group');
define('_AM_SMARTMEDIA_HELP', 'Help');
define('_AM_SMARTMEDIA_HITS', 'Hits');
define('_AM_SMARTMEDIA_ID', 'Id');
define('_AM_SMARTMEDIA_INDEX', 'Index');
define('_AM_SMARTMEDIA_INVENTORY', 'Module Summary');
define('_AM_SMARTMEDIA_ITEMCATEGORYNAME', 'Category');
define('_AM_SMARTMEDIA_ITEMID', 'Id');
define('_AM_SMARTMEDIA_LANGUAGE', 'Language');
define('_AM_SMARTMEDIA_LANGUAGE_INFO', 'translation');
define('_AM_SMARTMEDIA_LANGUAGE_ITEM', 'Default language of this item');
define('_AM_SMARTMEDIA_LANGUAGE_ITEM_DSC', "If no translation of this item is found for the user's selected language, the item will be displayed in this language. <br>To add a translation, edit an item and click on  'Create translation'.");
define('_AM_SMARTMEDIA_LANGUAGE_NEW', 'Language of the translation');
define('_AM_SMARTMEDIA_LANGUAGE_NEW_DSC', 'The informations that you are about to enter wil be displayed when this language will be selected by the user.');
define('_AM_SMARTMEDIA_MODADMIN', 'Module Admin :');
define('_AM_SMARTMEDIA_MODIFY', 'Modify');
define('_AM_SMARTMEDIA_MODIFYCAT', 'Modify category');
define('_AM_SMARTMEDIA_MODIFYTHISCAT', 'Modify this category?');
define('_AM_SMARTMEDIA_NO', 'No');
define('_AM_SMARTMEDIA_NOFOUND', 'No users match the required string.');
define('_AM_SMARTMEDIA_NONE', 'None');
define('_AM_SMARTMEDIA_NOTAVAILABLE', "<span style='font-weight: bold; color: red;'>Not available</span>");
define('_AM_SMARTMEDIA_NOCAT', 'No categories to display');
define('_AM_SMARTMEDIA_NOCOLTOEDIT', 'There are no categories to edit!');
define('_AM_SMARTMEDIA_NO_LANGUAGE_INFO', 'No translation to display');
define('_AM_SMARTMEDIA_NOTUPDATED', 'There was an error updating the database!');
define('_AM_SMARTMEDIA_OFFLINE', 'Offline');
define('_AM_SMARTMEDIA_OPTIONS', 'Options');
define('_AM_SMARTMEDIA_OPTS', 'Preferences');
define('_AM_SMARTMEDIA_PARENT_CATEGORY_EXP', "Parent category<span style='font-size: xx-small; font-weight: normal; display: block;'>Is this category a sub-category ?<br>If yes, select the category to which<br>will belong the present category.</span>");
define('_AM_SMARTMEDIA_PATH', 'Path');
define('_AM_SMARTMEDIA_PATH_ITEM', 'Upload items');
define('_AM_SMARTMEDIA_PATH_FILES', 'Attached files');
define('_AM_SMARTMEDIA_PATH_IMAGES', 'General images');
define('_AM_SMARTMEDIA_PATH_IMAGES_CATEGORY', 'Category images');
define('_AM_SMARTMEDIA_PATH_IMAGES_FOLDER', 'Folder images');
define('_AM_SMARTMEDIA_PATH_IMAGES_CLIP', 'Clip images');
define('_AM_SMARTMEDIA_PATHCONFIGURATION', 'Module Path Configuration');
define('_AM_SMARTMEDIA_PERSISTENT_INFO', 'Those informations do not change with the language');
define('_AM_SMARTMEDIA_PUBLISH', 'Publish');
define('_AM_SMARTMEDIA_PUBLISHED', 'Published');
define('_AM_SMARTMEDIA_SELECT_SORT', 'Sort order');
define('_AM_SMARTMEDIA_SELECT_STATUS', 'Status');
define('_AM_SMARTMEDIA_SHOWING', 'Showing');
define('_AM_SMARTMEDIA_SORT', 'Sort by');
define('_AM_SMARTMEDIA_TITLE', 'Title');
define('_AM_SMARTMEDIA_TOTALCLIPS', 'Clips :');
define('_AM_SMARTMEDIA_TOTALFOLDERS', 'Folders :');
define('_AM_SMARTMEDIA_TOTALCAT', 'Categories :');
define('_AM_SMARTMEDIA_UPLOAD', 'Upload');
define('_AM_SMARTMEDIA_UPDATE_MODULE', 'Update module');
define('_AM_SMARTMEDIA_UID', 'Poster name');
define('_AM_SMARTMEDIA_UID_DSC', 'Select the name of the poster');
define('_AM_SMARTMEDIA_VIEW_CATS', 'Select categories that each group can view');
define('_AM_SMARTMEDIA_WEIGHT', 'Weight');
define('_AM_SMARTMEDIA_YES', 'Yes');

// New table
define('_AM_SMARTMEDIA_ITEMCAT', 'Category');
define('_AM_SMARTMEDIA_STATUS', 'Status');

//0.86

define('_AM_TOPPAGE', 'Top Page');
define('_AM_ALLPAGES', 'All Pages');
define('_AM_BADMIN', 'Page');
define('_AM_TITLE', 'Title');
define('_AM_SIDE', 'Side');
define('_AM_WEIGHT', 'Weight');
define('_AM_VISIBLE', 'Visible');
define('_AM_VISIBLEIN', 'Visible In');
define('_AM_ACTION', 'Action');

define('_AM_ACTIVERIGHTS', 'Admin Rights');
define('_AM_ACCESSRIGHTS', 'Access Rights');
define('_AM_BCACHETIME', 'Cache Time');
