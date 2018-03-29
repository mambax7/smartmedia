<?php namespace XoopsModules\Smartmedia;

use XoopsModules\Smartmedia;

/**
 * Smartmedia\ContentHandler class
 *
 * Content Handler for Smartmedia\Content class
 *
 * @author  marcan <marcan@smartfactory.ca> &
 * @access  public
 * @package SmartMedia
 */
class ContentHandler extends \XoopsObjectHandler
{
    /**
     * Database connection
     *
     * @var object
     * @access    private
     */
    public $db;

    /**
     * Name of child class
     *
     * @var string
     * @access    private
     */
    public $classname = Content::class;

    /**
     * DB table name
     *
     * @var string
     * @access private
     */
    public $dbtable = 'smartmedia_categories';

    /**
     * key field name
     *
     * @var string
     * @access private
     */
    public $_key_field = 'contentid';

    /**
     * caption field name
     *
     * @var string
     * @access private
     */
    public $_caption_field = 'title';

    /**
     * Module id
     *
     * @var int
     * @access    private
     */
    public $_module_id;

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
     * @param  objecs &$db {@link XoopsHandlerFactory}
     * @return object {@link Smartmedia\ContentHandler}
     * @access public
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
     * create a new content object
     * @return object {@link Smartmedia\Content}
     * @access public
     */
    public function create()
    {
        return new $this->classname();
    }

    /**
     * retrieve a content object from the database
     * @param  int   $id ID of content
     * @param string $languageid
     * @return bool <a href='psi_element://Smartmedia\Content'>Smartmedia\Content</a>
     * @access public
     */
    public function &get($id, $languageid = 'current')
    {
        $id = (int)$id;
        if ($id > 0) {
            $sql = $this->_selectQuery(new \Criteria('contentid', $id));

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

                return $obj;
            }
        }

        return false;
    }

    /**
     * Create a "select" SQL query
     * @param  object $criteria {@link \CriteriaElement} to match
     * @return string SQL query
     * @access    private
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
     * count objects matching a criteria
     *
     * @param  object $criteria {@link \CriteriaElement} to match
     * @return int    count of objects
     * @access    public
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
     * @param  int $parentid
     * @return int
     */
    public function getCategoriesCount($parentid = 0)
    {
        if (0 == $parentid) {
            return $this->getCount();
        }
        $criteria = new \CriteriaCompo();
        if (isset($parentid) && (-1 != $parentid)) {
            $criteria->add(new \Criteria('parentid', $parentid));
        }

        return $this->getCount($criteria);
    }

    /**
     * retrieve objects from the database
     *
     * @param  object $criteria  {@link \CriteriaElement} conditions to be met
     * @param  bool   $id_as_key Should the content ID be used as array key
     * @return array  array of {@link Smartmedia\Content} objects
     * @access    public
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
                $ret[$obj->getVar('contentid')] =& $obj;
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * @param  array  $queryarray
     * @param  string $andor
     * @param  int    $limit
     * @param  int    $offset
     * @param  int    $userid
     * @return array
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
     * @param  int    $limit
     * @param  int    $start
     * @param  int    $parentid
     * @param  string $sort
     * @param  string $order
     * @param  bool   $id_as_key
     * @return array
     */
    public function &getCategories($limit = 0, $start = 0, $parentid = 0, $sort = 'weight', $order = 'ASC', $id_as_key = true)
    {
        $criteria = new \CriteriaCompo();

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        if (-1 != $parentid) {
            $criteria->add(new \Criteria('parentid', $parentid));
        }

        $criteria->setStart($start);
        $criteria->setLimit($limit);

        return $this->getObjects($criteria, $id_as_key);
    }

    /**
     * store a content in the database
     *
     * @param \XoopsObject $object
     * @param  bool        $force
     * @return bool   FALSE if failed, TRUE if already present and unchanged or successful
     * @access    public
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
            $contentid = $this->db->genId($this->db->prefix($this->dbtable) . '_uid_seq');
            $sql       = sprintf('INSERT INTO %s (
            contentid,
            parentid,
            weight,
            image,
            default_languageid)
            VALUES (
            %u,
            %u,
            %u,
            %s,
            %s)', $this->db->prefix($this->dbtable), $contentid, $parentid, $weight, $this->db->quoteString($image), $this->db->quoteString($default_languageid));
        } else {
            $sql = sprintf('UPDATE %s SET
            parentid = %u,
            weight = %u,
            image = %s,
            default_languageid = %s
            WHERE contentid = %u', $this->db->prefix($this->dbtable), $parentid, $weight, $this->db->quoteString($image), $this->db->quoteString($default_languageid), $contentid);
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
        if (empty($contentid)) {
            $contentid = $this->db->getInsertId();
        }
        $object->assignVar('contentid', $contentid);

        return true;
    }

    /**
     * delete a Content from the database
     *
     * @param \XoopsObject $object
     * @param  bool        $force
     * @return bool   FALSE if failed.
     * @access public
     */
    public function delete(\XoopsObject $object, $force = false)
    {
        if (strtolower(get_class($obj)) != $this->classname) {
            return false;
        }

        // Delete all language info for this content
        $smartmedia_content_textHandler = Smartmedia\Helper::getInstance()->getHandler('ContentText');
        $criteria                       = new \CriteriaCompo(new \Criteria('contentid', $obj->contentid()));
        if (!$smartmedia_content_textHandler->deleteAll($criteria)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE contentid = %u', $this->db->prefix($this->dbtable), $obj->getVar('contentid'));

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
     * delete department matching a set of conditions
     *
     * @param  object $criteria {@link \CriteriaElement}
     * @return bool   FALSE if deletion failed
     * @access    public
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
     * @param  int $cat_id
     * @return mixed
     */
    public function onlineFoldersCount($cat_id = 0)
    {
        return $this->foldersCount($cat_id, _SMARTMEDIA_FOLDER_STATUS_ONLINE);
    }

    /**
     * @param  int    $cat_id
     * @param  string $status
     * @return mixed
     */
    public function foldersCount($cat_id = 0, $status = '')
    {
        $smartmediaFolderHandler = Smartmedia\Helper::getInstance()->getHandler('Folder');

        return $smartmediaFolderHandler->getCountsByParent($cat_id, $status);
    }
}
