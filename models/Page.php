<?php

class Content_Model_Page extends Content_Model_Content_Abstract
{
  protected $_data = array(
    'id'        => null,
    'slug'      => null,
    'status'    => null,
    'published' => null,
  );

  protected $_formClass = 'Content_Form_Page';
}
