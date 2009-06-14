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
 * Delete post form
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_Form_DeletePost extends Zend_Form
{
  public function init()
  {
    $this->addElement('radio', 'choice');
    $this->choice->setLabel('Are you sure?')
                 ->setRequired(true)
                 ->setMultiOptions(array(
                   'Yes' => 'Yes',
                   'No'  => 'No',
                 ))
                 ->setValidators(array(
                   array('InArray', true, array(array('Yes', 'No'))),
                 ));

    $this->addElement('submit', 'submitBtn');
    $this->submitBtn->setLabel('Submit')
                    ->setOrder(10000);
  }
}
