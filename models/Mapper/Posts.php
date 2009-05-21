<?php

class Content_Model_Mapper_Posts extends Content_Model_Mapper_DbTable_Abstract
{
  protected $_modelClass = 'Content_Model_Post';
  protected $_tableClass = 'Content_Model_DbTable_Posts';

  public function save(Content_Model_Content_Interface $post)
  {
    if (!($post instanceof Content_Model_Post)) {
      throw new Exception('can only save posts via this mapper');
    }

    $this->_preSave($post);

    if (null === $post->id) {
      $postRow = $this->getTable()->createRow();
    } else {
      $postRow = $this->findRow($post->id);
    }

    $postRow->class = get_class($post);
    if (trim($post->slug) <> '') {
      $postRow->slug = $post->slug;
    } else {
      $postRow->slug = null;
    }
    if (0 !== $post->status && trim($post->status) <> '') {
      $postRow->status = $post->status;
    } else {
      $postRow->status = Content_Model_Post::STATUS_PUBLISHED;
    }
    if (null === $post->published) {
      $post->published = new Zend_Date();
    }
    $postRow->published = $post->published->get(Zend_Date::ISO_8601);

    $postRow->save();
    $post->id = $postRow->id;

    $revision = $post->getNewRevision(false);
    if (null !== $revision) {
      $revision->save();
    }

    $this->_postSave($post);

    $this->_models[$post->id] = $post;
    $this->_rows[$postRow->id] = $postRow;

    return $this->_models[$post->id];
  }
}
