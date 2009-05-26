<?php

class Content_View_Filter_Markdown implements Zend_Filter_Interface
{
  public function filter($value)
  {
    $filterChain = new Zend_Filter();

    $geshiFilter = new Content_View_Filter_Geshi();
    $filterChain->addFilter($geshiFilter);

    $value = $filterChain->filter($value);

    require_once 'markdown/markdown.php';
    return Markdown($value);
  }
}
