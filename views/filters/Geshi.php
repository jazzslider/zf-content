<?php

require_once 'geshi/geshi.php';

class Content_View_Filter_Geshi implements Zend_Filter_Interface
{
  public function filter($value)
  {
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
