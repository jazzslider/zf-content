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
 * Filter implementing HTMLPurifier processing
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_View_Filter_HtmlPurifier implements Zend_Filter_Interface
{
  protected $_config;
  protected $_purifier;

  public function __construct()
  {
    require_once 'HTMLPurifier/HTMLPurifier.auto.php';
  }

  public function filter($value)
  {
    $value = $this->getPurifier()->purify($value);
    return $value;
  }

  public function getConfig()
  {
    if (null === $this->_config) {
      $this->setConfig(HTMLPurifier_Config::createDefault());
    }
    return $this->_config;
  }

  public function setConfig(HTMLPurifier_Config $config)
  {
    $this->_config = $config;
    $this->_purifier = null;
    return $this;
  }

  public function getPurifier()
  {
    if (null === $this->_purifier) {
      $this->setPurifier(new HTMLPurifier($this->getConfig()));
    }
    return $this->_purifier;
  }

  public function setPurifier(HTMLPurifier $purifier)
  {
    $this->_purifier = $purifier;
    return $this;
  }
}
