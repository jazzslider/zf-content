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
 * Action controller for posts
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_PostsController extends Zend_Controller_Action
{
  protected $_postClass = 'Content_Model_Post';

  public function indexAction()
  {
    $this->view->posts = $this->_getMapper()->findPaginator($this->_getIndexSelect());
    $this->view->posts->setCurrentPageNumber($this->_getParam('page', 1));
  }

  protected function _getIndexSelect()
  {
    if ($this->_postClass != 'Content_Model_Post') {
      return $this->_getMapper()->getTable()->select()->where('class = ?', $this->_postClass)->order('published DESC');
    } else {
      return $this->_getMapper()->getTable()->select()->order('published DESC');
    }
  }

  public function viewAction()
  {
    $id = $this->_getParam('id', null);
    if (null === $id || !is_numeric($id)) {
      throw new Zend_Controller_Action_Exception('The requested post could not be found.', 404);
    }
    $post = $this->_getMapper()->find($id);
    if (null === $post) {
      throw new Zend_Controller_Action_Exception('The requested post could not be found.', 404);
    }
    $this->view->post = $post;
  }

  public function editAction()
  {
    $id = $this->_getParam('id', null);
    if (null === $id) {
      $class = $this->_postClass;
      $post = new $class(array(), $this->_getBootstrap());
    } else {
      if (!is_numeric($id)) {
        throw new Zend_Controller_Action_Exception('The requested post could not be found.', 404);
      }
      $post = $this->_getMapper()->find($id);
      if (null === $post) {
        throw new Zend_Controller_Action_Exception('The requested post could not be found.', 404);
      }
    }
    
    $this->view->post = $post;

    $form = $post->getForm();
    $post->populateForm($form);
    $form->setMethod('POST');
    $this->view->post = $post;

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $post->populateFromForm($form);
      $post->save();

      $this->_helper->FlashMessenger->addMessage('Post successfully saved!');
      $this->_helper->Redirector->gotoUrlAndExit($post->getViewPage()->getHref(), array('prependBase' => FALSE));
      return;
    }
  }

  public function deleteAction()
  {
    $id = $this->_getParam('id', null);
    if (null === $id) {
      throw new Zend_Controller_Action_Exception('No post ID was provided.', 404);
    } else {
      if (!is_numeric($id)) {
        throw new Zend_Controller_Action_Exception('The requested post could not be found.', 404);
      }
      $post = $this->_getMapper()->find($id);
      if (null === $post) {
        throw new Zend_Controller_Action_Exception('The requested post could not be found.', 404);
      }
    }

    $this->view->post = $post;
    $form = new Content_Form_DeletePost();
    $this->view->form = $form;

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      if ($form->choice->getValue() == 'Yes') {
        $post->delete();
        $this->_helper->FlashMessenger->addMessage('Post successfully deleted!');
        $this->_helper->Redirector->gotoUrlAndExit($post->getIndexPage()->getHref(), array('prependBase' => FALSE));
      } else {
        $this->_helper->FlashMessenger->addMessage('Post not deleted.');
        $this->_helper->Redirector->gotoUrlAndExit($post->getViewPage()->getHref(), array('prependBase' => FALSE));
      }
    }
  }

  protected function _getBootstrap()
  {
    return $this->getInvokeArg('bootstrap');
  }

  protected function _getMapper()
  {
    $resource = $this->_getBootstrap()->getPluginResource('modules');
    $moduleBootstraps = $resource->getExecutedBootstraps();
    $moduleBootstrap = $moduleBootstraps['content'];
    $moduleBootstrap->bootstrap('postmapper');
    return $moduleBootstrap->getResource('postmapper');
  }
}
