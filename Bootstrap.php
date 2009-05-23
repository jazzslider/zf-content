<?php

class Content_Bootstrap extends Zend_Application_Module_Bootstrap
{
  protected function _initConfig()
  {
    $appBootstrap = $this->getApplication();
    $config       = new Zend_Config_Ini(
      $this->getResourceLoader()->getBasePath() . '/config/content.ini',
      $appBootstrap->getEnvironment()
    );
    $appBootstrap->setOptions($config->toArray());
    return $this;
  }

  protected function _initContentplugins()
  {
    return new Content_Model_Plugins($this->getApplication());
  }

  protected function _initMapperplugins()
  {
    return new Content_Model_Plugins($this->getApplication());
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
