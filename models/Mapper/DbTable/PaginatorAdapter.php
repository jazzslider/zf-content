<?php

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
