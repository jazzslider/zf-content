<?php

class Content_Model_Page extends Content_Model_Content_Abstract
{
  protected $_formClass = 'Content_Form_Page';

  const STATUS_PUBLISHED = 1;
  const STATUS_DRAFT     = 0;

  public function preInit()
  {
    $this->_data = array_merge($this->_data, array(
      'slug'      => null,
      'status'    => null,
    ));
  }

  public function setStatus($status)
  {
    if (!in_array($status, array(self::STATUS_PUBLISHED, self::STATUS_DRAFT))) {
      throw new Exception('invalid status');
    }
    $this->_data['status'] = $status;
    return $this;
  }
}
