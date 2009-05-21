<?php

class Content_Model_Mapper_Revisions extends Content_Model_Mapper_DbTable_Abstract
{
  protected $_modelClass = 'Content_Model_Revision';
  protected $_tableClass = 'Content_Model_DbTable_Revisions';

  public function findCurrent(Content_Model_Post $post)
  {
    if (null === $post->id) {
      return null;
    }

    $select = $this->getTable()->select();
    $select->where('post = ?', $post->id);
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

  public function findAllByPost(Content_Model_Post $post, Zend_Db_Table_Select $select = null)
  {
    if (null === $select) {
      $select = $this->getSelect();
    }
    $select->where('post = ?', $post->id);
    return $this->findAll($select);
  }
}
