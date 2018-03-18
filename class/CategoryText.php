<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for managing categories translations
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: category_text.php,v 1.3 2005/06/02 13:33:37 malanciault Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage Categories
 */

use XoopsModules\Smartmedia;

/**
 * SmartMedia Category_text class
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class CategoryText extends \XoopsObject
{
    /**
     * Constructor
     *
     * @param array $id array containing values to be assigned
     */
    public function __construct($id = null)
    {
        $smartConfig =& smartmedia_getModuleConfig();

        $this->initVar('categoryid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('languageid', XOBJ_DTYPE_TXTBOX, $smartConfig['default_language'], true);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('meta_description', XOBJ_DTYPE_TXTAREA, null, false);

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
     * @return string categoryid of this translation
     */
    public function categoryid($format = 'S')
    {
        return $this->getVar('categoryid', $format);
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
     * @return string title of this category
     */
    public function title($format = 'S')
    {
        return $this->getVar('title', $format);
    }

    /**
     * @param string $format
     * @return description title of this clip
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
     * Stores the category's translation in the database
     *
     * @param  bool $force
     * @return bool true if successfully stored false if an error occured
     * @see Smartmedia\CategoryTextHandler::insert()
     */
    public function store($force = true)
    {
        global $smartmediaCategoryTextHandler;

        return $smartmediaCategoryTextHandler->insert($this, $force);
    }
}
