<?php

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
      $this->_helper->Redirector->gotoUrlAndExit($post->getViewPage()->getHref());
      return;
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
