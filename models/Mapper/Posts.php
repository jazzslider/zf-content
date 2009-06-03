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
 * Data mapper for post models
 * @author     Adam Jensen <jazzslider@gmail.com>
 * @copyright  Copyright (c) 2009 Adam Jensen
 * @license    http://www.gnu.org/licenses/gpl.txt GNU Public License
 */
class Content_Model_Mapper_Posts extends Content_Model_Mapper_DbTable_Abstract
{
  protected $_modelClass = 'Content_Model_Post';
  protected $_tableClass = 'Content_Model_DbTable_Posts';

  public function save(Content_Model_Content_Interface $post)
  {
    if (!($post instanceof Content_Model_Post)) {
      throw new Exception('can only save posts via this mapper');
    }

    $this->_preSave($post);

    if (null === $post->id) {
      $postRow = $this->getTable()->createRow();
    } else {
      $postRow = $this->findRow($post->id);
    }

    $postRow->class = get_class($post);
    if (trim($post->slug) <> '') {
      $postRow->slug = $post->slug;
    } else {
      $postRow->slug = null;
    }
    if (0 !== $post->status && trim($post->status) <> '') {
      $postRow->status = $post->status;
    } else {
      $postRow->status = Content_Model_Post::STATUS_PUBLISHED;
    }
    if (null === $post->author) {
      $auth = Zend_Auth::getInstance();
      if ($auth->hasIdentity()) {
        $identity = $auth->getIdentity();
        if ($identity instanceof Zend_Acl_Role || (is_string($identity) && trim($identity) <> '')) {
          $post->author = $auth->getIdentity();
        }
      }
    }
    if (null !== $post->author) {
      $postRow->author = $post->author->getRoleId();
    } else {
      $postRow->author = null;
    }
    if (null === $post->published) {
      $post->published = new Zend_Date();
    }
    $postRow->published = $post->published->get(Zend_Date::ISO_8601);

    $postRow->save();
    $post->id = $postRow->id;

    $revision = $post->getNewRevision(false);
    if (null !== $revision) {
      $revision->save();
    }

    $this->_postSave($post);

    $this->_models[$post->id] = $post;
    $this->_rows[$postRow->id] = $postRow;

    return $this->_models[$post->id];
  }

  public function getSelect()
  {
    $select = $ths->getTable()->select();
    $select->from(array('p' => 'content_posts'));
    return $select;
  }
}
