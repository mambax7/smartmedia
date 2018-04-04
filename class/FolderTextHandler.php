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

/**
 * Smartmedia Folder_text Handler class
 *
 * Folder Translations Handler responsible for handling {@link Smartmedia\FolderText} objects
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class FolderTextHandler extends \XoopsObjectHandler
{
    /**
     * Database connection
     *
     * @var object
     */
    public $db;

    /**
     * Name of child class
     *
     * @var string
     */
    public $classname = FolderText::class;

    /**
     * Related table name
     *
     * @var string
     */
    public $dbtable = 'smartmedia_folders_text';

    /**
     * Constructor
     *
     * @param object $db reference to a xoopsDB object
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Singleton - prevent multiple instances of this class
     *
     * @param  object &$db {@link XoopsHandlerFactory}
     * @return object Smartmedia\FolderTextHandler
     */
    public static function getInstance($db)
    {
        static $instance;
        if (null === $instance) {
            $instance = new static($db);
        }

        return $instance;
    }

    /**
     * Creates a new folder's translation object
     * @return object Smartmedia\FolderText
     */
    public function create()
    {
        return new $this->classname();
//        $temp = '\\XoopsModules\\Smartmedia\\' . $this->classname;
//        return new $temp;
    }

    /**
     * Retrieve a folder translation object from the database
     *
     * If no $languageid is specified, the default_language set in the module's preference
     * will be used
     *
     * @param         $folderid
     * @param  string $languageid language of the translation to retreive
     * @return bool Smartmedia\FolderText
     */
    public function &get($folderid, $languageid = 'none')
    {
        if ('none' === $languageid) {
            $smartConfig =& smartmedia_getModuleConfig();
            $languageid  = $smartConfig['default_language'];
        }

        $folderid = (int)$folderid;
        if ($folderid > 0) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('folderid', $folderid));
            $criteria->add(new \Criteria('languageid', $languageid));
            $sql = $this->_selectQuery($criteria);

            //echo "<br> $sql <br>";

            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                $obj = new $this->classname($this->db->fetchArray($result));

                return $obj;
            }
        }

        return $false;
    }

    /**
     * Create a "select" SQL query
     * @param  object $criteria {@link \CriteriaElement} to match
     * @return string SQL query
     */
    public function _selectQuery($criteria = null)
    {
        $sql = sprintf('SELECT * FROM `%s`', $this->db->prefix($this->dbtable));
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . '
                    ' . $criteria->getOrder();
            }
        }

        return $sql;
    }

    /**
     * Count objects matching a criteria
     *
     * @param  object $criteria {@link \CriteriaElement} to match
     * @return int    count of objects
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix($this->dbtable);
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Retrieve objects from the database
     *
     * @param  object $criteria  {@link \CriteriaElement} conditions to be met
     * @param  bool   $id_as_key Should the folder ID be used as array key
     * @return array  array of {@link Smartmedia\FolderText} objects
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret   = [];
        $limit = $start = 0;
        $sql   = $this->_selectQuery($criteria);
        if (isset($criteria)) {
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        $result = $this->db->query($sql, $limit, $start);
        // If no records from db, return empty array
        if (!$result) {
            return $ret;
        }

        // Add each returned record to the result array
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $obj = new $this->classname($myrow);
            if (!$id_as_key) {
                $ret[] =& $obj;
            } else {
                $ret[$obj->getVar('id')] =& $obj;
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * Get a list of created language
     *
     * @param $folderid
     * @return array array containing the language name of the created translations for $folderid
     * @see Smartmedia\Folder::getCreatedLanguages()
     */
    public function getCreatedLanguages($folderid)
    {
        $ret = [];
        $sql = sprintf('SELECT languageid FROM `%s`', $this->db->prefix($this->dbtable));
        $sql .= " WHERE folderid = $folderid";

        //  echo "<br>$sql<br>";

        $result = $this->db->query($sql);
        // If no records from db, return empty array
        if (!$result) {
            return $ret;
        }

        // Add each returned record to the result array
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[] =& $myrow['languageid'];
        }

        return $ret;
    }

    /**
     * Stores a folder in the database
     *
     * @param \XoopsObject $object
     * @param  bool        $force
     * @return bool   FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $object, $force = false)
    {
        // Make sure object is of correct type
        if (!is_a($object, $this->classname)) {
            return false;
        }

        // Make sure object needs to be stored in DB
        if (!$object->isDirty()) {
            return true;
        }

        // Make sure object fields are filled with valid values
        if (!$object->cleanVars()) {
            return false;
        }

        // Copy all object vars into local variables
        foreach ($object->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        // Create query for DB update
        if ($object->isNew()) {
            $sql = sprintf('INSERT INTO %s (
            folderid,
            languageid,
            title,
            short_title,
            summary,
            description,
            meta_description)
            VALUES (
            %u,
            %s,
            %s,
            %s,
            %s,
            %s,
            %s)', $this->db->prefix($this->dbtable), $folderid, $this->db->quoteString($languageid), $this->db->quoteString($title), $this->db->quoteString($short_title), $this->db->quoteString($summary), $this->db->quoteString($description), $this->db->quoteString($meta_description));
        } else {
            $sql = sprintf(
                'UPDATE %s SET
            title = %s,
            short_title = %s,
            summary = %s,
            description = %s,
            meta_description = %s
            WHERE folderid = %u AND languageid = %s',
                $this->db->prefix($this->dbtable),
                $this->db->quoteString($title),
                $this->db->quoteString($short_title),
                $this->db->quoteString($summary),
                $this->db->quoteString($description),
                $this->db->quoteString($meta_description),
                $folderid,
                           $this->db->quoteString($languageid)
            );
        }
        //echo "<br>" . $sql . "<br>";

        // Update DB
        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * Deletes a folder translation from the database
     *
     * @param \XoopsObject $object
     * @param  bool        $force
     * @return bool   FALSE if failed.
     */
    public function delete(\XoopsObject $object, $force = true)
    {
        if (strtolower(get_class($obj)) != $this->classname) {
            return false;
        }

        $sql = sprintf('DELETE FROM `%s` WHERE folderid = %u AND languageid = %s', $this->db->prefix($this->dbtable), $obj->getVar('folderid'), $this->db->quoteString($obj->getVar('languageid')));

        //echo "<br>" . $sql . "<br>";

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }

        return true;
    }

    /**
     * Delete folders translations matching a set of conditions
     *
     * @param  object $criteria {@link \CriteriaElement}
     * @return bool   FALSE if deletion failed
     */
    public function deleteAll($criteria = null)
    {
        $sql = 'DELETE FROM ' . $this->db->prefix($this->dbtable);
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }
}
