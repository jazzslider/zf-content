<?php

class Content_Model_Mapper_Revisions extends Content_Model_Mapper_DbTable_Abstract
{
  protected $_modelClass = 'Content_Model_Revision';
  protected $_tableClass = 'Content_Model_DbTable_Revisions';

  public function findCurrent(Content_Model_Content_Interface $model)
  {
    if (null === $model->id) {
      return null;
    }

    // TODO figure this out...I'd like to be able to offer revision control
    // for models other than Content_Model_Page, but without a centralized
    // ID table for everything implementing Content_Model_Content_Interface,
    // that's going to be really difficult
    $select = $this->getTable()->select();
    $select->where('model = ?', $model->id);
    $select->order('(active = 1) DESC');
    $select->order('created DESC');
    $select->limit(1);

    $revisionRow = $this->getTable()->fetchRow($select);
    if (null === $revisionRow) {
      return null;
    }

    if (!array_key_exists($revisionRow->id, $this->_rows)) {
      $this->_rows[$revisionRow->id] = $revisionRow;
    }
    if (!array_key_exists($revisionRow->id, $this->_models)) {
      $this->_load($revisionRow->id);
    }
    return $this->_models[$revisionRow->id];
  }

  public function findAllByModel(Content_Model_Content_Interface $model, Zend_Db_Table_Select $select = null)
  {
    if (null === $select) {
      $select = $this->getSelect();
    }
    $select->where('model = ?', $model->id);
    return $this->findAll($select);
  }
}
