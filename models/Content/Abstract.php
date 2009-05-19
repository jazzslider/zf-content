<?php

abstract class Content_Model_Content_Abstract implements Content_Model_Content_Interface
{
  /**
   * Internal data array
   *
   * @var array
   */
  protected $_data = array();

  /**
   * Form class
   *
   * @var string
   */
  protected $_formClass;

  /**
   * Form instance
   *
   * @var Zend_Form|null
   */
  protected $_form;

  /**
   * Bootstrap
   *
   * @var Zend_Application_Bootstrap_Bootstrapper
   */
  protected $_bootstrap;

  /**
   * Constructor
   *
   * @param Zend_Db_Table_Row_Abstract|stdClass|array $data data collection
   *  which will be passed to {@link populate()}
   * @param Zend_Application_Bootstrap_Bootstrapper $bootstrap
   */
  public function __construct($data,
                              Zend_Application_Bootstrap_Bootstrapper $bootstrap)
  {
    $this->setBootstrap($bootstrap);
    $this->populate($data);
    $this->init();
  }

  public function init()
  {
  }

  /**
   * Populate
   *
   * Populates the model with the provided data.
   * @param Zend_Db_Table_Row_Abstract|stdClass|array $data data collection
   * @return Content_Model_Content_Interface provides a fluent interface
   */
  public function populate($data)
  {
    if ($data instanceof Zend_Db_Table_Row_Abstract) {
      $data = $data->toArray();
    } else if (is_object($data)) {
      $data = (array)$data;
    }

    if (!is_array($data)) {
      throw new Exception('Initial data must be array or object');
    }

    if (array_key_exists('class', $data)) {
      unset($data['class']);
    }

    foreach ($data as $key => $value) {
      $this->$key = $value;
    }

    return $this;
  }

  /**
   * To Array
   *
   * @return array all the object's properties, as returned by
   *  {@link __get()}
   */
  public function toArray()
  {
    $array = array();
    foreach ($this->_data as $property => $value) {
      $array[$property] = $this->$property;
    }
    return $array;
  }

  public function getBootstrap()
  {
    return $this->_bootstrap;
  }

  public function setBootstrap(Zend_Application_Bootstrap_Bootstrapper $bootstrap)
  {
    $this->_bootstrap = $bootstrap;
    return $this;
  }

  /**
   * Get internal data property
   *
   * Allows property-style access to this model's data by one of several
   * means.  If there is a get{property} method, its return value is returned;
   * if $property is available in the {@link $_data} array, that value is
   * returned; otherwise, null is returned.
   * @param string $property name of the property
   * @return mixed value of the property
   */
  public function __get($property)
  {
    $method = 'get' . ucfirst($property);
    if (method_exists($this, $method)) {
      return $this->$method();
    }
    if (array_key_exists($property, $this->_data)) {
      return $this->_data[$property];
    }
    return null;
  }

  /**
   * Set internal data property
   *
   * Allows property-style mutation of this model's data by one of several
   * means: setX() methods and the {@link $_data} array.  All data is
   * validated through the default form object if possible before any
   * other mutation method is used.
   * @param string $property name of the field
   * @param mixed $value value of the field
   * @return mixed not really useful
   * @throws Exception if provided value is not valid
   */
  public function __set($property, $value)
  {
    // first, we use the form object to standardize validation
    $inputFilter = $this->getForm();
    if ($element = $inputFilter->getElement($property)) {
      if (!$element->isValid($value)) {
        throw new Exception(sprintf('Invalid value provided for "%s": %s',
                                    $name,
                                    implode(', ', $element->getMessages()))
        );
      }
    }

    // then, we allow child classes to override the default
    // set policy, just in case the form filter isn't enough
    $method = 'set' . ucfirst($property);
    if (method_exists($this, $method)) {
      return $this->$method($value);
    }

    // if that didn't work, we attempt to set it in the
    // data array
    if (array_key_exists($property, $this->_data)) {
      $this->_data[$property] = $value;
      return $this;
    }

    // if that didn't work, we just silently fail; most
    // likely the property was being set as part of an
    // array of values that weren't all intended for here
    return $this;
  }

  /**
   * Isset
   *
   * Checks to see if a particular property has a value.  Uses
   * xIsset() methods first, otherwise checks the {@link $_data}
   * array.
   * @param string $property name of the property
   * @return boolean
   */
  public function __isset($property)
  {
    $method = $property . 'Isset';
    if (method_exists($this, $method)) {
      return $this->$method();
    }
    if (array_key_exists($property, $this->_data)) {
      return (null !== $this->_data[$property]);
    }
    return false;
  }

  /**
   * Unset
   *
   * Unsets the value of a particular property.  Uses
   * unsetX() methods first, otherwise sets {@link $_data}
   * element to null.
   * @param string $property name of the property
   * @return mixed not really useful
   */
  public function __unset($property)
  {
    $method = 'unset' . ucfirst($property);
    if (method_exists($this, $method)) {
      return $this->$method();
    }
    if (array_key_exists($property, $this->_data)) {
      $this->_data[$property] = null;
      return $this;
    }
    return $this;
  }

  /**
   * Get Form
   *
   * Retrieves this model's associated form instance.
   * @return Zend_Form
   * @throws Exception if this model does not have a form class defined
   */
  public function getForm()
  {
    if (null === $this->_form) {
      if (null === $this->_formClass) {
        throw new Exception('No form class');
      }
      $formClass = $this->_formClass;
      $this->_form = new $formClass;
      if (method_exists($this->_form, 'setBootstrap')) {
        $this->_form->setBootstrap($this->getBootstrap());
      }
      $this->_form = $this->_postGetForm();
    }
    return $this->_form;
  }

  protected function _postGetForm()
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($this instanceof $modelClass) {
        $this->_form = $plugin->postGetForm($this, $this->_form);
      }
    }
    return $this->_form;
  }

  public function populateForm(Zend_Form $form)
  {
    foreach ($form->getElements() as $element) {
      if ($this->__isset($element->getName())) {
        $element->setValue($this->__get($element->getName()));
      }
    }
    $form = $this->_postPopulateForm($form);
    return $form;
  }

  protected function _postPopulateForm($form)
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($this instanceof $modelClass) {
        $form = $plugin->postPopulateForm($this, $form);
      }
    }
    return $form;
  }

  public function populateFromForm(Zend_Form $form)
  {
    foreach ($form->getElements() as $element) {
      if ($this->__isset($element->getName())) {
        $this->__set($element->getName(), $element->getValue());
      }
    }
    $this->_postPopulateFromForm($form);
    return $this;
  }

  protected function _postPopulateFromForm($form)
  {
    foreach ($this->getPlugins() as $plugin) {
      $modelClass = $plugin->getModelClass();
      if ($this instanceof $modelClass) {
        $plugin->postPopulateFromForm($this, $form);
      }
    }
    return $this;
  }

  public function getPlugins()
  {
    $resource = $this->getBootstrap()->getPluginResource('modules');
    $moduleBootstraps = $resource->getExecutedBootstraps();
    $moduleBootstrap = $moduleBootstraps['content'];
    $moduleBootstrap->bootstrap('contentplugins');
    return $moduleBootstrap->getResource('contentplugins');
  }
}
