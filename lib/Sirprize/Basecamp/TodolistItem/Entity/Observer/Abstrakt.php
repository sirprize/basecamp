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


namespace Sirprize\Basecamp\TodolistItem\Entity\Observer;


/**
 * Abstract class to observe and print state changes of the observed todo-item
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onCompleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onUncompleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onCreateSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onUpdateSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onDeleteSuccess(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	
	abstract public function onCompleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onUncompleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onCreateError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onUpdateError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	abstract public function onDeleteError(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem);
	
	
	
	protected function _getOnCompleteSuccessMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " completed in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnUncompleteSuccessMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " uncompleted in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnCreateSuccessMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " created in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnUpdateSuccessMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " updated in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnDeleteSuccessMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " deleted from todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	
	
	
	
	
	
	protected function _getOnCompleteErrorMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " could not be completed in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnUncompleteErrorMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " could not be uncompleted in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnCreateErrorMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " could not be created in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnUpdateErrorMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " could not be updated in todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
	
	protected function _getOnDeleteErrorMessage(\Sirprize\Basecamp\TodolistItem\Entity $todolistItem)
	{
		$message  = "todo-item '".$todolistItem->getContent()."'";
		$message .= " could not be deleted from todo-list '".$todolistItem->getTodolistId();
		return $message;
	}
	
}