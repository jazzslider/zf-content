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

  public function save(Content_Model_Content_Interface $revision)
  {
    if (!($revision instanceof Content_Model_Revision)) {
      throw new Exception('this mapper can only save revisions');
    }

    $this->_preSave($revision);

    if (null === $revision->id) {
      $revision->active = true;
      $revisionRow = $this->getTable()->createRow();
    } else {
      $revisionRow = $this->findRow($revision->id);
    }

    $revisionRow->post = $revision->post->id;
    $revisionRow->active = (integer)$revision->active;
    if (trim($revision->title) <> '') {
      $revisionRow->title = $revision->title;
    } else {
      $revisionRow->title = null;
    }
    if (trim($revision->body) <> '') {
      $revisionRow->body = $revision->body;
    } else {
      $revisionRow->body = null;
    }
    if (trim($revision->bodyFilter) <> '') {
      $revisionRow->bodyFilter = $revision->bodyFilter;
    } else {
      $revisionRow->bodyFilter = null;
    }
    if (null === $revision->created) {
      $revision->created = new Zend_Date();
    }
    $revisionRow->created = $revision->created->get(Zend_Date::ISO_8601);

    $revisionRow->save();
    $revision->id = $revisionRow->id;

    $this->_postSave($revision);

    $this->_models[$revision->id] = $revision;
    $this->_rows[$revisionRow->id] = $revisionRow;

    if ($revision->active) {
      $this->getBootstrap()->bootstrap('db');
      $db = $this->getBootstrap()->getResource('db');
      $firstPart = $db->quoteInto('post = ?', $revision->post->id);
      $secondPart = $db->quoteInto('id <> ?', $revision->id);
      $where = "$firstPart AND $secondPart";
      $this->getTable()->update(array('active' => 0), $where);
      $revision->post->setCurrentRevision($revision);
    }

    return $this->_models[$revision->id];
  }

  public function getSelect()
  {
    $select = $this->getTable()->select();
    $select->from(array('r' => 'content_revisions'));
    return $select;
  }
}
