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


require_once 'Sirprize/Basecamp/Milestone/Entity/Observer/Interfaze.php';


/**
 * Class to observe and print state changes of the observed milestone
 *
 * @category  Sirprize
 * @package   Basecamp
 */
class Stout implements \Sirprize\Basecamp\Milestone\Entity\Observer\Interfaze
{
	
	
	public function onCompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " completed in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onUncompleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " uncompleted in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onCreateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " created in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onUpdateSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " updated in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onDeleteSuccess(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " deleted from project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	
	
	
	
	
	
	public function onCompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be completed in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onUncompleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be uncompleted in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onCreateError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be created in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onUpdateError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be updated in project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
	
	public function onDeleteError(\Sirprize\Basecamp\Milestone\Entity $milestone)
	{
		$message  = "milestone '".$milestone->getTitle()."'";
		$message .= " could not be deleted from project '".$milestone->getProjectId()."'\n";
		print $message;
	}
	
}