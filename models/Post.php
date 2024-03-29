<?php
/**
 * Distributable content module for Zend Framework
 *
 * This module is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This module is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <{@link http://www.gnu.org/licenses/}>.
 *
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */

/**
 * Post model
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_Model_Post extends Content_Model_Content_Abstract
                         implements Zend_Acl_Resource_Interface
{
  protected $_formClass = 'Content_Form_Post';

  protected $_revisionClass = 'Content_Model_Revision';
  protected $_currentRevision;
  protected $_newRevision;

  const STATUS_PUBLISHED = 1;
  const STATUS_DRAFT     = 0;

  public function getResourceId()
  {
    if (null !== $this->id) {
      return 'content:post:' . $this->id;
    } else {
      return 'content:post';
    }
  }

  public function preInit()
  {
    $this->_data = array_merge($this->_data, array(
      'id'        => null,
      'slug'      => null,
      'status'    => null,
      'author'    => null,
      'published' => null,
      'revisions' => null,
    ));
  }

  public function getRevisionClass()
  {
    return $this->_revisionClass;
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
        $this->_newRevision->post = $this;
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

  public function setAuthor($author)
  {
    if (null !== $author && !($author instanceof Zend_Acl_Role_Interface)) {
      // The author property needs to be a Zend ACL Role for consistency,
      // but we don't want to limit it to just Zend_Acl_Role; instead,
      // allow the plugins a shot at defining it.  That way, an application
      // or other module can provide its own user model system that would
      // be fully compatible with this system.
      foreach ($this->getPlugins() as $plugin) {
        $modelClass = $plugin->getModelClass();
        if ($this instanceof $modelClass) {
          $author = $plugin->loadAuthorRole($author);
          if ($author instanceof Zend_Acl_Role_Interface) {
            break;
          }
        }
      }
      if (!($author instanceof Zend_Acl_Role_Interface)) {
        // None of the registered plugins could convert the spec
        // to a role object, so we'll fall back on Zend_Acl_Role.
        $author = new Zend_Acl_Role($author);
      }
    }
    $this->_data['author'] = $author;
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

  public function getIndexPage()
  {
    return new Zend_Navigation_Page_Mvc(array(
      'label'      => 'Index',
      'module'     => 'content',
      'controller' => 'posts',
      'action'     => 'index',
      'params'     => array(
        'id' => null,
      ),
    ));
  }

  public function getViewPage()
  {
    return new Zend_Navigation_Page_Mvc(array(
      'label'      => 'View',
      'module'     => 'content',
      'controller' => 'posts',
      'action'     => 'view',
      'params'     => array(
        'id' => $this->id,
      ),
      'resource'   => $this,
      'privilege'  => 'view',
    ));
  }

  public function getEditPage()
  {
    return new Zend_Navigation_Page_Mvc(array(
      'label'      => 'Edit',
      'module'     => 'content',
      'controller' => 'posts',
      'action'     => 'edit',
      'params'     => array(
        'id' => $this->id,
      ),
      'resource'   => $this,
      'privilege'  => 'edit',
    ));
  }

  public function getDeletePage()
  {
    return new Zend_Navigation_Page_Mvc(array(
      'label'      => 'Delete',
      'module'     => 'content',
      'controller' => 'posts',
      'action'     => 'delete',
      'params'     => array(
        'id' => $this->id,
      ),
      'resource'   => $this,
      'privilege'  => 'delete',
    ));
  }

  public function save()
  {
    $mapper = $this->getPostMapper();
    $mapper->save($this);
    return $this;
  }

  public function delete()
  {
    $mapper = $this->getPostMapper();
    $mapper->delete($this);
    return $this;
  }
}
