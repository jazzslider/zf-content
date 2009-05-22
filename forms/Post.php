<?php

class Content_Form_Post extends Zend_Form
{
  public function init()
  {
    $this->addElement('text', 'slug');
    $this->slug->setLabel('Slug')
               ->setDescription('A short, machine-readable name for this content; should only contain lowercase letters, numbers, and hyphens.')
               ->setRequired(false)
               ->setFilters(array('StringTrim', 'StripTags'))
               ->setValidators(array(
                 array('StringLength', true, array(1, 255)),
                 array('Regex', false, array('/^[a-z0-9\-]*$/')),
               ));

    $this->addElement('select', 'status');
    $this->status->setLabel('Status')
                 ->setRequired(true)
                 ->setMultiOptions(array(
                   Content_Model_Post::STATUS_PUBLISHED => 'Published',
                   Content_Model_Post::STATUS_DRAFT     => 'Draft',
                 ))
                 ->setValidators(array(
                   array('InArray', true, array(array(Content_Model_Post::STATUS_PUBLISHED, Content_Model_Post::STATUS_DRAFT))),
                 ));

    $this->addSubForm(new Content_Form_Revision(), 'revision');
    $this->revision->removeElement('submitBtn');

    $this->addElement('submit', 'submitBtn');
    $this->submitBtn->setLabel('Submit')
                    ->setOrder(10000);
  }
}
