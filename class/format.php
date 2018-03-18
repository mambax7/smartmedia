<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for managing clips formats
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: format.php,v 1.3 2005/06/02 13:33:37 malanciault Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage Clips
 */

use XoopsModules\Smartmedia;

defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * SmartMedia Format class
 *
 * Class representing a a clip's format object
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class Format extends \XoopsObject
{
    /**
     * Constructor
     *
     * @param int $id id of the clip to be retreieved OR array containing values to be assigned
     */
    public function __construct($id = null)
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('formatid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('template', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('format', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('ext', XOBJ_DTYPE_TXTBOX, null, false);

        if (isset($id)) {
            $objHandler = new Smartmedia\FormatHandler($this->db);
            $obj        = $objHandler->get($id);
            foreach ($obj->vars as $k => $v) {
                $this->assignVar($k, $v['value']);
            }
        }
    }

    /**
     * @return int id of this format
     */
    public function formatid()
    {
        return $this->getVar('formatid');
    }

    /**
     * @return string template of the format
     */
    public function template()
    {
        $ret = $this->getVar('template', 'none');

        return $ret;
    }

    /**
     * @return string name of the format
     */
    public function format()
    {
        return $this->getVar('format');
    }

    /**
     * @return string extension reprsenting this format
     */
    public function ext()
    {
        return $this->getVar('ext');
    }

    /**
     * Stores the format of the clip in the database
     *
     * @param  bool $force
     * @return bool true if successfully stored false if an error occured
     *
     * @see Smartmedia\FormatHandler::insert()
     */
    public function store($force = true)
    {
        $formatHandler = new Smartmedia\FormatHandler($this->db);

        return $formatHandler->insert($this, $force);
    }

    /**
     * Get redirection messages
     *
     * This method returns the redirection messages upon success of delete of addition,
     * edition or deletion of a format
     *
     * @param  string $action action related to the messages : new, edit or delete
     * @return array  containing the messages
     */
    public function getRedirectMsg($action)
    {
        $ret = [];
        if ('new' === $action) {
            $ret['error']   = _AM_SMARTMEDIA_FORMAT_CREATE_ERROR;
            $ret['success'] = _AM_SMARTMEDIA_FORMAT_CREATE_SUCCESS;
        } elseif ('edit' === $action) {
            $ret['error']   = _AM_SMARTMEDIA_FORMAT_EDIT_ERROR;
            $ret['success'] = _AM_SMARTMEDIA_FORMAT_EDIT_SUCCESS;
        } elseif ('delete' === $action) {
            $ret['error']   = _AM_SMARTMEDIA_FORMAT_DELETE_ERROR;
            $ret['success'] = _AM_SMARTMEDIA_FORMAT_DELETE_SUCCESS;
        }

        return $ret;
    }

    /**
     * Renders the template
     *
     * This methods renders the template ans replace different tags by the real values of the clip
     *
     * @param  object $clipObj {@link Smartmedia\Clip} object
     * @return string containing the template
     */
    public function render($clipObj)
    {
        $temp = $this->template();

        $temp = str_replace('{CLIP_URL}', $clipObj->file_lr(), $temp);
        $temp = str_replace('{CLIP_WIDTH}', $clipObj->width(), $temp);
        $temp = str_replace('{CLIP_HEIGHT}', $clipObj->height(), $temp);
        $temp = str_replace('{CLIP_URL}', $clipObj->autostart(), $temp);

        return $temp;
    }
}
