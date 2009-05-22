<?php

class Content_View_Helper_Filter extends Zend_View_Helper_Abstract
{
  public function filter($value, $filter)
  {
    if (null === $filter) {
      return $value;
    }
    if (!($filter instanceof Zend_Filter_Interface)) {
      $filter = new $filter();
    }
    return $filter->filter($value);
  }
}
