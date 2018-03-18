<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for managing folders
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: folder.php,v 1.3 2005/06/02 13:33:37 malanciault Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage Folders
 */

use XoopsModules\Smartmedia;

///** Status of an offline folder */
//define('_SMARTMEDIA_FOLDER_STATUS_OFFLINE', 1);
///** Status of an online folder */
//define('_SMARTMEDIA_FOLDER_STATUS_ONLINE', 2);

/**
 * SmartMedia Folder class
 *
 * Class representing a single folder object
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class Folder extends \XoopsObject
{
    /**
     * Language of the folder
     * @var string
     */
    public $languageid;

    /**
     * {@link Smartmedia\FolderText} object holding the folder's text informations
     * @var object
     * @see Smartmedia\FolderText
     */
    public $folder_text = null;

    /**
     * List of all the translations already created for this folder
     * @var array
     * @see getCreatedLanguages
     */
    public $_created_languages = null;

    /**
     * Flag indicating wheter or not a new translation can be added for this folder
     *
     * If all languages of the site are also in {@link $_created_languages} then no new
     * translation can be created
     * @var bool
     * @see canAddLanguage
     */
    public $_canAddLanguage = null;

    /**
     * Constructor
     *
     * @param string $languageid language of the folder
     * @param int    $id         id of the folder to be retreieved OR array containing values to be assigned
     */
    public function __construct($languageid = 'default', $id = null)
    {
        $smartConfig =& smartmedia_getModuleConfig();

        $this->initVar('folderid', XOBJ_DTYPE_INT, -1, true);
        $this->initVar('statusid', XOBJ_DTYPE_INT, _SMARTMEDIA_FOLDER_STATUS_ONLINE, false);
        $this->initVar('created_uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('created_date', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('modified_uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('modified_date', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('image_hr', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('image_lr', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('default_languageid', XOBJ_DTYPE_TXTBOX, $smartConfig['default_language'], false, 50);

        $this->initVar('categoryid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('new_category', XOBJ_DTYPE_INT, 0, false);

        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            }
        } else {
            $this->setNew();
        }

        if ('default' === $languageid) {
            $languageid = $this->default_languageid();
        }

        $this->loadLanguage($languageid);
    }

    /**
     * Check if the folder was successfully loaded
     *
     * @return bool true if not loaded, false if correctly loaded
     */
    public function notLoaded()
    {
        return ($this->folderid() == -1);
    }

    /**
     * Loads the specified translation for this folder
     *
     * If the specified language does not have any translation yet, the translation corresponding
     * to the default language will be loaded
     *
     * @param string $languageid language of the translation to load
     */
    public function loadLanguage($languageid)
    {
        $this->languageid = $languageid;

        $smartmediaFolderTextHandler = Smartmedia\Helper::getInstance()->getHandler('FolderText');
        $this->folder_text           =& $smartmediaFolderTextHandler->get($this->getVar('folderid'), $this->languageid);

        if (!$this->folder_text) {
            $this->folder_text = new Smartmedia\FolderText();
            $this->folder_text->setVar('folderid', $this->folderid());
            $this->folder_text->setVar('languageid', $languageid);

            $default_folder_text = $smartmediaFolderTextHandler->get($this->getVar('folderid'), $this->default_languageid());

            if ($default_folder_text) {
                //$this->folder_text =& $default_folder_text;
                $this->folder_text->setVar('title', $default_folder_text->title());
                $this->folder_text->setVar('short_title', $default_folder_text->short_title());
                $this->folder_text->setVar('summary', $default_folder_text->summary());
                $this->folder_text->setVar('description', $default_folder_text->description());
                $this->folder_text->setVar('meta_description', $default_folder_text->meta_description());
            }
        }
    }

    /**
     * @return int id of this folder
     */
    public function folderid()
    {
        return $this->getVar('folderid');
    }

    /**
     * Returns the status of the folder
     *
     * Status can be {@link _SMARTMEDIA_FOLDER_STATUS_OFFLINE} or {@link _SMARTMEDIA_FOLDER_STATUS_ONLINE}
     * @return string status of the folder
     */
    public function statusid()
    {
        return $this->getVar('statusid');
    }

    /**
     * Returns the date of creation of the folder
     *
     * The date will be formated according to the date format preference of the module
     * @param string $dateFormat
     * @param string $format
     * @return string date of creation of the folder
     */
    public function created_date($dateFormat = 'none', $format = 'S')
    {
        if ('none' === $dateFormat) {
            $smartConfig =& smartmedia_getModuleConfig();
            if (isset($smartConfig['dateformat'])) {
                $dateFormat = $smartConfig['dateformat'];
            } else {
                $dateFormat = 'Y-m-d';
            }
        }

        return formatTimestamp($this->getVar('created_date', $format), $dateFormat);
    }

    /**
     * @return int uid of the user who created the folder
     */
    public function created_uid()
    {
        return $this->getVar('created_uid');
    }

    /**
     * Returns the date of modification of the folder
     *
     * The date will be formated according to the date format preference of the module
     * @param string $dateFormat
     * @param string $format
     * @return string date of modification of the folder
     */
    public function modified_date($dateFormat = 'none', $format = 'S')
    {
        if ('none' === $dateFormat) {
            $smartConfig =& smartmedia_getModuleConfig();
            if (isset($smartConfig['dateformat'])) {
                $dateFormat = $smartConfig['dateformat'];
            } else {
                $dateFormat = 'Y-m-d';
            }
        }

        return formatTimestamp($this->getVar('modified_date', $format), $dateFormat);
    }

    /**
     * @return int uid of the user who modified the folder
     */
    public function modified_uid()
    {
        return $this->getVar('modified_uid');
    }

    /**
     * @return string weight of this clip
     */
    public function weight()
    {
        return $this->getVar('weight');
    }

    /**
     * Returns the categoryid to which this folder belongs
     * @see Smartmedia\Category
     * @return string parent categoryid of this folder
     */
    public function categoryid()
    {
        return $this->getVar('categoryid');
    }

    /**
     * Returns the high resolution image of this folder
     *
     * If no image has been set, the function will return blank.png, so a blank image can
     * be displayed
     *
     * @param  string $format format to use for the output
     * @return string high resolution image of this folder
     */
    public function image_hr($format = 'S')
    {
        if ('' != $this->getVar('image_hr')) {
            return $this->getVar('image_hr', $format);
        } else {
            return 'blank.png';
        }
    }

    /**
     * Returns the low resolution image of this folder
     *
     * If no image has been set, the function will return blank.png, so a blank image can
     * be displayed
     *
     * @param  string $format format to use for the output
     * @return string low resolution image of this folder
     */
    public function image_lr($format = 'S')
    {
        if ('' != $this->getVar('image_lr')) {
            return $this->getVar('image_lr', $format);
        } else {
            return 'blank.png';
        }
    }

    /**
     * @return int counter of this folder
     */
    public function counter()
    {
        return $this->getVar('counter');
    }

    /**
     * Returns the default language of the folder
     *
     * When no translation corresponding to the selected language is available, the folder's
     * information will be displayed in this language
     *
     * @param string $format
     * @return string default language of the folder
     */
    public function default_languageid($format = 'S')
    {
        return $this->getVar('default_languageid', $format);
    }

    /**
     * Returns the title of the folder
     *
     * If the format is "clean", the title will be return, striped from any html tag. This clean
     * title is likely to be used in the page title meta tag or any other place that requires
     * "html less" text
     *
     * @param  string $format format to use for the output
     * @return string title of the folder
     */
    public function title($format = 'S')
    {
        $myts = \MyTextSanitizer::getInstance();
        if (('s' === strtolower($format)) || ('show' === strtolower($format))) {
            return $myts->undoHtmlSpecialChars($this->folder_text->getVar('title', 'e'), 1);
        } elseif ('clean' === strtolower($format)) {
            return smartmedia_metagen_html2text($myts->undoHtmlSpecialChars($this->folder_text->getVar('title')));
        } else {
            return $this->folder_text->getVar('title', $format);
        }
    }

    /**
     * Returns the short title of the folder
     *
     * @param  string $format format to use for the output
     * @return string short title of the folder
     */
    public function short_title($format = 'S')
    {
        if (('s' === strtolower($format)) || ('show' === strtolower($format))) {
            $myts = \MyTextSanitizer::getInstance();

            return $myts->undoHtmlSpecialChars($this->folder_text->getVar('short_title', 'e'), 1);
        } else {
            return $this->folder_text->getVar('short_title', $format);
        }
    }

    /**
     * Returns the summary of the folder
     *
     * @param  string $format format to use for the output
     * @return string summary of the folder
     */
    public function summary($format = 'S')
    {
        return $this->folder_text->getVar('summary', $format);
    }

    /**
     * Returns the description of the folder
     *
     * @param  string $format format to use for the output
     * @return string description of the folder
     */
    public function description($format = 'S')
    {
        return $this->folder_text->getVar('description', $format);
    }

    /**
     * Returns the meta description of the folder
     *
     * @param  string $format format to use for the output
     * @return string meta description of the folder
     */
    public function meta_description($format = 'S')
    {
        return $this->folder_text->getVar('meta_description', $format);
    }

    /**
     * Set a text variable of the clip
     *
     * @param string $key   of the variable to set
     * @param string $value of the field to set
     * @return
     * @see Smartmedia\FolderText
     */
    public function setTextVar($key, $value)
    {
        return $this->folder_text->setVar($key, $value);
    }

    /**
     * Get the complete URL of this folder
     *
     * @return string complete URL of this folder
     */
    public function getItemUrl()
    {
        return SMARTMEDIA_URL . 'folder.php?categoryid=' . $this->categoryid() . '&folderid=' . $this->folderid();
    }

    /**
     * Get the complete hypertext link of this folder
     *
     * @return string complete hypertext link of this folder
     */
    public function getItemLink()
    {
        return "<a href='" . $this->getItemUrl() . "'>" . $this->title() . '</a>';
    }

    /**
     * Stores the folder in the database
     *
     * This method stores the folder as well as the current translation informations for the
     * folder
     *
     * @param  bool $force
     * @return bool true if successfully stored false if an error occured
     *
     * @see Smartmedia\FolderHandler::insert()
     * @see Smartmedia\FolderText::store()
     */
    public function store($force = true)
    {
        global $smartmediaFolderHandler;
        if (!$smartmediaFolderHandler->insert($this, $force)) {
            return false;
        }
        $this->folder_text->setVar('folderid', $this->folderid());
        if (!$this->folder_text->store()) {
            return false;
        }

        return $this->linkToParent($this->categoryid());
    }

    /**
     * Get all the translations created for this folder
     *
     * @param  bool $exceptDefault to determine if the default language should be returned or not
     * @return array array of {@link Smartmedia\FolderText}
     */
    public function getAllLanguages($exceptDefault = false)
    {
        global $smartmediaFolderTextHandler;
        $criteria = new    \CriteriaCompo();
        $criteria->add(new \Criteria('folderid', $this->folderid()));
        if ($exceptDefault) {
            $criteria->add(new \Criteria('languageid', $this->default_languageid(), '<>'));
        }

        return $smartmediaFolderTextHandler->getObjects($criteria);
    }

    /**
     * Get a list of created language
     *
     * @return array array containing the language name of the created translations for this folder
     * @see _created_languages
     * @see Smartmedia\FolderText::getCreatedLanguages()
     */
    public function getCreatedLanguages()
    {
        if (null != $this->_created_languages) {
            return $this->_created_languages;
        }
        global $smartmediaFolderTextHandler;
        $this->_created_languages = $smartmediaFolderTextHandler->getCreatedLanguages($this->folderid());

        return $this->_created_languages;
    }

    /**
     * Check to see if other translations can be added
     *
     * If all languages of the site are also in {@link $_created_languages} then no new
     * translation can be created
     *
     * @return bool true if new translation can be added false if all translation have been created
     * @see _canAddLanguage
     * @see getCreatedLanguages
     */
    public function canAddLanguage()
    {
        if (null != $this->_canAddLanguage) {
            return $this->_canAddLanguage;
        }

        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $languageList     = \XoopsLists::getLangList();
        $createdLanguages = $this->getCreatedLanguages();

        $this->_canAddLanguage = (count($languageList) > count($createdLanguages));

        return $this->_canAddLanguage;
    }

    /**
     * Link the folder to a category
     *
     * @param  int $parentid id of the category to link to
     * @return string hypertext links to edit and delete the clip
     * @see Smartmedia\FolderHandler::linkToParent()
     */
    public function linkToParent($parentid)
    {
        if (0 == (int)$parentid) {
            return true;
        }

        global $smartmediaFolderHandler;

        return $smartmediaFolderHandler->linkToParent($parentid, $this->folderid(), $this->getVar('new_category'));
    }

    /**
     * Render the admin links for this folder
     *
     * This method will create links to Edit and Delete the folder. The method will also check
     * to ensure the user is admin of the module if not, the method will return an empty string
     *
     * @return string hypertext links to edit and delete the folder
     * @see $is_smartmedia_admin
     */
    public function adminLinks()
    {
        global $is_smartmedia_admin;
        global $xoopsModule;
        $pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
        if ($is_smartmedia_admin) {
            $ret = '';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/folder.php?op=mod&folderid=' . $this->folderid() . '&categoryid=' . $this->categoryid() . '"><img src="' . $pathIcon16 . '/edit.png' . '"   alt="' . _MD_SMARTMEDIA_FOLDER_EDIT . '" title="' . _MD_SMARTMEDIA_FOLDER_EDIT . '"/></a>';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/folder.php?op=del&folderid=' . $this->folderid() . '&categoryid=' . $this->categoryid() . '"><img src="' . $pathIcon16 . '/delete.png' . '"  alt="' . _MD_SMARTMEDIA_FOLDER_DELETE . '" title="' . _MD_SMARTMEDIA_FOLDER_DELETE . '"/></a>';

            return $ret;
        } else {
            return '';
        }
    }

    /**
     * Format the folder information into an array
     *
     * This method puts each usefull informations of the folder into an array that will be used
     * in the modules template
     *
     * @return array array containing usfull informations of the folder
     */
    public function toArray()
    {
        $folder['folderid']   = $this->folderid();
        $folder['categoryid'] = $this->categoryid();
        $folder['itemurl']    = $this->getItemUrl();
        $folder['itemlink']   = $this->getItemLink();
        $folder['weight']     = $this->weight();

        if ('blank.png' !== $this->getVar('image_hr')) {
            $folder['image_hr_path'] = smartmedia_getImageDir('folder', false) . $this->image_hr();
        } else {
            $folder['image_hr_path'] = false;
        }

        $smartConfig                =& smartmedia_getModuleConfig();
        $folder['main_image_width'] = $smartConfig['main_image_width'];
        $folder['list_image_width'] = $smartConfig['list_image_width'];
        $folder['image_lr_path']    = smartmedia_getImageDir('folder', false) . $this->image_lr();
        $folder['counter']          = $this->counter();
        $folder['adminLinks']       = $this->adminLinks();

        $folder['title']            = $this->title();
        $folder['clean_title']      = $folder['title'];
        $folder['short_title']      = $this->title();
        $folder['summary']          = $this->summary();
        $folder['description']      = $this->description();
        $folder['meta_description'] = $this->meta_description();

        // Hightlighting searched words
        $highlight = true;
        if ($highlight && isset($_GET['keywords'])) {
            $myts                  = \MyTextSanitizer::getInstance();
            $keywords              = $myts->htmlSpecialChars(trim(urldecode($_GET['keywords'])));
            $h                     = new KeyHighlighter($keywords, true, 'smartmedia_highlighter');
            $folder['title']       = $h->highlight($folder['title']);
            $folder['summary']     = $h->highlight($folder['summary']);
            $folder['description'] = $h->highlight($folder['description']);
        }

        return $folder;
    }

    /**
     * Check to see if the folder has clips in it
     *
     * @return bool TRUE if the folder has clips, FALSE if not
     * @see Smartmedia\FolderHandler::clipsCount()
     */
    public function hasChild()
    {
        $smartmediaFolderHandler = Smartmedia\Helper::getInstance()->getHandler('Folder');
        $count                   = $smartmediaFolderHandler->clipsCount($this->folderid());
        if (isset($count[$this->folderid()]) && ($count[$this->folderid()] > 0)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the counter of the folder by one
     */
    public function updateCounter()
    {
        $this->setVar('counter', $this->counter() + 1);
        $this->store();
    }
}
