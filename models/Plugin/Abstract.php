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
 * Abstract model plugin class
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
abstract class Content_Model_Plugin_Abstract implements Content_Model_Plugin_Interface
{
  protected $_bootstrap;

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
}
