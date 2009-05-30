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
 * Filter implementing GeSHi syntax highlighting
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_View_Filter_Geshi implements Zend_Filter_Interface
{
  public function filter($value)
  {
    require_once 'geshi/geshi.php';
    $pattern = "~\[sourcecode language='([^']*)'\](.*)\[/sourcecode\]~msU";
    while (preg_match($pattern, $value, $matches)) {
      $language = $matches[1];
      $src      = $matches[2];
      $geshi = new GeSHi($src, $language);
      $geshi->set_overall_style('border:1px solid #ccc;padding:1em;', true);
      $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
      $geshi->set_line_style('background: #fcfcfc;');
      $highlighted = $geshi->parse_code();
      $value = str_replace($matches[0], $highlighted, $value);
    }

    return $value;
  }
}
