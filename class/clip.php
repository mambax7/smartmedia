<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for managing clips
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: clip.php,v 1.6 2005/06/02 19:50:59 fx2024 Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage Clips
 */

use XoopsModules\Smartmedia;

///** Status of an offline clip */
//define('_SMARTMEDIA_CLIP_STATUS_OFFLINE', 1);
///** Status of an online clip */
//define('_SMARTMEDIA_CLIP_STATUS_ONLINE', 2);

/**
 * SmartMedia Clip class
 *
 * Class representing a single clip object
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class Clip extends \XoopsObject
{
    /**
     * Language of the clip
     * @var string
     */
    public $languageid;

    /**
     * {@link Smartmedia\ClipText} object holding the clip's text informations
     * @var object
     * @see Smartmedia\ClipText
     */
    public $clip_text = null;

    /**
     * List of all the translations already created for this clip
     * @var array
     * @see getCreatedLanguages
     */
    public $_created_languages = null;

    /**
     * Flag indicating wheter or not a new translation can be added for this clip
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
     * @param string $languageid language of the clip
     * @param int    $id         id of the clip to be retreieved OR array containing values to be assigned
     */
    public function __construct($languageid = 'default', $id = null)
    {
        $smartConfig =& smartmedia_getModuleConfig();

        $this->initVar('clipid', XOBJ_DTYPE_INT, -1, true);
        $this->initVar('languageid', XOBJ_DTYPE_TXTBOX, $smartConfig['default_language'], false);
        $this->initVar('folderid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('statusid', XOBJ_DTYPE_INT, _SMARTMEDIA_CLIP_STATUS_ONLINE, false);
        $this->initVar('created_date', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('created_uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('modified_date', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('modified_uid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('duration', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('formatid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('width', XOBJ_DTYPE_INT, 320, false);
        $this->initVar('height', XOBJ_DTYPE_INT, 260, false);
        $this->initVar('autostart', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('image_hr', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('counter', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('image_lr', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('file_hr', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('file_lr', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('default_languageid', XOBJ_DTYPE_TXTBOX, $smartConfig['default_language'], false, 50);

        $this->initVar('folderid', XOBJ_DTYPE_INT, 0, false);

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
     * Check if the clip was successfully loaded
     *
     * @return bool true if not loaded, false if correctly loaded
     */
    public function notLoaded()
    {
        return (-1 == $this->clipid());
    }

    /**
     * Loads the specified translation for this clip
     *
     * If the specified language does not have any translation yet, the translation corresponding
     * to the default language will be loaded
     *
     * @param string $languageid language of the translation to load
     */
    public function loadLanguage($languageid)
    {
        $this->languageid          = $languageid;
        $smartmediaClipTextHandler = Smartmedia\Helper::getInstance()->getHandler('ClipText');
        $this->clip_text           =& $smartmediaClipTextHandler->get($this->getVar('clipid'), $this->languageid);
        if (!$this->clip_text) {
            $this->clip_text = new Smartmedia\ClipText();
            $this->clip_text->setVar('clipid', $this->clipid());
            $this->clip_text->setVar('languageid', $languageid);

            $default_clip_text =& $smartmediaClipTextHandler->get($this->getVar('clipid'), $this->default_languageid());

            if ($default_clip_text) {
                //$this->clip_text =& $default_clip_text;
                $this->clip_text->setVar('title', $default_clip_text->title());
                $this->clip_text->setVar('description', $default_clip_text->description());
                $this->clip_text->setVar('meta_description', $default_clip_text->meta_description());
                $this->clip_text->setVar('tab_caption_1', $default_clip_text->tab_caption_1());
                $this->clip_text->setVar('tab_text_1', $default_clip_text->tab_text_1());
                $this->clip_text->setVar('tab_caption_2', $default_clip_text->tab_caption_2());
                $this->clip_text->setVar('tab_text_2', $default_clip_text->tab_text_2());
                $this->clip_text->setVar('tab_caption_3', $default_clip_text->tab_caption_3());
                $this->clip_text->setVar('tab_text_3', $default_clip_text->tab_text_3());
            }
        }
    }

    /**
     * @return int id of this clip
     */
    public function clipid()
    {
        return $this->getVar('clipid');
    }

    /**
     * @return string spoken language of this clip
     */
    public function languageid()
    {
        return $this->getVar('languageid', $format);
    }

    /**
     * @return int duration in minutes of this clip
     */
    public function duration()
    {
        return $this->getVar('duration');
    }

    /**
     * @return int format id of this clip
     * @see Smartmedia\Format
     */
    public function formatid()
    {
        return $this->getVar('formatid');
    }

    /**
     * @return int width of this clip
     */
    public function width()
    {
        return (0 == $this->getVar('width') ? 320 : $this->getVar('width'));
    }

    /**
     * @return int height of this clip
     */
    public function height()
    {
        return (0 == $this->getVar('height') ? 260 : $this->getVar('height'));
    }

    /**
     * Returns the high resolution image of this clip
     *
     * If no image has been set, the function will return blank.png, so a blank image can
     * be displayed
     *
     * @param  string $format format to use for the output
     * @return string high resolution image of this clip
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
     * Returns the low resolution image of this clip
     *
     * If no image has been set, the function will return blank.png, so a blank image can
     * be displayed
     *
     * @param  string $format format to use for the output
     * @return string low resolution image of this clip
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
     * @param  string $format format to use for the output
     * @return string high resolution file of the clip
     */
    public function file_hr($format = 'S')
    {
        return $this->getVar('file_hr', $format);
    }

    /**
     * @param  string $format format to use for the output
     * @return string low resolution file of the clip
     */
    public function file_lr($format = 'S')
    {
        return $this->getVar('file_lr', $format);
    }

    /**
     * @return string weight of this clip
     */
    public function weight()
    {
        return $this->getVar('weight');
    }

    /**
     * @return int counter of this clip
     */
    public function counter()
    {
        return $this->getVar('counter');
    }

    /**
     * @return string wether or not the clip shall start automatically
     */
    public function autostart()
    {
        return $this->getVar('autostart');
    }

    /**
     * Returns the folderid to which this clip belongs
     * @see Smartmedia\Folder
     * @return string parent folderid of this clip
     */
    public function folderid()
    {
        return $this->getVar('folderid');
    }

    /**
     * Returns the status of the clip
     *
     * Status can be {@link _SMARTMEDIA_CLIP_STATUS_OFFLINE} or {@link _SMARTMEDIA_CLIP_STATUS_ONLINE}
     * @return string status of the clip
     */
    public function statusid()
    {
        return $this->getVar('statusid');
    }

    /**
     * Returns the date of creation of the clip
     *
     * The date will be formated according to the date format preference of the module
     * @return string date of creation of the clip
     */
    public function created_date()
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
     * @return int uid of the user who created the clip
     */
    public function created_uid()
    {
        return $this->getVar('created_uid');
    }

    /**
     * Returns the date of modification of the clip
     *
     * The date will be formated according to the date format preference of the module
     * @return string date of modification of the clip
     */
    public function modified_date()
    {
        $ret = $this->getVar('modified_date');
        $ret = formatTimestamp($ret, 'Y-m-d');

        return $ret;
    }

    /**
     * @return int uid of the user who modified the clip
     */
    public function modified_uid()
    {
        return $this->getVar('modified_uid');
    }

    /**
     * Returns the default language of the clip
     *
     * When no translation corresponding to the selected language is available, the clip's
     * information will be displayed in this language
     *
     * @param string $format
     * @return string default language of the clip
     */
    public function default_languageid($format = 'S')
    {
        return $this->getVar('default_languageid', $format);
    }

    /**
     * Returns the title of the clip
     *
     * If the format is "clean", the title will be return, striped from any html tag. This clean
     * title is likely to be used in the page title meta tag or any other place that requires
     * "html less" text
     *
     * @param  string $format format to use for the output
     * @return string title of the clip
     */
    public function title($format = 'S')
    {
        $myts = \MyTextSanitizer::getInstance();
        if (('s' === strtolower($format)) || ('show' === strtolower($format))) {
            return $myts->undoHtmlSpecialChars($this->clip_text->getVar('title', 'e'), 1);
        } elseif ('clean' === strtolower($format)) {
            return smartmedia_metagen_html2text($myts->undoHtmlSpecialChars($this->clip_text->getVar('title')));
        } else {
            return $this->clip_text->getVar('title', $format);
        }
    }

    /**
     * Returns the description of the clip
     *
     * @param  string $format format to use for the output
     * @return string description of the clip
     */
    public function description($format = 'S')
    {
        return $this->clip_text->getVar('description', $format);
    }

    /**
     * Returns the meta-description of the clip
     *
     * @param  string $format format to use for the output
     * @return string meta-description of the clip
     */
    public function meta_description($format = 'S')
    {
        return $this->clip_text->getVar('meta_description', $format);
    }

    /**
     * Returns the caption of the first tab for this clip
     *
     * Note that for the first tab to be displayed, the field {@link tab_text_1} needs to
     * ne not empty
     *
     * @param  string $format format to use for the output
     * @return string caption of the first tab of the clip
     */
    public function tab_caption_1($format = 'S')
    {
        return $this->clip_text->getVar('tab_caption_1', $format);
    }

    /**
     * Returns the text of the first tab for this clip
     *
     * Note that for the first tab to be displayed, this field needs to  ne not empty
     *
     * @param  string $format format to use for the output
     * @return string text of the first tab of the clip
     */
    public function tab_text_1($format = 'S')
    {
        return $this->clip_text->getVar('tab_text_1', $format);
    }

    /**
     * Returns the caption of the second tab for this clip
     *
     * Note that for the first tab to be displayed, the field {@link tab_text_2} needs to
     * ne not empty
     *
     * @param  string $format format to use for the output
     * @return string caption of the second tab of the clip
     */
    public function tab_caption_2($format = 'S')
    {
        return $this->clip_text->getVar('tab_caption_2', $format);
    }

    /**
     * Returns the text of the second tab for this clip
     *
     * Note that for the first tab to be displayed, this field needs to  ne not empty
     *
     * @param  string $format format to use for the output
     * @return string text of the second tab of the clip
     */
    public function tab_text_2($format = 'S')
    {
        return $this->clip_text->getVar('tab_text_2', $format);
    }

    /**
     * Returns the caption of the third tab for this clip
     *
     * Note that for the first tab to be displayed, the field {@link tab_text_2} needs to
     * ne not empty
     *
     * @param  string $format format to use for the output
     * @return string caption of the third tab of the clip
     */
    public function tab_caption_3($format = 'S')
    {
        return $this->clip_text->getVar('tab_caption_3', $format);
    }

    /**
     * Returns the text of the third tab for this clip
     *
     * Note that for the first tab to be displayed, this field needs to  ne not empty
     *
     * @param  string $format format to use for the output
     * @return string text of the third tab of the clip
     */
    public function tab_text_3($format = 'S')
    {
        return $this->clip_text->getVar('tab_text_3', $format);
    }

    /**
     * Set a text variable of the clip
     *
     * @param string $key   of the variable to set
     * @param string $value of the field to set
     * @see Smartmedia\ClipText
     */
    public function setTextVar($key, $value)
    {
        $this->clip_text->setVar($key, $value);
    }

    /**
     * Get the complete URL of this clip
     *
     * @param  int $categoryid category to which belong the parent folder of the clip
     * @return string complete URL of this clip
     */
    public function getItemUrl($categoryid)
    {
        return SMARTMEDIA_URL . 'clip.php?categoryid=' . $categoryid . '&folderid=' . $this->folderid() . '&clipid=' . $this->clipid();
    }

    /**
     * Get the complete hypertext link of this clip
     *
     * @param  int $categoryid       category to which belong the parent folder of the clip
     * @param  int $max_title_length maximum characters allowes in the title
     * @return string complete hypertext link of this clip
     */
    public function getItemLink($categoryid, $max_title_length = 0)
    {
        if ($max_title_length > 0) {
            $title = xoops_substr($this->title(), 0, $max_title_length);
        } else {
            $title = $this->title();
        }

        return "<a href='" . $this->getItemUrl($categoryid) . "'>" . $title . '</a>';
    }

    /**
     * Stores the clip in the database
     *
     * This method stores the clip as well as the current translation informations for the
     * clip
     *
     * @param  bool $force
     * @return bool true if successfully stored false if an error occured
     *
     * @see SmartmediaClipHandler::insert()
     * @see Smartmedia\ClipText::store()
     */
    public function store($force = true)
    {
        global $smartmediaClipHandler;
        if (!$smartmediaClipHandler->insert($this, $force)) {
            return false;
        }
        $this->clip_text->setVar('clipid', $this->clipid());

        return $this->clip_text->store();
    }

    /**
     * Get all the translations created for this clip
     *
     * @param  bool $exceptDefault to determine if the default language should be returned or not
     * @return array array of {@link Smartmedia\ClipText}
     */
    public function getAllLanguages($exceptDefault = false)
    {
        global $smartmediaClipTextHandler;
        $criteria = new    \CriteriaCompo();
        $criteria->add(new \Criteria('clipid', $this->clipid()));
        if ($exceptDefault) {
            $criteria->add(new \Criteria('languageid', $this->default_languageid(), '<>'));
        }

        return $smartmediaClipTextHandler->getObjects($criteria);
    }

    /**
     * Get a list of created language
     *
     * @return array array containing the language name of the created translations for this clip
     * @see _created_languages
     * @see Smartmedia\ClipText::getCreatedLanguages()
     */
    public function getCreatedLanguages()
    {
        if (null != $this->_created_languages) {
            return $this->_created_languages;
        }
        global $smartmediaClipTextHandler;
        $this->_created_languages = $smartmediaClipTextHandler->getCreatedLanguages($this->clipid());

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
     * Render the admin links for this clip
     *
     * This method will create links to Edit and Delete the clip. The method will also check
     * to ensure the user is admin of the module if not, the method will return an empty string
     *
     * @return string hypertext links to edit and delete the clip
     * @see $is_smartmedia_admin
     */
    public function adminLinks()
    {
        global $is_smartmedia_admin;
        global $xoopsModule;
        $pathIcon16 = \Xmf\Module\Admin::iconUrl('', 16);
        if ($is_smartmedia_admin) {
            $ret = '';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/clip.php?op=mod&clipid=' . $this->clipid() . '&folderid=' . $this->folderid() . '"><img src="' . $pathIcon16 . '/edit.png' . '" alt="' . _MD_SMARTMEDIA_CLIP_EDIT . '" title="' . _MD_SMARTMEDIA_CLIP_EDIT . '" /></a>';
            $ret .= '<a href="' . SMARTMEDIA_URL . 'admin/clip.php?op=del&clipid=' . $this->clipid() . '&folderid=' . $this->folderid() . '"><img src="' . $pathIcon16 . '/delete.png' . '" alt="' . _MD_SMARTMEDIA_CLIP_DELETE . '" title="' . _MD_SMARTMEDIA_CLIP_DELETE . '" /></a>';

            return $ret;
        } else {
            return '';
        }
    }

    /**
     * Render the template of the clip's format
     *
     * This method creates the format object associated to the clip's format and then renders
     * the template of the format
     *
     * @return string template of the clip's format
     * @see Smartmedia\Format
     * @see Smartmedia\Format::render()
     */
    public function renderTemplate()
    {
        $smartmediaFormatHandler = Smartmedia\Helper::getInstance()->getHandler('Format');
        $formatObj               = $smartmediaFormatHandler->get($this->formatid());

        return $formatObj->render($this);
    }

    /**
     * Format the clip information into an array
     *
     * This method puts each useful informations of the clip into an array that will be used in
     * the module template
     *
     * @param  int $categoryid category to which belong the parent folder of the clip
     * @param int  $max_title_length
     * @param bool $forBlock
     * @return array array containing usfull informations of the clip
     */
    public function toArray2($categoryid, $max_title_length = 0, $forBlock = false)
    {
        $clip['clipid']   = $this->clipid();
        $clip['itemurl']  = $this->getItemUrl($categoryid);
        $clip['itemlink'] = $this->getItemLink($categoryid, $max_title_length);

        if ($forBlock) {
            return $clip;
        }

        $clip['duration'] = $this->duration();

        $clip['template'] = $this->renderTemplate();

        $clip['width']  = $this->width();
        $clip['height'] = $this->height();

        $clip['weight']    = $this->weight();
        $clip['counter']   = $this->counter();
        $clip['statusid']  = $this->statusid();
        $clip['autostart'] = $this->autostart();
        $clip['counter']   = $this->counter();

        if ('blank.png' !== $this->getVar('image_hr')) {
            $clip['image_hr_path'] = smartmedia_getImageDir('clip', false) . $this->image_hr();
        } else {
            $clip['image_hr_path'] = false;
        }

        $smartConfig              =& smartmedia_getModuleConfig();
        $clip['main_image_width'] = $smartConfig['main_image_width'];
        $clip['list_image_width'] = $smartConfig['list_image_width'];

        $clip['image_lr_path'] = smartmedia_getImageDir('clip', false) . $this->image_lr();
        $clip['file_hr_path']  = $this->file_hr();
        $clip['file_lr_path']  = $this->file_lr();

        $clip['file_hr_link'] = "<a href='" . $this->file_hr() . "' target='_blank'>" . _MD_SMARTMEDIA_HIGH_RES_CLIP . '</a>';

        $clip['adminLinks'] = $this->adminLinks();

        $clip['title']            = $this->title();
        $clip['clean_title']      = $clip['title'];
        $clip['description']      = $this->description();
        $clip['meta_description'] = $this->meta_description();

        $clip['tab_caption_1'] = $this->tab_caption_1();
        $clip['tab_text_1']    = $this->tab_text_1();
        $clip['tab_caption_2'] = $this->tab_caption_2();
        $clip['tab_text_2']    = $this->tab_text_2();
        $clip['tab_caption_3'] = $this->tab_caption_3();
        $clip['tab_text_3']    = $this->tab_text_3();

        // Hightlighting searched words
        $highlight = true;
        if ($highlight && isset($_GET['keywords'])) {
            $myts                  = \MyTextSanitizer::getInstance();
            $keywords              = $myts->htmlSpecialChars(trim(urldecode($_GET['keywords'])));
            $h                     = new KeyHighlighter($keywords, true, 'smartmedia_highlighter');
            $clip['title']         = $h->highlight($clip['title']);
            $clip['description']   = $h->highlight($clip['description']);
            $clip['tab_caption_1'] = $h->highlight($clip['tab_caption_1']);
            $clip['tab_text_1']    = $h->highlight($clip['tab_text_1']);
            $clip['tab_caption_2'] = $h->highlight($clip['tab_caption_2']);
            $clip['tab_text_2']    = $h->highlight($clip['tab_text_2']);
            $clip['tab_caption_3'] = $h->highlight($clip['tab_caption_3']);
            $clip['tab_text_3']    = $h->highlight($clip['tab_text_3']);
        }

        return $clip;
    }

    /**
     * Update the counter of the clip by one
     */
    public function updateCounter()
    {
        $this->setVar('counter', $this->counter() + 1);
        $this->store();
    }
}
