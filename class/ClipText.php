<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for managing clips translations
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: clip_text.php,v 1.3 2005/06/02 13:33:37 malanciault Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage Clips
 */

use XoopsModules\Smartmedia;

/**
 * SmartMedia Clip_text class
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class ClipText extends \XoopsObject
{
    /**
     * Constructor
     *
     * @param int $id id of the clip to be retreieved OR array containing values to be assigned
     */
    public function __construct($id = null)
    {
        $smartConfig =& smartmedia_getModuleConfig();

        $this->initVar('clipid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('languageid', XOBJ_DTYPE_TXTBOX, $smartConfig['default_language'], true);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('meta_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('tab_caption_1', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('tab_text_1', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('tab_caption_2', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('tab_text_2', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('tab_caption_3', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('tab_text_3', XOBJ_DTYPE_TXTAREA, null, false);

        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doxcode', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('dosmiley', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('doimage', XOBJ_DTYPE_INT, 0, false);

        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            }
        } else {
            $this->setNew();
        }
    }

    /**
     * @param string $format
     * @return string clipid of this translation
     */
    public function clipid($format = 'S')
    {
        return $this->getVar('clipid', $format);
    }

    /**
     * @param string $format
     * @return string language of this translation
     */
    public function languageid($format = 'S')
    {
        return $this->getVar('languageid', $format);
    }

    /**
     * @param string $format
     * @return string title of this clip
     */
    public function title($format = 'S')
    {
        return $this->getVar('title', $format);
    }

    /**
     * @param string $format
     * @return string title of this clip
     */
    public function description($format = 'S')
    {
        return $this->getVar('description', $format);
    }

    /**
     * @param string $format
     * @return string meta_description of this clip
     */
    public function meta_description($format = 'S')
    {
        return $this->getVar('meta_description', $format);
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
        return $this->getVar('tab_caption_1', $format);
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
        return $this->getVar('tab_text_1', $format);
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
        return $this->getVar('tab_caption_2', $format);
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
        return $this->getVar('tab_text_2', $format);
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
        return $this->getVar('tab_caption_3', $format);
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
        return $this->getVar('tab_text_3', $format);
    }

    /**
     * Stores the clip's translation in the database
     *
     * @param  bool $force
     * @return bool true if successfully stored false if an error occured
     * @see Smartmedia\ClipTextHandler::insert()
     */
    public function store($force = true)
    {
        global $smartmediaClipTextHandler;

        return $smartmediaClipTextHandler->insert($this, $force);
    }
}
