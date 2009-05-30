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
 * Module bootstrap
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_Bootstrap extends Zend_Application_Module_Bootstrap
{
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
