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

    // TODO this approach to getting the bootstrapper is actually
    // pretty lame; better to pass it in as a dependency, but I'd
    // really like to avoid that since this is just a basic Zend_Form
    // child class...so for now, this should do
    $installedFilters = array();
    $frontController = Zend_Controller_Front::getInstance();
    $bootstrap = $frontController->getParam('bootstrap');
    $options = $bootstrap->getOptions();
    if (array_key_exists('content', $options) && array_key_exists('outputFilters', $options['content'])) {
      $filtersInConfig = $options['content']['outputFilters'];
      foreach ($filtersInConfig as $filterKey => $filterClass) {
        $installedFilters[$filterClass] = $filterClass;
      }
      $this->addElement('radio', 'bodyFilter');
      $this->bodyFilter->setLabel('Body output filter')
                       ->setRequired(false)
                       ->setMultiOptions($installedFilters)
                       ->setValidators(array(
                         array('InArray', false, array(array_keys($installedFilters))),
                       ));
    }

    $this->addElement('submit', 'submitBtn');
    $this->submitBtn->setLabel('Submit')
                    ->setOrder(100000);
  }
}
