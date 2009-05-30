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
 * View helper generating a byline for a post model
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_View_Helper_Byline extends Zend_View_Helper_Abstract
{
  public function byline(Content_Model_Post $post, array $options = array())
  {
    $byline = '';
    $byline .= 'Posted ' . $post->published->get(Zend_Date::DATE_MEDIUM);
    if (array_key_exists('name', $options)) {
      $byline .= ' by ';
      if (array_key_exists('url', $options)) {
        $byline .= '<a href="' . $options['url'] . '">' . $options['name'] . '</a>';
      } else {
        $byline .= $options['name'];
      }
    }

    return $byline;
  }
}
