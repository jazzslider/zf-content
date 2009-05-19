<?php

interface Content_Model_Plugin_Interface
{
  public function __construct(Zend_Application_Bootstrap_Bootstrapper $bootstrap);

  public function getBootstrap();
  public function setBootstrap(Zend_Application_Bootstrap_Bootstrapper $bootstrap);
}
