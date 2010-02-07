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


namespace Sirprize\Basecamp\Todolist\Entity\Observer;


/**
 * Abstract class to observe and print state changes of the observed todolist
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onCreateSuccess(\Sirprize\Basecamp\Todolist\Entity $todolist);
	abstract public function onUpdateSuccess(\Sirprize\Basecamp\Todolist\Entity $todolist);
	abstract public function onDeleteSuccess(\Sirprize\Basecamp\Todolist\Entity $todolist);
	
	abstract public function onCreateError(\Sirprize\Basecamp\Todolist\Entity $todolist);
	abstract public function onUpdateError(\Sirprize\Basecamp\Todolist\Entity $todolist);
	abstract public function onDeleteError(\Sirprize\Basecamp\Todolist\Entity $todolist);
	
	
	
	protected function _getOnCreateSuccessMessage(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		$message  = "todolist '".$todolist->getName()."'";
		$message .= " created in project '".$todolist->getProjectId();
		return $message;
	}
	
	
	protected function _getOnUpdateSuccessMessage(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		$message  = "todolist '".$todolist->getName()."'";
		$message .= " updated in project '".$todolist->getProjectId();
		return $message;
	}
	
	
	protected function _getOnDeleteSuccessMessage(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		$message  = "todolist '".$todolist->getName()."'";
		$message .= " deleted from project '".$todolist->getProjectId();
		return $message;
	}
	
	
	
	
	
	
	
	
	protected function _getOnCreateErrorMessage(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		$message  = "todolist '".$todolist->getName()."'";
		$message .= " could not be created in project '".$todolist->getProjectId();
		return $message;
	}
	
	
	protected function _getOnUpdateErrorMessage(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		$message  = "todolist '".$todolist->getName()."'";
		$message .= " could not be updated in project '".$todolist->getProjectId();
		return $message;
	}
	
	
	protected function _getOnDeleteErrorMessage(\Sirprize\Basecamp\Todolist\Entity $todolist)
	{
		$message  = "todolist '".$todolist->getName()."'";
		$message .= " could not be deleted from project '".$todolist->getProjectId();
		return $message;
	}
	
}