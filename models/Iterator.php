<?php
/**
 * Distributable content module for Zend Framework
 *
 * This module is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <{@link http://www.gnu.org/licenses/}>.
 *
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */

/**
 * Model iterator
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
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
