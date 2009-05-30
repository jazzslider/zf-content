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
 * Interface for content model classes
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
interface Content_Model_Content_Interface
{
  public function __construct($data, 
                              Zend_Application_Bootstrap_Bootstrapper $bootstrap);
  public function preInit();
  public function postInit();

  public function populate($data);
  public function toArray();

  public function getBootstrap();
  public function setBootstrap(Zend_Application_Bootstrap_Bootstrapper $bootstrap);

  public function __get($property);
  public function __set($property, $value);
  public function __isset($property);
  public function __unset($property);

  public function save();
  public function delete();

  public function getForm();

  /**
   * @return Zend_Form
   */
  public function populateForm(Zend_Form $form);

  /**
   * @return Content_Model_Content_Interface provides a fluent interface
   */
  public function populateFromForm(Zend_Form $form);
}
