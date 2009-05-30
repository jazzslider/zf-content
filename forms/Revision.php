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
 * Revision form
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_Form_Revision extends Zend_Form
{
  public function init()
  {
    $this->addElement('text', 'title');
    $this->title->setLabel('Title')
                ->setRequired(true)
                ->setFilters(array('StringTrim', 'StripTags'))
                ->setValidators(array(
                  array('StringLength', false, array(1, 255)),
                ));

    $this->addElement('textarea', 'body');
    $this->body->setLabel('Body')
               ->setRequired(false)
               ->setFilters(array('StringTrim'));

    // TODO this approach to getting the bootstrapper is actually
    // pretty lame; better to pass it in as a dependency, but I'd
    // really like to avoid that since this is just a basic Zend_Form
    // child class...so for now, this should do
    $installedFilters = array();
    $frontController = Zend_Controller_Front::getInstance();
    $bootstrap = $frontController->getParam('bootstrap');
    $options = $bootstrap->getOptions();
    if (array_key_exists('content', $options) && array_key_exists('outputFilters', $options['content'])) {
      $filtersInConfig = $options['content']['outputFilters'];
      foreach ($filtersInConfig as $filterKey => $filterClass) {
        $installedFilters[$filterClass] = $filterClass;
      }
      $this->addElement('radio', 'bodyFilter');
      $this->bodyFilter->setLabel('Body output filter')
                       ->setRequired(false)
                       ->setMultiOptions($installedFilters)
                       ->setValidators(array(
                         array('InArray', false, array(array_keys($installedFilters))),
                       ));
    }

    $this->addElement('submit', 'submitBtn');
    $this->submitBtn->setLabel('Submit')
                    ->setOrder(100000);
  }
}
