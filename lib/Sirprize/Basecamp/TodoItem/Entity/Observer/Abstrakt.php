<?php

/**
 * Basecamp API Wrapper for PHP 5.3+ 
 *
 * LICENSE
 *
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt
 *
 * @category   Sirprize
 * @package    Basecamp
 * @copyright  Copyright (c) 2010, Christian Hoegl, Switzerland (http://sirprize.me)
 * @license    MIT License
 */


namespace Sirprize\Basecamp\TodoItem\Entity\Observer;


/**
 * Abstract class to observe and print state changes of the observed todo-item
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onCompleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onUncompleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onCreateSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onUpdateSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onDeleteSuccess(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	
	abstract public function onCompleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onUncompleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onCreateError(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onUpdateError(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	abstract public function onDeleteError(\Sirprize\Basecamp\TodoItem\Entity $todoItem);
	
	
	
	protected function _getOnCompleteSuccessMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " completed in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnUncompleteSuccessMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " uncompleted in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnCreateSuccessMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " created in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnUpdateSuccessMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " updated in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnDeleteSuccessMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " deleted from todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	
	
	
	
	
	
	protected function _getOnCompleteErrorMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " could not be completed in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnUncompleteErrorMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " could not be uncompleted in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnCreateErrorMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " could not be created in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnUpdateErrorMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " could not be updated in todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
	
	protected function _getOnDeleteErrorMessage(\Sirprize\Basecamp\TodoItem\Entity $todoItem)
	{
		$message  = "todo-item '".$todoItem->getContent()."'";
		$message .= " could not be deleted from todo-list '".$todoItem->getTodoListId()."'";
		return $message;
	}
	
}