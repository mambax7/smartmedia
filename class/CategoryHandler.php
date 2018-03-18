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
 * Smartmedia Category Handler class
 *
 * Category Handler responsible for handling {@link Smartmedia\Category} objects
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class CategoryHandler extends \XoopsObjectHandler
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
    public $classname = Category::class; //'Category';

    /**
     * Related table name
     *
     * @var string
     */
    public $dbtable = 'smartmedia_categories';

    /**
     * key field name
     *
     * @var string
     */
    public $_key_field = 'categoryid';

    /**
     * caption field name
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
     * @param  \XoopsObject &$db {@link XoopsHandlerFactory}
     * @return  Smartmedia\CategoryHandler
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
     * Creates a new Category object
     * @return object Smartmedia\Category
     */
    public function create()
    {
        return new $this->classname();
//        $temp = '\\XoopsModules\\Smartmedia\\' . $this->classname;
//        return new $temp;
    }

    /**
     * Retrieves a category object from the database
     *
     * If no languageid is specified, the method will load the translation related to the current
     * language selected by the user
     *
     * @param  int    $id         id of the folder
     * @param  string $languageid language of the translation to load
     * @return mixed  reference to the {@link Smartmedia\Category} object, FALSE if failed
     */
    public function &get($id, $languageid = 'current')
    {
        $id = (int)$id;
        if ($id > 0) {
            $sql = $this->_selectQuery(new \Criteria('categoryid', $id));

            //echo "<br>$sql<br/>";

            if (!$result = $this->db->query($sql)) {
                return false;
            }
            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                if ('current' == $languageid) {
                    global $xoopsConfig;
                    $languageid = $xoopsConfig['language'];
                }
                $obj = new $this->classname($languageid, $this->db->fetchArray($result));

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

        //echo "<br>$sql<br/>";

        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Count categories belonging to a specific parentid
     *
     * If no $parentid is specified, the method will count all top level categories in the module.<br>
     * Please note that nested categories are not implemented in the module. The structure is there
     * for futur use.
     *
     * @param  int $parentid category in which to count categories
     * @return int count of objects
     */
    public function getCategoriesCount($parentid = 0)
    {
        if (0 == $parentid) {
            return $this->getCount();
        }
        $criteria = new \CriteriaCompo();
        if (isset($parentid) && ($parentid != -1)) {
            $criteria->add(new \Criteria('parentid', $parentid));
        }

        return $this->getCount($criteria);
    }

    /**
     * Retrieve objects from the database
     *
     * @param  object $criteria  {@link \CriteriaElement} conditions to be met
     * @param  bool   $id_as_key Should the category ID be used as array key
     * @return array  array of {@link Smartmedia\Category} objects
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        global $xoopsConfig;

        $ret   = [];
        $limit = $start = 0;
        $sql   = $this->_selectQuery($criteria);

        if (isset($criteria)) {
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

        //echo "<br>$sql<br>";

        $result = $this->db->query($sql, $limit, $start);
        // If no records from db, return empty array
        if (!$result) {
            return $ret;
        }

        // Add each returned record to the result array
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $obj = new $this->classname($xoopsConfig['language'], $myrow);
            if (!$id_as_key) {
                $ret[] =& $obj;
            } else {
                $ret[$obj->getVar('categoryid')] =& $obj;
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * Get a list of {@link Smartmedia\Category} objects for the search feature
     *
     * @param array  $queryarray list of keywords to look for
     * @param string $andor      specify which type of search we are performing : AND or OR
     * @param int    $limit      maximum number of results to return
     * @param int    $offset     at which category shall we start
     * @param int    $userid     userid is not used here as category creator are not tracked
     *
     * @return array array containing information about the folders mathing the search criterias
     */
    public function &getObjectsForSearch($queryarray = [], $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        global $xoopsConfig;

        $ret = [];
        $sql = 'SELECT *
                   FROM ' . $this->db->prefix($this->dbtable) . ' AS item
                   INNER JOIN ' . $this->db->prefix($this->dbtable) . '_text AS itemtext
                   ON item.' . $this->_key_field . ' = itemtext.' . $this->_key_field;

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

        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ('' != $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . '
                    ' . $criteria->getOrder();
            }
        }

        // echo "<br>$sql<br>";

        $result = $this->db->query($sql, $limit, $offset);
        // If no records from db, return empty array
        if (!$result) {
            return $ret;
        }

        // Add each returned record to the result array
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $item['id']    = $myrow[$this->_key_field];
            $item['title'] = $myrow[$this->_caption_field];
            $ret[]         = $item;
            unset($item);
        }

        return $ret;
    }

    /**
     * Get a list of {@link Smartmedia\Category}
     *
     * @param int    $limit     maximum number of results to return
     * @param int    $start     at which folder shall we start
     * @param int    $parentid  category to which belong the categories to return
     * @param string $sort      sort parameter
     * @param string $order     order parameter
     * @param bool   $id_as_key wether or not the categoryid should be used as array key
     *
     * @return array array of {@link Smartmedia\Folder}
     */
    public function &getCategories($limit = 0, $start = 0, $parentid = 0, $sort = 'weight', $order = 'ASC', $id_as_key = true)
    {
        $criteria = new \CriteriaCompo();

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        if ($parentid != -1) {
            $criteria->add(new \Criteria('parentid', $parentid));
        }

        $criteria->setStart($start);
        $criteria->setLimit($limit);

        return $this->getObjects($criteria, $id_as_key);
    }

    /**
     * Stores a category in the database
     *
     * @param \XoopsObject $obj reference to the {@link Smartmedia\Category}
     * @param  bool   $force
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
            $categoryid = $this->db->genId($this->db->prefix($this->dbtable) . '_uid_seq');
            $sql        = sprintf('INSERT INTO %s (
            categoryid,
            parentid,
            weight,
            image,
            default_languageid)
            VALUES (
            %u,
            %u,
            %u,
            %s,
            %s)', $this->db->prefix($this->dbtable), $categoryid, $parentid, $weight, $this->db->quoteString($image), $this->db->quoteString($default_languageid));
        } else {
            $sql = sprintf('UPDATE %s SET parentid = %u, weight = %u, image = %s, default_languageid = %s WHERE categoryid = %u', $this->db->prefix($this->dbtable), $parentid, $weight, $this->db->quoteString($image), $this->db->quoteString($default_languageid), $categoryid);
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
        if (empty($categoryid)) {
            $categoryid = $this->db->getInsertId();
        }
        $object->assignVar('categoryid', $categoryid);

        return true;
    }

    /**
     * Deletes a category from the database
     *
     * @param  \XoopsObject $obj reference to the {@link Smartmedia\Category} obj to delete
     * @param  bool   $force
     * @return bool   FALSE if failed.
     */
    public function delete(\XoopsObject $obj, $force = false)
    {
        if (strtolower(get_class($obj)) != $this->classname) {
            return false;
        }

        // Delete all language info for this category
        $smartmediaCategoryTextHandler = Smartmedia\Helper::getInstance()->getHandler('CategoryText');
        $criteria                      = new \CriteriaCompo(new \Criteria('categoryid', $obj->categoryid()));
        if (!$smartmediaCategoryTextHandler->deleteAll($criteria)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE categoryid = %u', $this->db->prefix($this->dbtable), $obj->getVar('categoryid'));

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
     * Deletes categories matching a set of conditions
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
     * Count the number of online folders within a category
     *
     * @param  int $cat_id id of the category where to look
     * @return int count of folders
     *
     * @see foldersCount()
     */
    public function onlineFoldersCount($cat_id = 0)
    {
        return $this->foldersCount($cat_id, _SMARTMEDIA_FOLDER_STATUS_ONLINE);
    }

    /**
     * Count the number of online folders within a category
     *
     * @param  int   $cat_id id of the folder where to look
     * @param string $status status of the folders to count
     * @return int count of folders
     */
    public function foldersCount($cat_id = 0, $status = '')
    {
        $smartmediaFolderHandler = Smartmedia\Helper::getInstance()->getHandler('Folder');

        return $smartmediaFolderHandler->getCountsByParent($cat_id, $status);
    }
}
