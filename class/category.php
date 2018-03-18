<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for managing categories
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: category.php,v 1.3 2005/06/02 13:33:37 malanciault Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage Categories
 */

use XoopsModules\Smartmedia;

/**
 * SmartMedia Category class
 *
 * Class representing a single category object
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class Category extends \XoopsObject
{
    /**
     * Language of the category
     * @var string
     */
    public $languageid;

    /**
     * {@link Smartmedia\CategoryText} object holding the category's text informations
     * @var object
     * @see Smartmedia\CategoryText
     */
    public $category_text = null;

    /**
     * List of all the translations already created for this category
     * @var array
     * @see getCreatedLanguages
     */
    public $_created_languages = null;

    /**
     * Flag indicating wheter or not a new translation can be added for this category
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
     * @param string  $languageid language of the category
     * @param integer $id         id of the category to be retreieved OR array containing values to be assigned
     */
    public function __construct($languageid = 'default', $id = null)
    {
        $smartConfig =& smartmedia_getModuleConfig();

        $this->initVar('categoryid', XOBJ_DTYPE_INT, -1, true);
        $this->initVar('parentid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('image', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('default_languageid', XOBJ_DTYPE_TXTBOX, $smartConfig['default_language'], false, 50);

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
     * Check if the category was successfully loaded
     *
     * @return bool true if not loaded, false if correctly loaded
     */
    public function notLoaded()
    {
        return ($this->categoryid() == -1);
    }

    /**
     * Loads the specified translation for this category
     *
     * If the specified language does not have any translation yet, the translation corresponding
     * to the default language will be loaded
     *
     * @param string $languageid language of the translation to load
     */
    public function loadLanguage($languageid)
    {
        $this->languageid              = $languageid;
        $smartmediaCategoryTextHandler = Smartmedia\Helper::getInstance()->getHandler('CategoryText');
        $this->category_text           = $smartmediaCategoryTextHandler->get($this->getVar('categoryid'), $this->languageid);

        if (!$this->category_text) {
            $this->category_text = new Smartmedia\CategoryText();
            $this->category_text->setVar('categoryid', $this->categoryid());
            $this->category_text->setVar('languageid', $languageid);

            $default_category_text = $smartmediaCategoryTextHandler->get($this->getVar('categoryid'), $this->default_languageid());

            if ($default_category_text) {
                //$this->category_text =& $default_category_text;
                $this->category_text->setVar('title', $default_category_text->title());
                $this->category_text->setVar('description', $default_category_text->description());
                $this->category_text->setVar('meta_description', $default_category_text->meta_description());
            }
        }
    }

    /**
     * @return int id of this category
     */
    public function categoryid()
    {
        return $this->getVar('categoryid');
    }

    /**
     * @return int id of parent of this category
     */
    public function parentid()
    {
        return $this->getVar('parentid');
    }

    /**
     * @return string weight of this category
     */
    public function weight()
    {
        return $this->getVar('weight');
    }

    /**
     * Returns the image of this category
     *
     * If no image has been set, the function will return blank.png, so a blank image can
     * be displayed
     *
     * @param  string $format format to use for the output
     * @return string low resolution image of this category
     */
    public function image($format = 'S')
    {
        if ('' != $this->getVar('image')) {
            return $this->getVar('image', $format);
        } else {
            return 'blank.png';
        }
    }

    /**
     * Returns the default language of the category
     *
     * When no translation corresponding to the selected language is available, the category's
     * information will be displayed in this language
     *
     * @param string $format
     * @return string default language of the category
     */
    public function default_languageid($format = 'S')
    {
        return $this->getVar('default_languageid', $format);
    }

    /**
     * Returns the title of the category
     *
     * If the format is "clean", the title will be return, striped from any html tag. This clean
     * title is likely to be used in the page title meta tag or any other place that requires
     * "html less" text
     *
     * @param  string $format format to use for the output
     * @return string title of the category
     */
    public function title($format = 'S')
    {
        $myts = \MyTextSanitizer::getInstance();
        if (('s' === strtolower($format)) || ('show' === strtolower($format))) {
            return $myts->undoHtmlSpecialChars($this->category_text->getVar('title', 'e'), 1);
        } elseif ('clean' === strtolower($format)) {
            return smartmedia_metagen_html2text($myts->undoHtmlSpecialChars($this->category_text->getVar('title')));
        } else {
            return $this->category_text->getVar('title', $format);
        }
    }

    /**
     * Returns the description of the category
     *
     * @param  string $format format to use for the output
     * @return string description of the category
     */
    public function description($format = 'S')
    {
        return $this->category_text->getVar('description', $format);
    }

    /**
     * Returns the meta_description of the category
     *
     * @param  string $format format to use for the output
     * @return string meta_description of the category
     */
    public function meta_description($format = 'S')
    {
        return $this->category_text->getVar('meta_description', $format);
    }

    /**
     * Set a text variable of the category
     *
     * @param string $key   of the variable to set
     * @param string $value of the field to set
     * @return
     * @see Smartmedia\CategoryText
     */
    public function setTextVar($key, $value)
    {
        return $this->category_text->setVar($key, $value);
    }

    /**
     * Get the complete URL of this category
     *
     * @return string complete URL of this category
     */
    public function getItemUrl()
    {
        return SMARTMEDIA_URL . 'category.php?categoryid=' . $this->categoryid();
    }

    /**
     * Get the complete hypertext link of this category
     *
     * @return string complete hypertext link of this category
     */
    public function getItemLink()
    {
        return "<a href='" . $this->getItemUrl() . "'>" . $this->title() . '</a>';
    }

    /**
     * Stores the category in the database
     *
     * This method stores the category as well as the current translation informations for the
     * category
     *
     * @param  bool $force
     * @return bool true if successfully stored false if an error occured
     *
     * @see Smartmedia\CategoryHandler::insert()
     * @see Smartmedia\CategoryText::store()
     */
    public function store($force = true)
    {
        global $smartmediaCategoryHandler;
        if (!$smartmediaCategoryHandler->insert($this, $force)) {
            return false;
        }
        $this->category_text->setVar('categoryid', $this->categoryid());

        return $this->category_text->store();
    }

    /**
     * Get all the translations created for this category
     *
     * @param  bool $exceptDefault to determine if the default language should be returned or not
     * @return array array of {@link Smartmedia\CategoryText}
     */
    public function getAllLanguages($exceptDefault = false)
    {
        global $smartmediaCategoryTextHandler;
        $criteria = new    \CriteriaCompo();
        $criteria->add(new \Criteria('categoryid', $this->categoryid()));
        if ($exceptDefault) {
            $criteria->add(new \Criteria('languageid', $this->default_languageid(), '<>'));
        }

        return $smartmediaCategoryTextHandler->getObjects($criteria);
    }

    /**
     * Get a list of created language
     *
     * @return array array containing the language name of the created translations for this category
     * @see _created_languages
     * @see Smartmedia\CategoryText::getCreatedLanguages()
     */
    public function getCreatedLanguages()
    {
        if (null != $this->_created_languages) {
            return $this->_created_languages;
        }
        global $smartmediaCategoryTextHandler;
        $this->_created_languages = $smartmediaCategoryTextHandler->getCreatedLanguages($this->categoryid());

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
     * Render the admin links for this category
     *
     * This method will create links to Edit and Delete the category. The method will also check
     * to ensure the user is admin of the module if not, the method will return an empty string
     *
     * @return string hypertext links to edit and delete the category
     * @see $is_smartmedia_admin
     */
    public function adminLinks()
    {
        global $is_smartmedia_admin;
        global $xoopsModule;

        $pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);

        if ($is_smartmedia_admin) {
            $ret = '';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/category.php?op=mod&categoryid=' . $this->categoryid() . '"><img src="' . $pathIcon16 . '/edit.png' . '"   alt="' . _MD_SMARTMEDIA_CATEGORY_EDIT . '" title="' . _MD_SMARTMEDIA_CATEGORY_EDIT . '"/></a>';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/category.php?op=del&categoryid=' . $this->categoryid() . '"><img src="' . $pathIcon16 . '/delete.png' . '"   alt="' . _MD_SMARTMEDIA_CATEGORY_DELETE . '" title="' . _MD_SMARTMEDIA_CATEGORY_DELETE . '"/></a>';

            return $ret;
        } else {
            return '';
        }
    }

    /**
     * Format the category information into an array
     *
     * This method puts each usefull informations of the category into an array that will be used in
     * the module template
     *
     * @return array array containing usfull informations of the clip
     */
    public function toArray()
    {
        $category['categoryid'] = $this->categoryid();
        $category['itemurl']    = $this->getItemUrl();
        $category['itemlink']   = $this->getItemLink();
        $category['parentid']   = $this->parentid();
        $category['weight']     = $this->weight();
        if ('blank.png' !== $this->getVar('image')) {
            $category['image_path'] = smartmedia_getImageDir('category', false) . $this->image();
        } else {
            $category['image_path'] = false;
        }
        $smartConfig                  = smartmedia_getModuleConfig();
        $category['main_image_width'] = $smartConfig['main_image_width'];
        $category['list_image_width'] = $smartConfig['list_image_width'];
        $category['adminLinks']       = $this->adminLinks();

        $category['title']            = $this->title();
        $category['clean_title']      = $category['title'];
        $category['description']      = $this->description();
        $category['meta_description'] = $this->meta_description();

        // Hightlighting searched words
        $highlight = true;
        if ($highlight && isset($_GET['keywords'])) {
            $myts                    = \MyTextSanitizer::getInstance();
            $keywords                = $myts->htmlSpecialChars(trim(urldecode($_GET['keywords'])));
            $h                       = new KeyHighlighter($keywords, true, 'smartmedia_highlighter');
            $category['title']       = $h->highlight($category['title']);
            $category['description'] = $h->highlight($category['description']);
        }

        return $category;
    }

    /**
     * Check to see if the category has folders in it
     *
     * @return bool TRUE if the category has folders, FALSE if not
     * @see Smartmedia\FolderHandler::getCountsByParent()
     */
    public function hasChild()
    {
        $smartmediaFolderHandler = Smartmedia\Helper::getInstance()->getHandler('Folder');
        $count                   = $smartmediaFolderHandler->getCountsByParent($this->categoryid());
        if (isset($count[$this->categoryid()]) && ($count[$this->categoryid()] > 0)) {
            return true;
        } else {
            return false;
        }
    }
}
