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


namespace Sirprize\Basecamp\TodoList\Entity\Observer;


/**
 * Abstract class to observe and print state changes of the observed todoList
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onCreateSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList);
	abstract public function onUpdateSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList);
	abstract public function onDeleteSuccess(\Sirprize\Basecamp\TodoList\Entity $todoList);
	
	abstract public function onCreateError(\Sirprize\Basecamp\TodoList\Entity $todoList);
	abstract public function onUpdateError(\Sirprize\Basecamp\TodoList\Entity $todoList);
	abstract public function onDeleteError(\Sirprize\Basecamp\TodoList\Entity $todoList);
	
	
	
	protected function _getOnCreateSuccessMessage(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$message  = "todoList '".$todoList->getName()."'";
		$message .= " created in project '".$todoList->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnUpdateSuccessMessage(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$message  = "todoList '".$todoList->getName()."'";
		$message .= " updated in project '".$todoList->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnDeleteSuccessMessage(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$message  = "todoList '".$todoList->getName()."'";
		$message .= " deleted from project '".$todoList->getProjectId()."'";
		return $message;
	}
	
	
	
	
	
	
	
	
	protected function _getOnCreateErrorMessage(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$message  = "todoList '".$todoList->getName()."'";
		$message .= " could not be created in project '".$todoList->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnUpdateErrorMessage(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$message  = "todoList '".$todoList->getName()."'";
		$message .= " could not be updated in project '".$todoList->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnDeleteErrorMessage(\Sirprize\Basecamp\TodoList\Entity $todoList)
	{
		$message  = "todoList '".$todoList->getName()."'";
		$message .= " could not be deleted from project '".$todoList->getProjectId()."'";
		return $message;
	}
	
}