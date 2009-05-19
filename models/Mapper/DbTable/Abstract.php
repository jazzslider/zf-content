<?php

abstract class Content_Model_Mapper_DbTable_Abstract implements Content_Model_Mapper_Interface
{
  /**
   * @var Zend_Application_Bootstrap_Bootstrapper
   */
  protected $_bootstrap;

  /**
   * @var array
   */
  protected $_models;

  /**
   * @var array
   */
  protected $_rows;

  /**
   * @var Zend_Db_Table_Abstract
   */
  protected $_table;

  /**
   * @var string
   */
  protected $_iteratorClass;

  /**
   * @var string
   */
  protected $_modelClass;

  /**
   * @var string
   */
  protected $_tableClass;

  public function __construct(Zend_Application_Bootstrap_Bootstrapper $bootstrap)
  {
    $this->setBootstrap($bootstrap);
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

  public function find($id)
  {
    if (!array_key_exists($id, $this->_models)) {
      $this->_load($id);
    }
    return $this->_models[$id];
  }

  public function findRow($id)
  {
    if (!array_key_exists($id, $this->_rows)) {
      $this->_rows[$id] = $this->getTable()->find($id)->current();
    }
    return $this->_rows[$id];
  }

  public function findAll($select = null)
  {
    if (null === $select) {
      $select = $this->getSelect();
    } else {
      if (!($select instanceof Zend_Db_Table_Select)) {
        throw new Exception('invalid argument');
      }
    }

    $rowset = $this->getTable()->fetchAll($select);
    $models = array();
    foreach ($rowset as $row) {
      if (!array_key_exists($row->id, $this->_rows)) {
        $this->_rows[$row->id] = $row;
      }
      if (array_key_exists($row->id, $this->_models)) {
        $models[] = $this->_models[$row->id];
      } else {
        if (null !== $this->_iteratorclass) {
          $models[] = $row->id;
        } else {
          $this->_load($row->id);
          $models[] = $this->find($row->id);
        }
      }
    }

    if (null !== $this->_iteratorClass) {
      $iteratorClass = $this->_iteratorClass;
      $iterator = new $iteratorClass($models, $this->getBootstrap());
    } else {
      $iterator = $models;
    }
    return $iterator;
  }

  public function loadFromRows(Zend_Db_Table_Rowset_Abstract $rowset)
  {
    $models = array();
    foreach ($rowset as $row) {
      if (!array_key_exists($row->id, $this->_rows)) {
        $this->_rows[$row->id] = $row;
      }
      if (!array_key_exists($row->id, $this->_models)) {
        $this->_load($row->id);
      }
      $models[] = $this->_models[$row->id];
    }
    return $models;
  }

  public function findPaginator($select = null)
  {
    if (null === $select) {
      $select = $this->getSelect();
    } else {
      if (!($select instanceof Zend_Db_Table_Select)) {
        throw new Exception('invalid argument');
      }
    }

    return new Zend_Paginator(new Content_Model_Mapper_DbTable_PaginatorAdapter($select, $this));
  }

  abstract public function getSelect();

  public function getTable()
  {
    if (null === $this->_table) {
      $tableClass = $this->_tableClass;
      if (null === $tableClass) {
        throw new Exception('must define table class');
      }
      $this->setTable(new $tableClass());
    }
    return $this->_table;
  }

  public function setTable(Zend_Db_Table_Abstract $table)
  {
    if ($table !== $this->_table) {
      $this->_rows = array();
    }
    $this->_table = $table;
    return $this;
  }

  /**
   * @return void
   */
  protected function _load($id)
  {
    $row = $this->findRow($id);
    if (null === $row) {
      $this->_models[$id] = null;
      return;
    }

    $modelClass = $this->_modelClass;
    if (null === $modelClass) {
      throw new Exception('must define a model class');
    }
    $this->_models[$id] = new $modelClass($row, $this->getBootstrap());
    $this->_postLoad($this->_models[$id]);

    return;
  }

  public function getPlugins()
  {
    $resource = $this->getBootstrap()->getPluginResource('modules');
    $moduleBootstraps = $resource->getExecutedBootstraps();
    $moduleBootstrap = $moduleBootstraps['content'];
    $moduleBootstrap->bootstrap('mapperplugins');
    return $moduleBootstrap->getResource('mapperplugins');
  }

  protected function _postLoad(Content_Model_Content_Interface $model)
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($model instanceof $modelClass) {
        $plugin->postLoad($model);
      }
    }
  }

  protected function _preSave(Content_Model_Content_Interface $model)
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($model instanceof $modelClass) {
        $plugin->preSave($model);
      }
    }
  }

  protected function _postSave(Content_Model_Content_Interface $model)
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($model instanceof $modelClass) {
        $plugin->postSave($model);
      }
    }
  }

  protected function _preDelete(Content_Model_Content_Interface $model)
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($model instanceof $modelClass) {
        $plugin->preDelete($model);
      }
    }
  }

  protected function _postDelete(Content_Model_Content_Interface $model)
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($model instanceof $modelClass) {
        $plugin->postDelete($model);
      }
    }
  }
}