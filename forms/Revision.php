<?php

class Content_Form_Revision extends Zend_Form
{
  public function init()
  {
    $this->addElement('text', 'title');
    $this->title->setLabel('Title')
                ->setRequired(true)
                ->setFilters(array('StringTrim', 'StripTags'))
                ->setValidators(array(
                  array('StringLength', false, array(1, 255)),
                ));

    $this->addElement('textarea', 'body');
    $this->body->setLabel('Body')
               ->setRequired(false)
               ->setFilters(array('StringTrim'));

    // TODO bodyFilter selector...without it, this won't work

    $this->addElement('submit', 'submitBtn');
    $this->submitBtn->setLabel('Submit')
                    ->setOrder(100000);
  }
}
