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
 * Restrictive HTML filter
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_View_Filter_SafeHtml implements Zend_Filter_Interface
{
  protected $_allowed;

  public function __construct($allowed = 'p,a[href],strong,em,blockquote[cite],q[cite],abbr,acronym,cite,dfn,ul,ol,li,dl,dt,dd,del,ins,sub,sup')
  {
    $this->setAllowed($allowed);
  }

  public function getAllowed()
  {
    return $this->_allowed;
  }

  public function setAllowed($allowed)
  {
    $this->_allowed = $allowed;
    return $this;
  }

  public function filter($value)
  {
    $filterChain = new Zend_Filter();
    
    $htmlFilter = new Content_View_Filter_HtmlPurifier();
    $config = $htmlFilter->getConfig();
    $config->set('AutoFormat', 'AutoParagraph', true);
    $config->set('AutoFormat', 'Linkify', true);
    $allowed = $this->getAllowed();
    if (null !== $allowed) {
      $config->set('HTML', 'Allowed', $allowed);
    }
    $filterChain->addFilter($htmlFilter);

    $geshiFilter = new Content_View_Filter_Geshi();
    $filterChain->addFilter($geshiFilter);

    return $filterChain->filter($value);
  }
}
