<?php

class Content_Model_Iterator implements Iterator, Countable
{
  protected $_mapper;
  protected $_models = array();
  protected $_bootstrap;

  public function __construct(Content_Model_Mapper_Interface $mapper,
                              array $models,
                              Zend_Application_Bootstrap_Bootstrapper $bootstrap)
  {
    $this->setMapper($mapper);
    $this->_models = $models;
    $this->setBootstrap($bootstrap);
  }

  public function getMapper()
  {
    return $this->_mapper;
  }

  public function setMapper(Content_Model_Mapper_Interface $mapper)
  {
    $this->_mapper = $mapper;
    return $this;
  }

  public function getBootstrap()
  {
    return $this->_bootstrap;
  }

  public function setBootstrap(Zend_Application_Bootstrap_Bootstrapper $bootstrap)
  {
    $this->_bootstrap = $bootstrap;
    return $this;
  }

  public function count()
  {
    return count($this->_models);
  }

  public function current()
  {
    $model = current($this->_models);
    if (!($model instanceof Content_Model_Content_Interface)) {
      $model = $this->getMapper()->find($model);
      if (null === $model) {
        throw new Exception('One of the model IDs in this iterator is no longer available.');
      }
      $this->_models[key($this->_models)] = $model;
    }
    return current($this->_models);
  }

  public function key()
  {
    return key($this->_models);
  }

  public function next()
  {
    next($this->_models);
  }

  public function rewind()
  {
    reset($this->_models);
  }

  public function valid()
  {
    return (current($this->_models) !== false);
  }
}
