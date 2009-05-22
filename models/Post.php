<?php

class Content_Model_Post extends Content_Model_Content_Abstract
{
  protected $_formClass = 'Content_Form_Post';

  protected $_revisionClass = 'Content_Model_Revision';
  protected $_currentRevision;
  protected $_newRevision;

  const STATUS_PUBLISHED = 1;
  const STATUS_DRAFT     = 0;

  public function preInit()
  {
    $this->_data = array_merge($this->_data, array(
      'id'        => null,
      'slug'      => null,
      'status'    => null,
      'published' => null,
      'revisions' => null,
    ));
  }

  public function getRevisionClass()
  {
    return $this->_revisionClass;
  }

  public function getRevisionMapper()
  {
    $resource = $this->getBootstrap()->getPluginResource('modules');
    $moduleBootstraps = $resource->getExecutedBootstraps();
    $moduleBootstrap = $moduleBootstraps['content'];
    $moduleBootstrap->bootstrap('revisionmapper');
    return $moduleBootstrap->getResource('revisionmapper');
  }

  public function getCurrentRevision()
  {
    if (null === $this->_currentRevision) {
      $foundCurrentRevision = $this->getRevisionMapper()->findCurrent($this);
      if (null !== $foundCurrentRevision) {
        $this->setCurrentRevision($foundCurrentRevision);
      }
    }
    return $this->_currentRevision;
  }

  public function setCurrentRevision(Content_Model_Revision $revision)
  {
    $this->_currentRevision = $revision;
    $this->_newRevision = null;
    return $this;
  }

  public function getNewRevision($createIfNull = true)
  {
    if (null === $this->_newRevision && $createIfNull) {
      $currentRevision = $this->getCurrentRevision();
      if (null !== $currentRevision) {
        $this->_newRevision = clone $currentRevision;
        unset($this->_newRevision->id);
        unset($this->_newRevision->created);
      } else {
        $this->_newRevision = new Content_Model_Revision(array(), $this->getBootstrap());
        $this->_newRevision->model = $this;
      }
    }
    return $this->_newRevision;
  }

  public function getRevisions()
  {
    if (null === $this->_data['revisions']) {
      $this->_data['revisions'] = $this->getRevisionMapper()->findAllByPost($this);
    }
    return $this->_data['revisions'];
  }

  public function getRevision($id)
  {
    $revision = $this->getRevisionMapper()->find($id);
    if (null !== $revision && $revision->model !== $this) {
      $revision = null;
    }
    return $revision;
  }

  public function setStatus($status)
  {
    if (!in_array($status, array(self::STATUS_PUBLISHED, self::STATUS_DRAFT))) {
      throw new Exception('invalid status');
    }
    $this->_data['status'] = $status;
    return $this;
  }

  public function setPublished($published)
  {
    if (!($published instanceof Zend_Date)) {
      $published = new Zend_Date($published, Zend_Date::ISO_8601);
    }
    $this->_data['published'] = $published;
    return $this;
  }

  public function getTitle()
  {
    return $this->_getFromRevision('title');
  }

  public function setTitle($title)
  {
    return $this->_setInRevision('title', $title);
  }

  public function unsetTitle()
  {
    return $this->_unsetInRevision('title');
  }

  public function titleIsset()
  {
    return $this->_issetInRevision('title');
  }

  public function getBody()
  {
    return $this->_getFromRevision('body');
  }

  public function setBody($body)
  {
    return $this->_setInRevision('body', $body);
  }

  public function unsetBody()
  {
    return $this->_unsetInRevision('body');
  }

  public function bodyIsset()
  {
    return $this->_issetInRevision('body');
  }

  public function getBodyFilter()
  {
    return $this->_getFromRevision('bodyFilter');
  }

  public function setBodyFilter($bodyFilter)
  {
    return $this->_setInRevision('bodyFilter', $bodyFilter);
  }

  public function unsetBodyFilter()
  {
    return $this->_unsetInRevision('bodyFilter');
  }

  public function bodyFilterIsset()
  {
    return $this->_issetInRevision('bodyFilter');
  }

  public function getTeaser()
  {
    return $this->_getFromRevision('teaser');
  }

  protected function _getFromRevision($property)
  {
    $newRevision = $this->getNewRevision(false);
    if (null !== $newRevision) {
      return $newRevision->$property;
    }
    $currentRevision = $this->getCurrentRevision();
    if (null !== $currentRevision) {
      return $currentRevision->$property;
    }
    return null;
  }

  protected function _setInRevision($property, $value)
  {
    $this->getNewRevision()->$property = $value;
    return $this;
  }

  protected function _unsetInRevision($property)
  {
    unset($this->getNewRevision()->$property);
    return $this;
  }

  protected function _issetInRevision($property)
  {
    $newRevision = $this->getNewRevision(false);
    if (null !== $newRevision) {
      return isset($newRevision->$property);
    }
    $currentRevision = $this->getCurrentRevision();
    if (null !== $currentRevision) {
      return isset($currentRevision->$property);
    }
    return false;
  }

  public function getActionNavigation($type = 'instance')
  {
    switch ($type) {
      case 'listing' :
        return new Zend_Navigation(array(
          $this->getCreatePage(),
        ));
        break;
      case 'instance' :
      default :
        return new Zend_Navigation(array(
          $this->getIndexPage(),
          $this->getViewPage(),
          $this->getEditPage(),
          $this->getDeletePage(),
        ));
        break;
    }
  }
}
