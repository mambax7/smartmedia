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

/** Status of an offline clip */
define('_SMARTMEDIA_CLIP_STATUS_OFFLINE', 1);
/** Status of an online clip */
define('_SMARTMEDIA_CLIP_STATUS_ONLINE', 2);


/**
 * Smartmedia Clip Handler class
 *
 * Clip Handler responsible for handling {@link Smartmedia\Clip} objects
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class ClipHandler extends \XoopsObjectHandler
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
    public $classname = Clip::class;

    /**
     * Related table name
     *
     * @var string
     */
    public $dbtable = 'smartmedia_clips';

    /**
     * DB parent table name
     *
     * @var string
     */
    public $dbtable_parent = 'smartmedia_folders_categories';

    /**
     * Related parent field name
     *
     * @var string
     */
    public $_parent_field = 'folderid';

    /**
     * Key field name
     *
     * @var string
     */
    public $_key_field = 'clipid';

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
     * @param  object &$db {@link XoopsHandlerFactory}
     * @return object SmartmediaClipHandler
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
     * Creates a new clip object
     *
     * @return object Smartmedia\Clip
     */
    public function create()
    {
        return new $this->classname();
//        $temp = '\\XoopsModules\\Smartmedia\\' . $this->classname;
//        $clip = new $temp;
//        return $clip;
    }

    /**
     * Retrieve a clip object from the database
     *
     * If no languageid is specified, the method will load the translation related to the current
     * language selected by the user
     *
     * @param  int    $id         id of the clip
     * @param  string $languageid language of the translation to load
     * @return mixed  reference to the {@link Smartmedia\Clip} object, FALSE if failed
     */
    public function &get($id, $languageid = 'current')
    {
        $id = (int)$id;
        if ($id > 0) {
            $sql = $this->_selectQuery(new \Criteria('clipid', $id));

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
     * Create a "SELECY" SQL query
     *
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
     * Count clips belonging to a specific folder
     *
     * If no categoryid is specified, the method will count all clips in the module
     *
     * @param  int $categoryid category in which to count clips
     * @return int count of objects
     */
    public function getclipsCount($categoryid = 0)
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
     * Retrieve objects from the database
     *
     * @param  object $criteria {@link \CriteriaElement} conditions to be met
     * @param bool    $category_id_as_key
     * @return array  array of {@link Smartmedia\Clip} objects
     */
    public function &getObjects($criteria = null, $category_id_as_key = false)
    {
        global $xoopsConfig;
        $smartConfig =& smartmedia_getModuleConfig();

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
            if (!$category_id_as_key) {
                $ret[$obj->getVar('clipid')] =& $obj;
            } else {
                $ret[$myrow['folderid']][$obj->getVar('clipid')] =& $obj;
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * Get a list of {@link Smartmedia\Clip} objects for the search feature
     *
     * @param array  $queryarray list of keywords to look for
     * @param string $andor      specify which type of search we are performing : AND or OR
     * @param int    $limit      maximum number of results to return
     * @param int    $offset     at which clip shall we start
     * @param int    $userid     userid related to the creator of the clip
     *
     * @return array array containing information about the clips mathing the search criterias
     */
    public function &getObjectsForSearch($queryarray = [], $andor = 'AND', $limit = 0, $offset = 0, $userid = 0)
    {
        global $xoopsConfig;

        $ret = [];
        $sql = 'SELECT item.' . $this->_key_field . ', itemtext.' . $this->_caption_field . ', itemtext.description, itemtext.tab_caption_1, itemtext.tab_text_1, itemtext.tab_caption_2, itemtext.tab_text_2, itemtext.tab_caption_3, itemtext.tab_text_3, parent.categoryid, parent.folderid FROM
                   (
                     (' . $this->db->prefix($this->dbtable) . ' AS item
                       INNER JOIN ' . $this->db->prefix($this->dbtable) . '_text AS itemtext
                       ON item.' . $this->_key_field . ' = itemtext.' . $this->_key_field . '
                     )
                     INNER JOIN ' . $this->db->prefix($this->dbtable_parent) . ' AS parent
                      ON parent.' . $this->_parent_field . ' = item.' . $this->_parent_field . '
                   )
                   ';

        if (!empty($queryarray)) {
            $criteriaKeywords = new \CriteriaCompo();
            for ($i = 0; $i < count($queryarray); ++$i) {
                $criteriaKeyword = new \CriteriaCompo();
                $criteriaKeyword->add(new \Criteria('itemtext.title', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.description', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.tab_caption_1', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.tab_text_1', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.tab_caption_2', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.tab_text_2', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.tab_caption_3', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
                $criteriaKeyword->add(new \Criteria('itemtext.tab_text_3', '%' . $queryarray[$i] . '%', 'LIKE'), 'OR');
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
            $item['folderid']   = $myrow['folderid'];
            $item['categoryid'] = $myrow['categoryid'];

            $ret[] = $item;
            unset($item);
        }

        return $ret;
    }

    /**
     * Get a list of {@link Smartmedia\Clip}
     *
     * @param int    $limit              maximum number of results to return
     * @param int    $start              at which clip shall we start
     * @param int    $categoryid         category to which belong the parent folder of the clip
     * @param string $sort               sort parameter
     * @param string $order              order parameter
     * @param bool   $category_id_as_key wether or not the categoryid should be used as array key
     *
     * @return array array of {@link Smartmedia\Clip}
     */
    public function &getClips($limit = 0, $start = 0, $categoryid = 0, $sort = 'weight', $order = 'ASC', $category_id_as_key = true)
    {
        $criteria = new \CriteriaCompo();

        if (isset($categoryid) && (0 != $categoryid)) {
            $criteria->add(new \Criteria('folderid', $categoryid));
        }

        $criteria->setSort($sort);
        $criteria->setOrder($order);

        $criteria->setStart($start);
        $criteria->setLimit($limit);

        return $this->getObjects($criteria, $category_id_as_key);
    }

    /**
     * Get a list of {@link Smartmedia\Clip} used in the admin index page
     *
     * @param int    $start       at which clip shall we start
     * @param int    $limit       maximum number of results to return
     * @param string $sort        sort parameter
     * @param string $order       order parameter
     * @param string $languagesel specific language
     *
     * @return array array of {@link Smartmedia\Clip}
     */
    public function &getClipsFromAdmin($start = 0, $limit = 0, $sort = 'clipid', $order = 'ASC', $languagesel)
    {
        if ('all' !== $languagesel) {
            $where = "WHERE clips_text.languageid = '" . $languagesel . "'";
        } else {
            $where = '';
        }
        global $xoopsConfig, $xoopsDB;
        $smartConfig =& smartmedia_getModuleConfig();
        $ret         = [];
        $sql         = 'SELECT DISTINCT clips.clipid, clips.weight, clips_text.title, folders.folderid, folders_text.title AS foldertitle, categories.categoryid
                    FROM (
                        ' . $xoopsDB->prefix('smartmedia_clips') . ' AS clips
                        INNER JOIN ' . $this->db->prefix('smartmedia_clips_text') . ' AS clips_text ON clips.clipid = clips_text.clipid
                        )
                    INNER JOIN ' . $this->db->prefix('smartmedia_folders') . ' AS folders ON clips.folderid=folders.folderid

                    INNER JOIN ' . $this->db->prefix('smartmedia_folders_text') . ' AS folders_text ON folders.folderid = folders_text.folderid

                       INNER JOIN ' . $this->db->prefix('smartmedia_folders_categories') . ' AS categories
                    ON folders.folderid = categories.folderid ' . $where . "
                    ORDER BY $sort $order
        ";

        //echo "<br>$sql<br>";

        $result = $this->db->query($sql, $limit, $start);
        // If no records from db, return empty array
        if (!$result) {
            return $ret;
        }

        // Add each returned record to the result array
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $item                = [];
            $item['clipid']      = $myrow['clipid'];
            $item['weight']      = $myrow['weight'];
            $item['title']       = $myrow['title'];
            $item['folderid']    = $myrow['folderid'];
            $item['foldertitle'] = $myrow['foldertitle'];
            $item['categoryid']  = $myrow['categoryid'];
            $ret[]               = $item;
            unset($item);
        }

        return $ret;
    }

    /**
     * Get count of clips for the admin index page
     *
     * @param string $languagesel specific language
     *
     * @return int count of clips
     */
    public function &getClipsCountFromAdmin($languagesel)
    {
        global $xoopsConfig, $xoopsDB;
        $smartConfig =& smartmedia_getModuleConfig();

        if ('all' === $languagesel) {
            $where = '';
        } else {
            $where = "WHERE clips_text.languageid = '" . $languagesel . "'";
        }
        $sql = 'SELECT COUNT(DISTINCT clips.clipid)
                    FROM (
                        ' . $xoopsDB->prefix('smartmedia_clips') . ' AS clips
                        INNER JOIN ' . $this->db->prefix('smartmedia_clips_text') . ' AS clips_text ON clips.clipid = clips_text.clipid
                        )
                        INNER JOIN ' . $this->db->prefix('smartmedia_folders') . ' AS folders ON clips.folderid=folders.folderid
                        INNER JOIN ' . $this->db->prefix('smartmedia_folders_text') . ' AS folders_text ON folders.folderid = folders_text.folderid
                    INNER JOIN ' . $this->db->prefix('smartmedia_folders_categories') . ' AS categories
                    ON folders.folderid = categories.folderid

        ' . $where;

        //echo "<br>$sql<br>";

        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Get count of clips by folder
     *
     * @param int $parent_id folderid in which to count the clips
     *
     * @return array count of clips
     */
    public function getCountsByParent($parent_id = 0)
    {
        $ret = [];
        $sql = 'SELECT ' . $this->_parent_field . ' AS parentid, COUNT(' . $this->_key_field . ' ) AS count
                FROM ' . $this->db->prefix($this->dbtable) . '';

        if ((int)$parent_id > 0) {
            $sql .= ' WHERE ' . $this->_parent_field . ' = ' . (int)$parent_id;
        }
        $sql .= ' GROUP BY ' . $this->_parent_field;

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
     * Stores a clip in the database
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
            $clipid = $this->db->genId($this->db->prefix($this->dbtable) . '_uid_seq');
            $sql    = sprintf(
                'INSERT INTO %s (
            clipid,
            folderid,
            statusid,
            created_date,
            created_uid,
            modified_date,
            modified_uid,
            languageid,
            duration,
            formatid,
            width,
            height,
            counter,
            autostart,
            image_lr,
            image_hr,
            file_lr,
            file_hr,
            weight,
            default_languageid)
            VALUES (
            %u,
            %u,
            %u,
            %u,
            %u,
            %u,
            %u,
            %s,
            %u,
            %u,
            %u,
            %u,
            %u,
            %u,
            %s,
            %s,
            %s,
            %s,
            %u,
            %s)',
                $this->db->prefix($this->dbtable),
                $clipid,
                $folderid,
                $statusid,
                time(),
                $created_uid,
                time(),
                $modified_uid,
                $this->db->quoteString($languageid),
                $duration,
                $formatid,
                $width,
                $height,
                $counter,
                $autostart,
                $this->db->quoteString($image_lr),
                              $this->db->quoteString($image_hr),
                $this->db->quoteString($file_lr),
                $this->db->quoteString($file_hr),
                $weight,
                $this->db->quoteString($default_languageid)
            );
        } else {
            $sql = sprintf(
                'UPDATE %s SET
            folderid = %u,
            statusid = %u,
            created_date = %u,
            created_uid = %u,
            modified_date = %u,
            modified_uid = %u,
            languageid = %s,
            duration = %u,
            formatid = %u,
            width = %u,
            height = %u,
            counter = %u,
            autostart = %u,
            image_lr = %s,
            image_hr = %s,
               file_lr = %s,
            file_hr = %s,
            weight = %u,
            default_languageid = %s
            WHERE clipid = %u',
                $this->db->prefix($this->dbtable),
                $folderid,
                $statusid,
                $created_date,
                $created_uid,
                time(),
                $modified_uid,
                $this->db->quoteString($languageid),
                $duration,
                $formatid,
                $width,
                $height,
                $counter,
                $autostart,
                $this->db->quoteString($image_lr),
                           $this->db->quoteString($image_hr),
                $this->db->quoteString($file_lr),
                $this->db->quoteString($file_hr),
                $weight,
                $this->db->quoteString($default_languageid),
                $clipid
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

        //Make sure auto-gen ID is stored correctly in object
        if (empty($clipid)) {
            $clipid = $this->db->getInsertId();
        }
        $object->assignVar('clipid', $clipid);

        return true;
    }

    /**
     * Deletes a clip from the database
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

        $smartmediaClipTextHandler = Smartmedia\Helper::getInstance()->getHandler('ClipText');
        $criteria                  = new \CriteriaCompo(new \Criteria('clipid', $obj->clipid()));
        if (!$smartmediaClipTextHandler->deleteAll($criteria)) {
            return false;
        }
        $sql = sprintf('DELETE FROM `%s` WHERE clipid = %u', $this->db->prefix($this->dbtable), $obj->getVar('clipid'));

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
     * Deletes clips matching a set of conditions
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
     * @param       $fieldname
     * @param       $fieldvalue
     * @param  null $criteria
     * @return bool
     */
    public function updateAll($fieldname, $fieldvalue, $criteria = null)
    {
        $set_clause = is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        $sql        = 'UPDATE ' . $this->db->prefix('smartmedia_clips') . ' SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }
}
