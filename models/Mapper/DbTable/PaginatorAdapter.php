<?php
/**
 * Distributable content module for Zend Framework
 *
 * This module is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <{@link http://www.gnu.org/licenses/}>.
 *
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */

/**
 * Paginator adapter for use with the database table mapper
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_Model_Mapper_DbTable_PaginatorAdapter implements Zend_Paginator_Adapter_Interface
{
  protected $_select;
  protected $_internalAdapter;
  protected $_mapper;

  public function __construct(Zend_Db_Table_Select $select, Content_Model_Mapper_DbTable_Abstract $mapper)
  {
    $this->setSelect($select);
    $this->setMapper($mapper);
  }

  public function setSelect(Zend_Db_Table_Select $select)
  {
    $this->_select = $select;
    $this->_internalAdapter = new Zend_Paginator_Adapter_DbTableSelect($select);
    return $this;
  }

  public function setMapper(Content_Model_Mapper_DbTable_Abstract $mapper)
  {
    $this->_mapper = $mapper;
    return $this;
  }

  public function count()
  {
    return $this->_internalAdapter->count();
  }

  public function getItems($offset, $itemCountPerPage)
  {
    $rows = $this->_internalAdapter->getItems($offset, $itemCountPerPage);
    return $this->_mapper->loadFromRows($rows);
  }
}
