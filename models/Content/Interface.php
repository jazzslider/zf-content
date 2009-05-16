<?php

interface Content_Model_Content_Interface
{
  public function __construct($data, 
                              Zend_Application_Bootstrap_Bootstrapper $bootstrap);
  public function init();

  public function populate($data);
  public function toArray();

  public function getBootstrap();
  public function setBootstrap(Zend_Application_Bootstrap_Bootstrapper $bootstrap);

  public function __get($property);
  public function __set($property, $value);
  public function __isset($property);
  public function __unset($property);

  public function save();
  public function delete();

  public function getId();
  public function getClass();

  public function getForm();

  /**
   * @return Zend_Form
   */
  public function populateForm(Zend_Form $form);

  /**
   * @return Content_Model_Content_Interface provides a fluent interface
   */
  public function populateFromForm(Zend_Form $form);
}
