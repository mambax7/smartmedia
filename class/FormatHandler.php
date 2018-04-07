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
 * Format handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Format class objects.
 *
 * @author  marcan <marcan@smartfactory.ca>
 * @package SmartMedia
 */
class FormatHandler extends \XoopsObjectHandler
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
    public $classname = Format::class;

    /**
     * db table name
     *
     * @var string
     * @access private
     */
    public $dbtable = 'smartmedia_formats';

    /**
     * key field name
     *
     * @var string
     * @access private
     */
    public $_key_field = 'formatid';

    /**
     * caption field name
     *
     * @var string
     * @access private
     */
    public $_caption_field = 'format';

    /**
     * Constructor
     *
     * @param \XoopsDatabase $db reference to a xoops_db object
     */
    public function __construct(\XoopsDatabase $db)
    {
        $this->db = $db;
    }

    /**
     * Singleton - prevent multiple instances of this class
     *
     * @param  \XoopsDatabase $db {@link XoopsHandlerFactory}
     * @return FormatHandler {@link Smartmedia\FormatHandler}
     * @access public
     */
    public static function getInstance($db)
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new static($db);
        }

        return $instance;
    }

    /**
     * @param  bool $isNew
     * @return mixed
     */
    public function create($isNew = true)
    {
        $obj = new $this->classname;
        if ($isNew) {
            $obj->setNew();
        }

        return $obj;
    }

    /**
     * retrieve a Format
     *
     * @param  int $id format id
     * @return mixed reference to the {@link Smartmedia\Format} object, FALSE if failed
     */
    public function &get($id)
    {
        if ((int)$id > 0) {
            $sql = 'SELECT * FROM ' . $this->db->prefix($this->dbtable) . ' WHERE ' . $this->_key_field . '=' . $id;
            if (!$result = $this->db->query($sql)) {
                return false;
            }

            $numrows = $this->db->getRowsNum($result);
            if (1 == $numrows) {
                $obj = new $this->classname;
                $obj->assignVars($this->db->fetchArray($result));

                return $obj;
            }
        }

        return false;
    }

    /**
     * insert a new format in the database
     *
     * @param \XoopsObject $object
     * @param  bool        $force
     * @return bool   FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(\XoopsObject $object, $force = false)
    {
        if (!is_a($object, $this->classname)) {
            return false;
        }

        if (!$object->isDirty()) {
            return true;
        }

        if (!$object->cleanVars()) {
            return false;
        }

        foreach ($object->cleanVars as $k => $v) {
            ${$k} = $v;
        }

        if ($object->isNew()) {
            $sql = sprintf('INSERT INTO `%s` (' . $this->_key_field . ", template, FORMAT, ext) VALUES ('', %s, %s, %s)", $this->db->prefix($this->dbtable), $this->db->quoteString($template), $this->db->quoteString($format), $this->db->quoteString($ext));
        } else {
            $id  = $formatid;
            $sql = sprintf('UPDATE `%s` SET template = %s, FORMAT = %s, ext = %s WHERE ' . $this->_key_field . ' = %u', $this->db->prefix($this->dbtable), $this->db->quoteString($template), $this->db->quoteString($format), $this->db->quoteString($ext), $id);
        }

        //echo "<br>" . $sql . "<br>";

        if (false !== $force) {
            $result = $this->db->queryF($sql);
        } else {
            $result = $this->db->query($sql);
        }

        if (!$result) {
            return false;
        }
        if ($object->isNew()) {
            $object->assignVar('id', $this->db->getInsertId());
        }

        return true;
    }

    /**
     * delete a Format from the database
     *
     * @param \XoopsObject $object
     * @param  bool        $force
     * @return bool   FALSE if failed.
     */
    public function delete(\XoopsObject $object, $force = false)
    {
        if (get_class($object) != $this->classname) {
            return false;
        }

        $sql = sprintf('DELETE FROM `%s` WHERE ' . $this->_key_field . ' = %u', $this->db->prefix($this->dbtable), $object->formatid());

        //echo "<br>$sql<br>";

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
     * retrieve Format from the database
     *
     * @param  \CriteriaElement $criteria  {@link \CriteriaElement} conditions to be met
     * @param  bool   $id_as_key use the formatid as key for the array?
     * @return array  array of {@link Smartmedia\Format} objects
     */
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret   = false;
        $limit = $start = 0;
        $sql   = 'SELECT * FROM ' . $this->db->prefix($this->dbtable);

        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $whereClause = $criteria->renderWhere();

            if ('WHERE ()' !== $whereClause) {
                $sql .= ' ' . $criteria->renderWhere();
                if ('' != $criteria->getSort()) {
                    $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                }
                $limit = $criteria->getLimit();
                $start = $criteria->getStart();
            }
        }

        //echo "<br>" . $sql . "<br>";

        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }

//        if (!is_array($result) || 0 == count($result)) {

        if ((!$result) || (0 === $result->num_rows)) {
            return $ret;
        }

        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $obj = new $this->classname;
//            $temp = '\\XoopsModules\\Smartmedia\\' . $this->classname;
//            $obj = new $temp;

            $obj->assignVars($myrow);

            if (!$id_as_key) {
                $ret[] = $obj;
            } else {
                $ret[$myrow['id']] = $obj;
            }
            unset($obj);
        }

        return $ret;
    }

    /**
     * @param  string $sort
     * @param  string $order
     * @return array
     */
    public function getFormats($sort = 'format', $order = 'ASC')
    {
        $criteria = new \CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $ret = $this->getObjects($criteria);

        return $ret;
    }

    /**
     * count Formats matching a condition
     *
     * @param  \CriteriaElement $criteria {@link \CriteriaElement} to match
     * @return int    count of clients
     */
    public function getCount(\CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix($this->dbtable);
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $whereClause = $criteria->renderWhere();
            if ('WHERE ()' !== $whereClause) {
                $sql .= ' ' . $criteria->renderWhere();
            }
        }

        //echo "<br>" . $sql . "<br>";

        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Change a value for a Format with a certain criteria
     *
     * @param string $fieldname  Name of the field
     * @param string $fieldvalue Value to write
     * @param object $criteria   {@link \CriteriaElement}
     *
     * @return bool
     **/
    public function updateAll($fieldname, $fieldvalue, $criteria = null)
    {
        $set_clause = is_numeric($fieldvalue) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->quoteString($fieldvalue);
        $sql        = 'UPDATE ' . $this->db->prefix($this->dbtable) . ' SET ' . $set_clause;
        if (isset($criteria) && is_subclass_of($criteria, 'CriteriaElement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }

        //echo "<br>" . $sql . "<br>";

        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }
}
