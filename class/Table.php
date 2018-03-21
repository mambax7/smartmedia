<?php namespace XoopsModules\Smartmedia;

/**
 * Contains the classes for updating database tables
 *
 * @license    GNU
 * @author     marcan <marcan@smartfactory.ca>
 * @version    $Id: dbupdater.php,v 1.1 2005/06/02 14:19:22 malanciault Exp $
 * @link       http://www.smartfactory.ca The SmartFactory
 * @package    SmartMedia
 * @subpackage dbUpdater
 */

use XoopsModules\Smartmedia;

/**
 * Smartmedia\Table class
 *
 * Information about an individual table
 *
 * @package SmartMedia
 * @author  marcan <marcan@smartfactory.ca>
 * @link    http://www.smartfactory.ca The SmartFactory
 */
class Table
{
    /**
     * @var string $_name name of the table
     */
    public $_name;

    /**
     * @var string $_structure structure of the table
     */
    public $_structure;

    /**
     * @var array $_data containing valued of each records to be added
     */
    public $_data;

    /**
     * @var array $_alteredFields containing fields to be altered
     */
    public $_alteredFields;

    /**
     * @var array $_dropedFields containing fields to be droped
     */
    public $_dropedFields;

    /**
     * @var array $_flagForDrop flag table to drop it
     */
    public $_flagForDrop = false;

    /**
     * @var array $_updatedFields containing fields which values will be updated
     */
    public $_updatedFields;

    /**
     * Constructor
     *
     * @param string $name name of the table
     *
     */
    public function __construct($name)
    {
        $this->_name = $name;
        $this->_data = [];
    }

    /**
     * Return the table name, prefixed with site table prefix
     *
     * @return string table name
     *
     */
    public function name()
    {
        global $xoopsDB;

        return $xoopsDB->prefix($this->_name);
    }

    /**
     * Set the table structure
     *
     * @param string $structure table structure
     *
     */
    public function setStructure($structure)
    {
        $this->_structure = $structure;
    }

    /**
     * Return the table structure
     *
     * @return string table structure
     *
     */
    public function getStructure()
    {
        return sprintf($this->_structure, $this->name());
    }

    /**
     * Add values of a record to be added
     *
     * @param string $data values of a record
     *
     */
    public function setData($data)
    {
        $this->_data[] = $data;
    }

    /**
     * Get the data array
     *
     * @return array containing the records values to be added
     *
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Use to insert data in a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function addData()
    {
        global $xoopsDB;

        foreach ($this->getData() as $data) {
            $query = sprintf('INSERT INTO %s VALUES (%s)', $this->name(), $data);
            $ret   = $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_ADD_DATA_ERR, $this->name()) . '</li>';
            } else {
                echo "<li class='ok'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_ADD_DATA, $this->name()) . '</li>';
            }
        }

        return $ret;
    }

    /**
     * Add values of a record to be added
     *
     * @param string $name       name of the field
     * @param string $properties properties of the field
     *
     */
    public function addAlteredField($name, $properties)
    {
        $field['name']          = $name;
        $field['properties']    = $properties;
        $this->_alteredFields[] = $field;
    }

    /**
     * Get fields that need to be altered
     *
     * @return array fields that need to be altered
     *
     */
    public function getAlteredFields()
    {
        return $this->_alteredFields;
    }

    /**
     * Add field for which the value will be updated
     *
     * @param string $name  name of the field
     * @param string $value value to be set
     *
     */
    public function addUpdatedField($name, $value)
    {
        $field['name']          = $name;
        $field['value']         = $value;
        $this->_updatedFields[] = $field;
    }

    /**
     * Get fields which values need to be updated
     *
     * @return array fields which values need to be updated
     *
     */
    public function getUpdatedFields()
    {
        return $this->_updatedFields;
    }

    /**
     * Add values of a record to be added
     *
     * @param string $name name of the field
     *
     */
    public function addDropedField($name)
    {
        $this->_dropedFields[] = $name;
    }

    /**
     * Get fields that need to be droped
     *
     * @return array fields that need to be droped
     *
     */
    public function getDropedFields()
    {
        return $this->_dropedFields;
    }

    /**
     * Set the flag to drop the table
     *
     */
    public function setFlagForDrop()
    {
        $this->_flagForDrop = true;
    }

    /**
     * Use to create a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function createTable()
    {
        global $xoopsDB;

        $query = $this->getStructure();
        $ret   = $xoopsDB->query($query);
        if (!$ret) {
            echo "<li class='err'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_CREATE_TABLE_ERR, $this->name()) . '</li>';
        } else {
            echo "<li class='ok'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_CREATE_TABLE, $this->name()) . '</li>';
        }

        return $ret;
    }

    /**
     * Use to drop a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function dropTable()
    {
        global $xoopsDB;

        $query = sprintf('DROP TABLE %s', $this->name());
        $ret   = $xoopsDB->query($query);
        if (!$ret) {
            echo "<li class='err'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_DROP_TABLE_ERR, $this->name()) . '</li>';

            return false;
        } else {
            echo "<li class='ok'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_DROP_TABLE, $this->name()) . '</li>';

            return true;
        }
    }

    /**
     * Use to alter a table
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function alterTable()
    {
        global $xoopsDB;

        $ret = true;

        foreach ($this->getAlteredFields() as $alteredField) {
            $query = sprintf('ALTER TABLE %s ADD %s %s', $this->name(), $alteredField['name'], $alteredField['properties']);
            $ret   = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_ADDFIELD_ERR, $alteredField['name'], $this->name()) . '</li>';
            } else {
                echo "<li class='ok'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_ADDFIELD, $alteredField['name'], $this->name()) . '</li>';
            }
        }

        return $ret;
    }

    /**
     * Use to update fields values
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function updateFieldsValues()
    {
        global $xoopsDB;

        $ret = true;

        foreach ($this->getUpdatedFields() as $updatedField) {
            $query = sprintf('UPDATE %s SET %s = %s', $this->name(), $updatedField['name'], $updatedField['value']);
            $ret   = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_UPDATE_TABLE_ERR, $this->name()) . '</li>';
            } else {
                echo "<li class='ok'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_UPDATE_TABLE, $this->name()) . '</li>';
            }
        }

        return $ret;
    }

    /**
     * Use to drop fields
     *
     * @return bool true if success, false if an error occured
     *
     */
    public function dropFields()
    {
        global $xoopsDB;

        $ret = true;

        foreach ($this->getDropedFields() as $dropedField) {
            $query = sprintf('ALTER TABLE %s DROP %s', $this->name(), $dropedField);
            $ret   = $ret && $xoopsDB->query($query);
            if (!$ret) {
                echo "<li class='err'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_DROPFIELD_ERR, $dropedField, $this->name()) . '</li>';
            } else {
                echo "<li class='ok'>" . sprintf(_AM_SMARTMEDIA_DB_MSG_DROPFIELD, $dropedField, $this->name()) . '</li>';
            }
        }

        return $ret;
    }
}
