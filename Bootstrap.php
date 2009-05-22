<?php

class Content_Bootstrap extends Zend_Application_Module_Bootstrap
{
  protected function _initContentplugins()
  {
    return new Content_Model_Plugins();
  }

  protected function _initMapperplugins()
  {
    return new Content_Model_Plugins();
  }

  protected function _initPostmapper()
  {
    return new Content_Model_Mapper_Posts($this->getApplication());
  }

  protected function _initRevisionmapper()
  {
    return new Content_Model_Mapper_Revisions($this->getApplication());
  }
}
