<?php

class Content_Model_Revision extends Content_Model_Content_Abstract
{
  protected $_formClass = 'Content_Form_Revision';

  public function preInit()
  {
    $this->_data = array_merge($this->_data, array(
      'id'         => null,
      'post'       => null,
      'active'     => null,
      'title'      => null,
      'body'       => null,
      'bodyFilter' => null,
      'created'    => null,
    ));
  }

  public function getPostMapper()
  {
    $resource = $this->getBootstrap()->getPluginResource('modules');
    $moduleBootstraps = $resource->getExecutedBootstraps();
    $moduleBootstrap = $moduleBootstraps['content'];
    $moduleBootstrap->bootstrap('postmapper');
    return $moduleBootstrap->getResource('postmapper');
  }

  public function getRevisionMapper()
  {
    $resource = $this->getBootstrap()->getPluginResource('modules');
    $moduleBootstraps = $resource->getExecutedBootstraps();
    $moduleBootstrap = $moduleBootstraps['content'];
    $moduleBootstrap->bootstrap('revisionmapper');
    return $moduleBootstrap->getResource('revisionmapper');
  }

  public function setPost($post)
  {
    if (!($post instanceof Content_Model_Post)) {
      // default is to get a Content_Model_Post instance using the argument
      // as an ID
      $post = $this->getPostMapper()->find($post);
      if (!($post instanceof Content_Model_Post)) {
        throw new Exception('data integrity error');
      }
    }
    $this->_data['post'] = $post;
    return $this;
  }

  public function setActive($active)
  {
    $this->_data['active'] = (boolean)$active;
    return $this;
  }

  public function getTeaser()
  {
    // TODO logic for shaving off all but the first bit, or possibly add a
    // data field
    return $this->body;
  }

  /**
   * Set Created
   *
   * Sets the creation date of this revision.
   * @param Zend_Date|string $date
   * @return Content_Model_Revision provides a fluent interface
   * @throws Exception when date cannot be parsed into a 
   *                   Zend_Date object
   */
  public function setCreated($date)
  {
    if (!($date instanceof Zend_Date)) {
      if (null !== $date && !is_string($date)) {
        throw new Exception('invalid date format');
      }
      try {
        $date = new Zend_Date($date, Zend_Date::ISO_8601);
      } catch (Zend_Date_Exception $e) {
        throw new Exception('invalid date format');
      }
    }
    $this->_data['created'] = $date;
    return $this;
  }

  public function save()
  {
    $mapper = $this->getRevisionMapper();
    $mapper->save($this);
    return $this;
  }

  public function delete()
  {
    $mapper = $this->getRevisionMapper();
    $mapper->delete($this);
    return $this;
  }
}
