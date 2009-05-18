<?php

interface Content_Model_Mapper_Interface
{
  public function __construct(Zend_Application_Bootstrap_Bootstrapper $bootstrap);

  public function getBootstrap();
  public function setBootstrap(Zend_Application_Bootstrap_Bootstrapper $bootstrap);

  public function find($id);
  public function findAll($criteria = null);
  public function findPaginator($criteria = null);

  public function save(Content_Model_Content_Interface $content);
  public function delete(Content_Model_Content_Interface $content);
}
