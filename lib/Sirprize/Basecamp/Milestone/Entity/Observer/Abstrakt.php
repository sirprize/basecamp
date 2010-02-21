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


namespace Sirprize\Basecamp\Milestone\Entity\Observer;


/**
 * Abstract class to observe and print state changes of the observed milestone
 *
 * @category  Sirprize
 * @package   Basecamp
 */
abstract class Abstrakt
{
	
	
	abstract public function onCompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onUncompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onUpdateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onDeleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone);
	
	abstract public function onCompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onUncompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onCreateError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onUpdateError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	abstract public function onDeleteError(\Sirprize\Basecamp\Milestone\Entity $milestone);
	
	
	
	protected function _getOnCompleteSuccessMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " completed in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnUncompleteSuccessMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " uncompleted in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnCreateSuccessMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " created in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnUpdateSuccessMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " updated in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnDeleteSuccessMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " deleted from project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	
	
	
	
	
	
	protected function _getOnCompleteErrorMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be completed in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnUncompleteErrorMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be uncompleted in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnCreateErrorMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be created in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnUpdateErrorMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be updated in project '".$milestone->getProjectId()."'";
		return $message;
	}
	
	
	protected function _getOnDeleteErrorMessage(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be deleted from project '".$milestone->getProjectId()."'";
		return $message;
	}
	
}