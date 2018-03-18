<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for managing folders translations
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: folder_text.php,v 1.3 2005/06/02 13:33:37 malanciault Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage Folders
 */

use XoopsModules\Smartmedia;

/**
 * SmartMedia Folder_text class
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class FolderText extends \XoopsObject
{
    /**
     * Constructor
     *
     * @param int $id id of the folder to be retreieved OR array containing values to be assigned
     */
    public function __construct($id = null)
    {
        $smartConfig =& smartmedia_getModuleConfig();

        $this->initVar('folderid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('languageid', XOBJ_DTYPE_TXTBOX, $smartConfig['default_language'], true);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->initVar('short_title', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('summary', XOBJ_DTYPE_TXTAREA, null, false);
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
     * @return string folderid of this translation
     */
    public function folderid($format = 'S')
    {
        return $this->getVar('folderid', $format);
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
     * @return string title of this folder
     */
    public function title($format = 'S')
    {
        return $this->getVar('title', $format);
    }

    /**
     * @param string $format
     * @return string short_title of this folder
     */
    public function short_title($format = 'S')
    {
        return $this->getVar('short_title', $format);
    }

    /**
     * @param string $format
     * @return string summary of this folder
     */
    public function summary($format = 'S')
    {
        return $this->getVar('summary', $format);
    }

    /**
     * @param string $format
     * @return string description of this folder
     */
    public function description($format = 'S')
    {
        return $this->getVar('description', $format);
    }

    /**
     * @param string $format
     * @return string meta_description of this folder
     */
    public function meta_description($format = 'S')
    {
        return $this->getVar('meta_description', $format);
    }

    /**
     * Stores the format's translation in the database
     *
     * @param  bool $force
     * @return bool true if successfully stored false if an error occured
     * @see SmartmediaFormat_textHandler::insert()
     */
    public function store($force = true)
    {
        global $smartmediaFolderTextHandler;

        return $smartmediaFolderTextHandler->insert($this, $force);
    }
}
