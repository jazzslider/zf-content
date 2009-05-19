<?php

class Content_Model_Plugins implements Iterator, ArrayAccess, Countable
{
  protected $_plugins = array();
  protected $_current = 0;

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

  public function register($plugin)
  {
    if (!is_object($plugin)) {
      $plugin = new $plugin($this->getBootstrap());
    }
    if (!($plugin instanceof Content_Model_Plugin_Interface)) {
      throw new Exception('invalid plugin');
    }
    $this->_plugins[get_class($plugin)] = $plugin;
    return $this;
  }

  public function unregister($plugin)
  {
    if (is_object($plugin)) {
      $plugin = get_class($plugin);
    }
    if (array_key_exists($plugin, $this->_plugins)) {
      unset($this->_plugins[$plugin]);
    }
    return $this;
  }

  public function current()
  {
    return $this->_plugins[$this->key()];
  }

  public function key()
  {
    $keys = array_keys($this->_plugins);
    return $keys[$this->_current];
  }

  public function next()
  {
    $this->_current++;
  }

  public function rewind()
  {
    $this->_current = 0;
  }

  public function valid()
  {
    $keys = array_keys($this->_plugins);
    return array_key_exists($this->_current, $keys);
  }

  public function offsetExists($offset)
  {
    return array_key_exists($offset, $this->_plugins);
  }

  public function offsetGet($offset)
  {
    if ($this->offsetExists($offset)) {
      return $this->_plugins[$offset];
    }
    return null;
  }

  public function offsetSet($offset, $value)
  {
    if (!is_object($value) || $offset !== get_class($value)) {
      throw new Exception('invalid');
    }
    $this->register($value);
  }

  public function offsetUnset($offset)
  {
    $this->unregister($offset);
  }

  public function count()
  {
    return count($this->_plugins);
  }
}
