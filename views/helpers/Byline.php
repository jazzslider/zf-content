<?php

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
