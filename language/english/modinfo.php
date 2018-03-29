<?php

/**
 * Module: SmartMedia
 * Author: The SmartFactory <www.smartfactory.ca>
 * Licence: GNU
 */

// Module Info
// The name of this module
global $xoopsModule;

define('_MI_SMARTMEDIA_NAME', 'SmartMedia');

// A brief description of this module
define('_MI_SMARTMEDIA_DESC', 'MultiMedia Management System for your XOOPS site');

// Config options
define('_MI_SMARTMEDIA_ALL', 'All');
define('_MI_SMARTMEDIA_CAT_ON_INDEX', 'Categories count on index page');
define('_MI_SMARTMEDIA_CAT_ON_INDEXDSC', '');
define('_MI_SMARTMEDIA_CLI_PER_FOL', 'Clips count in a folder');
define('_MI_SMARTMEDIA_CLI_PER_FOLDSC', '');
define('_MI_SMARTMEDIA_DEFAULT_LANGUAGE', 'Default language');
define('_MI_SMARTMEDIA_FOL_PER_CAT', 'Folders count in a category');
define('_MI_SMARTMEDIA_FOL_PER_CATDSC', '');
define('_MI_SMARTMEDIA_INDEX_MSG', 'Index page introduction message');
define('_MI_SMARTMEDIA_INDEX_MSGDSC', '');
define('_MI_SMARTMEDIA_LIST_IMG_WIDTH', 'List images width');
define('_MI_SMARTMEDIA_LIST_IMG_WIDTHDSC', '');
define('_MI_SMARTMEDIA_MAIN_IMG_WIDTH', 'Main images width');
define('_MI_SMARTMEDIA_MAIN_IMG_WIDTHDSC', '');
define('_MI_SMARTMEDIA_HIGHLIGHT_COLOR', 'Color used for highlighting searched words');
define('_MI_SMARTMEDIA_HIGHLIGHT_COLORDSC', '');

define('_MI_SMARTMEDIA_CAT_ON_ADMIN', 'Categories count in a page on admin side');
define('_MI_SMARTMEDIA_CAT_ON_ADMINDSC', '');
define('_MI_SMARTMEDIA_FOLDER_ON_ADMIN', 'Folders count in a category on admin side');
define('_MI_SMARTMEDIA_FOLDER_ON_ADMINDSC', '');

// Names of admin menu items

define('_MI_SMARTMEDIA_ADMENU0', 'Home');
define('_MI_SMARTMEDIA_ADMENU1', 'Manager');
define('_MI_SMARTMEDIA_ADMENU2', 'Categories');
define('_MI_SMARTMEDIA_ADMENU3', 'Folders');
define('_MI_SMARTMEDIA_ADMENU4', 'Clips');
define('_MI_SMARTMEDIA_ADMENU5', 'Clip Formats');
define('_MI_SMARTMEDIA_ADMENU6', 'Permissions');
define('_MI_SMARTMEDIA_ADMENU7', 'Blocks and Groups');
define('_MI_SMARTMEDIA_ADMENU8', 'Go to module');
define('_MI_SMARTMEDIA_ABOUT', 'About');

//Names of Blocks and Block information
define('_MI_SMARTMEDIA_BLOCK_CLIPS_RECENT', 'Recent clips list');
define('_MI_SMARTMEDIA_BLOCK_CLIPS_RECENT_DSC', 'List of all new clips');

// About.php constants
define('_MI_SMARTMEDIA_AUTHOR_INFO', 'Developers');
define('_MI_SMARTMEDIA_DEVELOPER_LEAD', 'Lead developer(s)');
define('_MI_SMARTMEDIA_DEVELOPER_CONTRIBUTOR', 'Contributor(s)');
define('_MI_SMARTMEDIA_DEVELOPER_WEBSITE', 'Website');
define('_MI_SMARTMEDIA_DEVELOPER_EMAIL', 'Email');
define('_MI_SMARTMEDIA_DEVELOPER_CREDITS', 'Credits');
define('_MI_SMARTMEDIA_DEMO_SITE', 'SmartFactory Demo Site');
define('_MI_SMARTMEDIA_MODULE_INFO', 'Module Developpment Informations');
define('_MI_SMARTMEDIA_MODULE_STATUS', 'Status');
define('_MI_SMARTMEDIA_MODULE_RELEASE_DATE', 'Release date');
define('_MI_SMARTMEDIA_MODULE_DEMO', 'Demo Site');
define('_MI_SMARTMEDIA_MODULE_SUPPORT', 'Official support site');
define('_MI_SMARTMEDIA_MODULE_BUG', 'Report a bug for this module');
define('_MI_SMARTMEDIA_MODULE_SUBMIT_BUG', 'Submit a bug');
define('_MI_SMARTMEDIA_MODULE_FEATURE', 'Suggest a new feature for this module');
define('_MI_SMARTMEDIA_MODULE_SUBMIT_FEATURE', 'Request a feature');
define('_MI_SMARTMEDIA_MODULE_DISCLAIMER', 'Disclaimer');
define('_MI_SMARTMEDIA_AUTHOR_WORD', "The Author's Word");
define('_MI_SMARTMEDIA_VERSION_HISTORY', 'Version History');
define('_MI_SMARTMEDIA_BY', 'By');

// Beta
define('_MI_SMARTMEDIA_WARNING_BETA', 'This module comes as is, without any guarantees whatsoever.
This module is BETA, meaning it is still under active development. This release is meant for
<b>testing purposes only</b> and we <b>strongly</b> recommend that you do not use it on a live
website or in a production environment.');

// RC
define('_MI_SMARTMEDIA_WARNING_RC', 'This module comes as is, without any guarantees whatsoever. This module
is a Release Candidate and should not be used on a production web site. The module is still under
active development and its use is under your own responsibility, which means the author is not responsible.');

// Final
define('_MI_SMARTMEDIA_WARNING_FINAL', 'This module comes as is, without any guarantees whatsoever. Although this
module is not beta, it is still under active development. This release can be used in a live website
or a production environment, but its use is under your own responsibility, which means the author
is not responsible.');

// The name of this module
define('_MI_SMARTMEDIA_DIRNAME', basename(dirname(dirname(__DIR__))));
define('_MI_SMARTMEDIA_HELP_HEADER', __DIR__ . '/help/helpheader.tpl');
define('_MI_SMARTMEDIA_BACK_2_ADMIN', 'Back to Administration of ');

//help
define('_MI_SMARTMEDIA_HELP_OVERVIEW', 'Overview');

//help multi-page
define('_MI_SMARTMEDIA_DISCLAIMER', 'Disclaimer');
define('_MI_SMARTMEDIA_LICENSE', 'License');
define('_MI_SMARTMEDIA_SUPPORT', 'Support');
define('_MI_SMARTMEDIA_OVERVIEW', 'Overview');

define('_MI_SMARTMEDIA_SHOW_SAMPLE_BUTTON', 'Show Sample Button?');
define('_MI_SMARTMEDIA_SHOW_SAMPLE_BUTTON_DESC', 'If yes, the "Add Sample Data" button will be visible to the Admin. It is Yes as a default for first installation.');

