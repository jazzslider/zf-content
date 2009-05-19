<?php

interface Content_Model_Mapper_Plugin_Interface
{
  public function getModelClass();

  public function postLoad(Content_Model_Content_Interface $model);
  public function preSave(Content_Model_Content_Interface $model);
  public function postSave(Content_Model_Content_Interface $model);
  public function preDelete(Content_Model_Content_Interface $model);
  public function postDelete(Content_Model_Content_Interface $model);
}
