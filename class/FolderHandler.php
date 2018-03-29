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

/** Status of an offline folder */
define('_SMARTMEDIA_FOLDER_STATUS_OFFLINE', 1);
/** Status of an online folder */
define('_SMARTMEDIA_FOLDER_STATUS_ONLINE', 2);


/**
 * Smartmedia Folder Handler class
 *
 * Folder Handler responsible for handling {@link Smartmedia\Folder} objects
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class FolderHandler extends \XoopsObjectHandler
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
    public $classname = Folder::class;

    /**
     * Related table name
     *
     * @var string
     */
    public $dbtable = 'smartmedia_folders';

    /**
     * Related parent table name
     *
     * @var string
     */
    public $dbtable_parent = 'smartmedia_folders_categories';

    /**
     * Related child table name
     *
     * @var string
     */
    public $dbtable_child = 'smartmedia_clips';

    /**
     * Parent field name
     *
     * @var string
     */
    public $_parent_field = 'categoryid';

    /**
     * Key field name
     *
     * @var string
     */
    public $_key_field = 'folderid';

    /**
     * Caption field name
     *
     * @var string
     */
    public $_caption_field = 'title';

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
     * @param  \XoopsDatabase $db {@link XoopsHandlerFactory}
     * @return Smartmedia\FolderHandler
     */
    public static function getInstance(\XoopsDatabase $db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new static($db);
        }

        return $instance;
    }

    /**
     * Creates a new folder object
     * @return Smartmedia\Folder
     */
    public function create()
    {
        $folder = new $this->classname();
        return $folder;
    }

    /**
     * Retrieve a folder object from the database
     *
     * If no languageid is specified, the method will load the translation related to the current
     * language selected by the user
     *
     * @param  int    $id         id of the folder
     * @param  string $languageid language of the translation to load
     * @return mixed  reference to the {@link Smartmedia\Folder} object, FALSE if failed
     */
    public function &get($id, $languageid = 'current')
    {
        $id = (int)$id;
        if ($id > 0) {
            $sql = $this->_selectQuery(new \Criteria('folderid', $id));

            //echo "<br>$sql<br/>";

            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                if ('current' === $languageid) {
                    global $xoopsConfig;
                    $languageid = $xoopsConfig['language'];
                }
                $obj = new $this->classname($languageid, $this->db->fetchArray($result));

                // Check to see if the categoryid is in the url
                if (isset($_GET['categoryid'])) {
                    $obj->setVar('categoryid', (int)$_GET['categoryid']);
                }

                return $obj;
            }
        }

        return false;
    }

    /**
     * Create a "SELECT" SQL query
     *
     * @param  object $criteria {@link \CriteriaElement} to match
     * @return string SQL query
     */
    public function _selectQuery($criteria = null)
    {
        $sql = sprintf('SELECT * FROM %s', $this->db->prefix($this->dbtable));
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
     * Creates a "SELECT" SQL query with INNER JOIN statement
     *
     * This methods builds a SELECT query joining the folders table to the folders_categories table.
     *
     * @param  int    $parentid id of the parent on which to join
     * @param  object $criteria {@link \CriteriaElement} to match
     * @return string SQL query
     */
    public function _selectJoinQuery($parentid, $criteria = null)
    {
        $sql = sprintf('SELECT * FROM %s AS parent INNER JOIN %s AS child ON parent.%s=child.%s', $this->db->prefix($this->dbtable_parent), $this->db->prefix($this->dbtable), $this->_key_field, $this->_key_field);
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            if (0 != $parentid) {
                $criteria->add(new \Criteria($this->_parent_field, $parentid));
            }

            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . '
                    ' . $criteria->getOrder();
            }
        } elseif (0 != $categoryid) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria($this->_parent_field, $parentid));
            $sql .= ' ' . $criteria->renderWhere();
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
     * Count folders belonging to a specific category
     *
     * If no categoryid is specified, the method will count all folders in the module
     *
     * @param  int $categoryid category in which to count folders
     * @return int count of objects
     */
    public function getfoldersCount($categoryid = 0)
    {
        $criteria = new \CriteriaCompo();
        if (isset($categoryid) && (0 != $categoryid)) {
            $criteria->add(new \Criteria('categoryid', $categoryid));

            return $this->getCount($criteria);
        } else {
            return $this->getCount();
        }
    }

    /**
     * Count the categories to which belongs a specific folder
     *
     * @param  int $folderid id of the folder
     * @return int count of objects
     */
    public function getParentCount($folderid)
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('folderid', $folderid));

        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix($this->dbtable_parent);
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        if (!$result = $this->db->query($sql)) {
            return -1;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Retrieve objects from the database
     *
     * @param  int    $categoryid         id of a category
     * @param  object $criteria           {@link \CriteriaElement} conditions to be met
     * @param  bool   $category_id_as_key Should the folder ID be used as array key
     * @return array  array of {@link Smartmedia\Folder} objects
     */
    public function &getObjects($categoryid, $criteria = null, $category_id_as_key = false)
    {
        global $xoopsConfig;

        $smartConfig =& smartmedia_getModuleConfig();

        $ret   = [];
        $limit = $start = 0;
        $sql   = $this->_selectJoinQuery($categoryid, $criteria);

        if (isset($criteria)) {
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        // echo "<br>$sql<br>";

        $result = $this->db->query($sql, $limit, $start);
        // If no records from db, return empty array
        if (!$result) {
            return $ret;
        }

        // Add each returned record to the result array
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $obj = new $this->classname($xoopsConfig['language'], $myrow);
            if (!$category_id_as_key) {
                $ret[$obj->getVar('folderid')] =& $obj;
            } else {
                $ret[$myrow['categoryid']][$obj->getVar('folderid')] =& $obj;
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * Get a list of {@link Smartmedia\Folder} objects for the search feature
     *
     * @param array  $queryarray list of keywords to look for
     * @param string $andor      specify which type of search we are performing : AND or OR
     * @param int    $limit      maximum number of results to return
     * @param int    $offset     at which folder shall we start
     * @param int    $userid     userid related to the creator of the folder
     *
     * @return array array containing information about the folders mathing the search criterias
     */
    public function &getObjectsForSearch($queryarray = [], $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        global $xoopsConfig;

        $ret = [];
        $sql = 'SELECT item.' . $this->_key_field . ', itemtext.' . $this->_caption_field . ', itemtext.description, parent.categoryid FROM
                   (
                     (' . $this->db->prefix($this->dbtable) . ' AS item
                       INNER JOIN ' . $this->db->prefix($this->dbtable) . '_text AS itemtext
                       ON item.' . $this->_key_field . ' = itemtext.' . $this->_key_field . '
                     )
                     INNER JOIN ' . $this->db->prefix($this->dbtable_parent) . ' AS parent
                      ON parent.' . $this->_key_field . ' = item.' . $this->_key_field . '
                   )';

        if (!empty($queryarray)) {
            $criteriaKeywords = new \CriteriaCompo();
            for ($i = 0; $i < count($queryarray); ++$i) {
                $criteriaKeyword = new \CriteriaCompo();
                $criteriaKeyword->add(new \Criteria('itemtext.title', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.description', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeywords->add($criteriaKeyword, $andor);
            }
        }

        if (0 != $userid) {
            $criteriaUser = new \CriteriaCompo();
            $criteriaUser->add(new \Criteria('item.uid', $userid), 'OR');
        }

        $criteria = new \CriteriaCompo();

        // Languageid
        $criteriaLanguage = new \CriteriaCompo();
        $criteriaLanguage->add(new \Criteria('itemtext.languageid', $xoopsConfig['language']));
        $criteria->add($criteriaLanguage);

        if (!empty($criteriaUser)) {
            $criteria->add($criteriaUser, 'AND');
        }

        if (!empty($criteriaKeywords)) {
            $criteria->add($criteriaKeywords, 'AND');
        }

        $criteria->setSort('item.weight');
        $criteria->setOrder('ASC');

        $sql .= ' ' . $criteria->renderWhere();

        //$sql .= "GROUP BY parent." . $this->_key_field . "";

        if ('' != $criteria->getSort()) {
            $sql .= ' ORDER BY ' . $criteria->getSort() . '
                ' . $criteria->getOrder();
        }

        //echo "<br>$sql<br>";

        $result = $this->db->query($sql, $limit, $offset);
        // If no records from db, return empty array
        if (!$result) {
            return $ret;
        }

        // Add each returned record to the result array
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $item['id']         = $myrow[$this->_key_field];
            $item['title']      = $myrow[$this->_caption_field];
            $item['categoryid'] = $myrow[$this->_parent_field];

            $ret[] = $item;
            unset($item);
        }

        return $ret;
    }

    /**
     * Get a list of {@link Smartmedia\Folder}
     *
     * @param int    $limit              maximum number of results to return
     * @param int    $start              at which folder shall we start
     * @param int    $categoryid         category to which belong the parent category of the clip
     * @param string $status
     * @param string $sort               sort parameter
     * @param string $order              order parameter
     * @param bool   $category_id_as_key wether or not the categoryid should be used as array key
     *
     * @return array array of {@link Smartmedia\Folder}
     */
    public function &getfolders($limit = 0, $start = 0, $categoryid, $status = '', $sort = 'weight', $order = 'ASC', $category_id_as_key = true)
    {
        $criteria = new \CriteriaCompo();

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        $criteria->setStart($start);
        $criteria->setLimit($limit);

        if ($status) {
            $criteria->add(new \Criteria('statusid', $status));
        }

        return $this->getObjects($categoryid, $criteria, $category_id_as_key);
    }

    /**
     * Get count of folders by category
     *
     * @param int    $parent_id category in which to count the folders
     *
     * @param string $status
     * @return array count of folders
     */
    public function getCountsByParent($parent_id = 0, $status = 'none')
    {
        $ret = [];
        $sql = 'SELECT parent.' . $this->_parent_field . ' AS parentid, COUNT( item.' . $this->_key_field . ' ) AS count
                FROM ' . $this->db->prefix($this->dbtable) . ' AS item
                INNER JOIN ' . $this->db->prefix($this->dbtable_parent) . ' AS parent ON item.' . $this->_key_field . ' = parent.' . $this->_key_field;

        if ((int)$parent_id > 0) {
            $sql .= ' WHERE ' . $this->_parent_field . ' = ' . (int)$parent_id;
            if ('none' !== $status) {
                $sql .= ' AND statusid = ' . $status;
            }
        } else {
            if ('none' !== $status) {
                $sql .= ' WHERE statusid = ' . $status;
            }
        }
        $sql .= ' GROUP BY parent.' . $this->_parent_field;

        //echo "<br>$sql<br>";

        $result = $this->db->query($sql);
        if (!$result) {
            return $ret;
        }
        while (false !== ($row = $this->db->fetchArray($result))) {
            $ret[$row['parentid']] = (int)$row['count'];
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
            // Determine next auto-gen ID for table
            $folderid = $this->db->genId($this->db->prefix($this->dbtable) . '_uid_seq');
            $sql      = sprintf('INSERT INTO %s (
            folderid,
            statusid,
            created_uid,
            created_date,
            modified_uid,
            modified_date,
            weight,
            image_lr,
            image_hr,
            counter,
            default_languageid)
            VALUES (
            %u,
            %u,
            %u,
            %u,
            %u,
            %u,
            %u,
            %u,
            %s,
            %s,
            %s)', $this->db->prefix($this->dbtable), $folderid, $statusid, $created_uid, $created_date, $modified_uid, $modified_date, $weight, $this->db->quoteString($image_lr), $this->db->quoteString($image_hr), $counter, $this->db->quoteString($default_languageid));
        } else {
            $sql = sprintf('UPDATE %s SET
            statusid = %u,
            created_uid = %u,
            created_date = %u,
            modified_uid = %u,
            modified_date = %u,
            weight = %u,
            image_lr = %s,
            image_hr = %s,
            counter = %u,
            default_languageid = %s
            WHERE folderid = %u', $this->db->prefix($this->dbtable), $statusid, $created_uid, $created_date, $modified_uid, $modified_date, $weight, $this->db->quoteString($image_lr), $this->db->quoteString($image_hr), $counter, $this->db->quoteString($default_languageid), $folderid);
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

        //Make sure auto-gen ID is stored correctly in object
        if (empty($folderid)) {
            $folderid = $this->db->getInsertId();
        }
        $object->assignVar('folderid', $folderid);

        return true;
    }

    /**
     * Link a folder to a category
     *
     * If $new is TRUE, a new link will be created, if not, the existing link will be updated
     *
     * @param  int  $parentid id of the category to link to
     * @param  int  $keyid    id of the folder to link
     * @param  bool $new      if it's a new link or not
     * @return true
     * @todo Make the method returns true if success, false if not. In order to this, the
     *                        receiving end of this method needs to be modified
     *
     * @see  Smartmedia\Folder::linkToParent()
     */
    public function linkToParent($parentid, $keyid, $new = true)
    {
        if ($new) {
            $sql = 'INSERT INTO ' . $this->db->prefix($this->dbtable_parent) . ' ( ' . $this->_parent_field . ', ' . $this->_key_field . " ) VALUES ( '$parentid', '$keyid' )";
        } else {
            $sql = 'UPDATE ' . $this->db->prefix($this->dbtable_parent) . ' SET ' . $this->_parent_field . "= '$parentid' WHERE " . $this->_key_field . "= '$keyid'";
        }

        //return $this->db->queryF($sql);
        $this->db->queryF($sql);

        return true;
    }

    /**
     * Deletes a folder from the database
     *
     * @param \XoopsObject $object
     * @param  bool        $force
     * @return bool   FALSE if failed.
     */
    public function delete(\XoopsObject $object, $force = false)
    {
        if (strtolower(get_class($obj)) != $this->classname) {
            return false;
        }

        $smartmediaFolderTextHandler = Smartmedia\Helper::getInstance()->getHandler('FolderText');
        $criteria                    = new \CriteriaCompo(new \Criteria('folderid', $obj->folderid()));
        if (!$smartmediaFolderTextHandler->deleteAll($criteria)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE folderid = %u', $this->db->prefix($this->dbtable), $obj->getVar('folderid'));

        //echo "<br>$sql</br />";

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
     * Deletes a link between a folder and a category
     *
     * The method will also check, after deleting the link, if other links to this folder still
     * exists. If not, the folder itself will also be deleted
     *
     * @param \XoopsObject $object
     * @param int          $parentid id of the category which link to delete
     * @param bool         $force
     *
     * @return bool FALSE if failed.
     */
    public function deleteParentLink(\XoopsObject $object, $parentid, $force = false)
    {
        if (strtolower(get_class($obj)) != $this->classname) {
            return false;
        }

        // Delete parent link
        $sql = sprintf('DELETE FROM %s WHERE folderid = %u AND categoryid = %u', $this->db->prefix($this->dbtable_parent), $obj->getVar('folderid'), $parentid);

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }
        if (!$result) {
            return false;
        }

        // Check if there is still a link to another parent, if not, also delete the folder itself
        $links_left = $this->getParentCount($obj->folderid());
        if (!isset($links_left) || (-1 == $links_left)) {
            // an error occured
            return false;
        } elseif (0 == $links_left) {
            return $this->delete($obj);
        } else {
            return true;
        }
    }

    /**
     * Deletes folders matching a set of conditions
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

    /**
     * Count the number of online clips within a folder
     *
     * @param  int $cat_id id of the folder where to look
     * @return int count of clips
     *
     * @see clipsCount()
     */
    public function onlineClipsCount($cat_id = 0)
    {
        return $this->clipsCount($cat_id);
    }

    /**
     * Count the number of online clips within a folder
     *
     * @param  int   $cat_id id of the folder where to look
     * @param string $status
     * @return int count of clips
     */
    public function clipsCount($cat_id = 0, $status = '')
    {
        $smartmediaClipHandler = Smartmedia\Helper::getInstance()->getHandler('Clip');

        return $smartmediaClipHandler->getCountsByParent($cat_id, $status);
    }
}
