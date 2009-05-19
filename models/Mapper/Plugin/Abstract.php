<?php

abstract class Content_Model_Mapper_Plugin_Abstract extends Content_Model_Plugin_Abstract
                                                    implements Content_Model_Mapper_Plugin_Interface
{
  protected $_modelClass = 'Content_Model_Content_Interface';

  public function getModelClass()
  {
    return $this->_modelClass;
  }

  public function postLoad(Content_Model_Content_Interface $model)
  {
  }

  public function preSave(Content_Model_Content_Interface $model)
  {
  }

  public function postSave(Content_Model_Content_Interface $model)
  {
  }

  public function preDelete(Content_Model_Content_Interface $model)
  {
  }

  public function postDelete(Content_Model_Content_Interface $model)
  {
  }
}
